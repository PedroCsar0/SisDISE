<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\GrupoParametro;
use App\Models\ItemParametro;
use App\Models\Diagnostico;
use App\Models\Principio;
use App\Models\ParametroAvaliacao;
use App\Models\Empresa;
use App\Models\DiagnosticoItem;
use Barryvdh\DomPDF\Facade\Pdf;

class DiagnosticoController extends Controller
{
    /**
     * Rota GET /api/questionario
     */
    public function getQuestionario()
    {
        $questionario = GrupoParametro::with('itemParametros')->get();
        return response()->json($questionario);
    }

    /**
     * Rota GET /api/diagnosticos
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->tipo === 'Gestor Empresarial') {
            if (!$user->empresa) {
                return response()->json([], 200);
            }
            return $user->empresa->diagnosticos()->with('empresa')->latest()->get();
        }

        if ($user->tipo === 'Administrador') {
             return \App\Models\Diagnostico::with('empresa')->latest()->get();
        }

        $empresasDoAvaliador = $user->empresasCriadas()->pluck('id');
        return \App\Models\Diagnostico::whereIn('empresa_id', $empresasDoAvaliador)
            ->with('empresa')
            ->latest()
            ->get();
    }


    /**
     * Rota POST /api/diagnosticos
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
            'titulo'     => 'required|string|max:255',
            'respostas'  => 'required|array|min:39',
            'respostas.*.item_parametro_id' => 'required|integer|exists:item_parametros,id',
            'respostas.*.nota' => 'required|integer|min:0|max:5',
        ]);

        $gruposTemplate = GrupoParametro::all();
        $itensPorGrupo = ItemParametro::all()->groupBy('grupo_parametro_id');
        $respostasMap = collect($dadosValidados['respostas'])->pluck('nota', 'item_parametro_id');

        $diagnosticoCompleto = DB::transaction(function () use ($dadosValidados, $gruposTemplate, $itensPorGrupo, $respostasMap) {

            $user = Auth::user();

            $diagnostico = Diagnostico::create([
                'user_id'       => $user->id,
                'empresa_id'    => $dadosValidados['empresa_id'],
                'titulo'        => $dadosValidados['titulo'],
                'dataAnalise'   => now(),
                'escoreFinal'   => 0,
                'classificacao' => 'Processando...',
            ]);

            $principioSdt = $diagnostico->principios()->create(['nomePrincipio' => 'Direitos humanos e trabalho', 'escoreObtido' => 0]);
            $principioSma = $diagnostico->principios()->create(['nomePrincipio' => 'Meio ambiente', 'escoreObtido' => 0]);
            $principioSac = $diagnostico->principios()->create(['nomePrincipio' => 'Anticorrupção', 'escoreObtido' => 0]);

            $mapPrincipioGrupo = [
                'Sdt1' => $principioSdt, 'Sdt2' => $principioSdt,
                'Sma1' => $principioSma, 'Sma2' => $principioSma, 'Sma3' => $principioSma,
                'Sac1' => $principioSac,
            ];

            foreach ($gruposTemplate as $grupo) {

                $pi = $grupo->peso;
                $nmax = $grupo->nota_maxima_grupo;
                $somaNotasGrupo_Eni = 0;

                $itensDesteGrupo = $itensPorGrupo->get($grupo->id) ?? collect();

                foreach ($itensDesteGrupo as $item) {
                    $notaItem = $respostasMap->get($item->id) ?? 0;
                    $somaNotasGrupo_Eni += $notaItem;

                    $diagnostico->itens()->create([
                        'item_parametro_id' => $item->id,
                        'nota' => $notaItem
                    ]);
                }

                $escoreGrupo_si = 0;
                if ($nmax > 0) {
                    $escoreGrupo_si = 100 * ($somaNotasGrupo_Eni / $nmax) * $pi;
                }

                $principioPai = $mapPrincipioGrupo[$grupo->codigo];

                // --- ESTA É A CORREÇÃO CRÍTICA (que causa o bug do "0") ---
                $principioPai->parametroAvaliacaos()->create([
                    'descricao'     => $grupo->nome,
                    'nota'          => $somaNotasGrupo_Eni,
                    'peso'          => $pi,
                    'escore_obtido' => $escoreGrupo_si, // <-- CORRIGIDO (Salva o Escore, ex: 120)
                ]);
                // --- FIM DA CORREÇÃO ---

                $principioPai->escoreObtido += $escoreGrupo_si;
            }

            $principioSdt->save();
            $principioSma->save();
            $principioSac->save();

            $escoreFinal_Sf = $principioSdt->escoreObtido + $principioSma->escoreObtido + $principioSac->escoreObtido;
            $classificacao = $this->getClassificacao($escoreFinal_Sf);

            $diagnostico->update([
                'escoreFinal'   => $escoreFinal_Sf,
                'classificacao' => $classificacao,
            ]);

            return $diagnostico->load('empresa', 'principios.parametroAvaliacaos');
        });

        return response()->json($diagnosticoCompleto, 201);
    }


    /**
     * Rota GET /api/diagnosticos/{diagnostico}
     */
    public function show(Diagnostico $diagnostico)
    {
        return $diagnostico->load('empresa', 'principios.parametroAvaliacaos');
    }

    /**
     * Função auxiliar para determinar a classificação
     */
    private function getClassificacao(float $escore): string
    {
        if ($escore <= 250) { return "Sustentabilidade inexistente"; }
        if ($escore <= 500) { return "Baixa sustentabilidade"; }
        if ($escore <= 750) { return "Sustentabilidade moderada"; }
        return "Sustentável";
    }

    /**
     * Rota GET /api/relatorio/{diagnostico}/pdf
     */
    public function downloadPDF(Diagnostico $diagnostico)
    {
        $diagnostico->load('principios', 'empresa');
        $pdf = Pdf::loadView('pdf.relatorio', [
            'diagnostico' => $diagnostico
        ]);
        $fileName = 'SisDISE_Relatorio_' . $diagnostico->titulo . '.pdf';
        return $pdf->stream($fileName);
    }

    /**
     * Rota GET /api/diagnosticos/{diagnostico}/pges
     */
    public function gerarPGES(Diagnostico $diagnostico)
    {
        $itensComNotas = $diagnostico->itens()->with('itemParametro')->get();
        $itensParaMelhorar = $itensComNotas->filter(function ($item) {
            return $item->nota <= 2;
        });
        return response()->json($itensParaMelhorar->values());
    }

    /**
     * Rota GET /api/diagnosticos/{diagnostico}/pges/pdf
     */
    public function downloadPGES(Diagnostico $diagnostico)
    {
        $diagnostico->load('empresa');
        $itensComNotas = $diagnostico->itens()->with('itemParametro')->get();
        $itensParaMelhorar = $itensComNotas->filter(function ($item) {
            return $item->nota <= 2;
        });

        $pdf = Pdf::loadView('pdf.pges', [
            'diagnostico' => $diagnostico,
            'itensParaMelhorar' => $itensParaMelhorar->values()
        ]);

        $fileName = 'SisDISE_PlanoDeAcao_' . $diagnostico->id . '.pdf';
        return $pdf->stream($fileName);
    }
}
