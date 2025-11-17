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
use Barryvdh\DomPDF\Facade\Pdf; // Importe o PDF

class DiagnosticoController extends Controller
{
    /**
     * Rota GET /api/questionario
     * Busca os 6 Grupos e seus 39 Itens (perguntas) para montar o questionário no Vue.
     */
    public function getQuestionario()
    {
        $questionario = GrupoParametro::with('itemParametros')->get();
        return response()->json($questionario);
    }

    /**
     * Rota GET /api/diagnosticos
     * Lista diagnósticos baseado no tipo de usuário.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Se for Gestor Empresarial
        if ($user->tipo === 'Gestor Empresarial') {
            if (!$user->empresa) {
                return response()->json([], 200); // Retorna vazio se o gestor não tem empresa
            }
            return $user->empresa
                ->diagnosticos()
                ->with('empresa')
                ->latest()
                ->get();
        }

        // Se for Admin
        if ($user->tipo === 'Administrador') {
             return \App\Models\Diagnostico::with('empresa')
                ->latest()
                ->get();
        }

        // Se for Avaliador, mostra apenas os das suas empresas
        $empresasDoAvaliador = $user->empresasCriadas()->pluck('id');
        return \App\Models\Diagnostico::whereIn('empresa_id', $empresasDoAvaliador)
            ->with('empresa')
            ->latest()
            ->get();
    }


    /**
     * Rota POST /api/diagnosticos
     * Recebe as 39 respostas, calcula os escores e salva o diagnóstico.
     */
    public function store(Request $request)
    {
        // 1. VALIDAÇÃO (Corrigida para incluir 'titulo')
        $dadosValidados = $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
            'titulo'     => 'required|string|max:255', // <-- CORRIGIDO
            'respostas'  => 'required|array|min:39',
            'respostas.*.item_parametro_id' => 'required|integer|exists:item_parametros,id',
            'respostas.*.nota' => 'required|integer|min:0|max:5',
        ]);

        // 2. PREPARAÇÃO DOS DADOS
        $gruposTemplate = GrupoParametro::all();
        $itensPorGrupo = ItemParametro::all()->groupBy('grupo_parametro_id');
        $respostasMap = collect($dadosValidados['respostas'])->pluck('nota', 'item_parametro_id');

        // 3. LÓGICA DE CÁLCULO
        $diagnosticoCompleto = DB::transaction(function () use ($dadosValidados, $gruposTemplate, $itensPorGrupo, $respostasMap) {

            $user = Auth::user();

            // 3a. Cria o Diagnóstico "pai" (Corrigido para incluir 'titulo')
            $diagnostico = Diagnostico::create([
                'user_id'       => $user->id,
                'empresa_id'    => $dadosValidados['empresa_id'],
                'titulo'        => $dadosValidados['titulo'], // <-- CORRIGIDO
                'dataAnalise'   => now(),
                'escoreFinal'   => 0,
                'classificacao' => 'Processando...',
            ]);

            // 3b. Cria os 3 Princípios "filhos"
            $principioSdt = $diagnostico->principios()->create(['nomePrincipio' => 'Direitos humanos e trabalho', 'escoreObtido' => 0]);
            $principioSma = $diagnostico->principios()->create(['nomePrincipio' => 'Meio ambiente', 'escoreObtido' => 0]);
            $principioSac = $diagnostico->principios()->create(['nomePrincipio' => 'Anticorrupção', 'escoreObtido' => 0]);

            $mapPrincipioGrupo = [
                'Sdt1' => $principioSdt, 'Sdt2' => $principioSdt,
                'Sma1' => $principioSma, 'Sma2' => $principioSma, 'Sma3' => $principioSma,
                'Sac1' => $principioSac,
            ];

            // 3c. Itera sobre os 6 Grupos de Parâmetros
            foreach ($gruposTemplate as $grupo) {

                $pi = $grupo->peso; // <-- Busca o peso correto (ex: 2.0)
                $nmax = $grupo->nota_maxima_grupo;
                $somaNotasGrupo_Eni = 0;

                $itensDesteGrupo = $itensPorGrupo->get($grupo->id) ?? collect();

                // Soma as notas (ni) E salva os 39 itens individuais
                foreach ($itensDesteGrupo as $item) {
                    $notaItem = $respostasMap->get($item->id) ?? 0;
                    $somaNotasGrupo_Eni += $notaItem;

                    $diagnostico->itens()->create([
                        'item_parametro_id' => $item->id,
                        'nota' => $notaItem
                    ]);
                }

                // ** A MÁGICA: Equação 01 (do PDF) **
                $escoreGrupo_si = 0;
                if ($nmax > 0) {
                    // Ex: 100 * (42 / 70) * 2.0 = 120
                    $escoreGrupo_si = 100 * ($somaNotasGrupo_Eni / $nmax) * $pi;
                }

                // 3d. Salva o resultado deste Grupo (Corrigido para salvar o Escore Ponderado)
                $principioPai = $mapPrincipioGrupo[$grupo->codigo];

                // --- ESTA É A CORREÇÃO CRÍTICA (que causa o bug do "0") ---
                $principioPai->parametroAvaliacaos()->create([
                    'descricao'     => $grupo->nome,
                    'nota'          => $somaNotasGrupo_Eni,
                    'peso'          => $pi,
                    'escore_obtido' => $escoreGrupo_si, // <-- CORRIGIDO (Salva o Escore, ex: 120)
                ]);
                // --- FIM DA CORREÇÃO ---

                // 3e. Acumula o escore no Princípio "pai"
                $principioPai->escoreObtido += $escoreGrupo_si;
            }

            // 3f. Salva os escores finais dos 3 Princípios
            $principioSdt->save();
            $principioSma->save();
            $principioSac->save();

            // 3g. Calcula o Escore Final
            $escoreFinal_Sf = $principioSdt->escoreObtido + $principioSma->escoreObtido + $principioSac->escoreObtido;

            // 3h. Determina a Classificação
            $classificacao = $this->getClassificacao($escoreFinal_Sf);

            // 3i. Atualiza o Diagnóstico "pai" com o resultado final
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
     * Mostra um único diagnóstico salvo (Corrigido para carregar empresa).
     */
    public function show(Diagnostico $diagnostico)
    {
        // Carrega a empresa E os princípios/parâmetros (para o modal)
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
     * Gera e baixa o relatório em PDF.
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
     * Gera o Plano de Gestão Empresarial Sustentável (PGES).
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
     * Gera e baixa o PDF do Plano de Ação (PGES).
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
