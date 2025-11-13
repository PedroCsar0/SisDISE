<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\GrupoParametro; // Template dos 6 Grupos (Sdt1, Sdt2...)
use App\Models\ItemParametro;  // Template dos 39 Itens (1.1.1, 1.1.2...)
use App\Models\Diagnostico;
use App\Models\Principio;
use App\Models\ParametroAvaliacao; // Tabela para salvar os 6 resultados (Sdt1, Sdt2...)
use App\Models\Empresa;
use App\Models\DiagnosticoItem;
use Barryvdh\DomPDF\Facade\Pdf;

class DiagnosticoController extends Controller
{
    /**
     * Rota GET /api/questionario
     * Busca os 6 Grupos e seus 39 Itens (perguntas) para montar o questionário no Vue.
     */
    public function getQuestionario()
    {
        // Carrega os 6 Grupos (Sdt1...) e, para cada um, carrega seus Itens (1.1.1...)
        $questionario = GrupoParametro::with('itemParametros')->get();

        return response()->json($questionario);
    }

    /**
     * Rota POST /api/diagnosticos
     * Recebe as 39 respostas, calcula os escores e salva o diagnóstico.
     * Esta é a lógica central do SisDISE, baseada no Manual Técnico.
     */
    public function store(Request $request)
    {
        // 1. VALIDAÇÃO DOS DADOS
        $dadosValidados = $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
            'respostas'  => 'required|array|min:39', // Total de 39 respostas
            'respostas.*.item_parametro_id' => 'required|integer|exists:item_parametros,id',
            'respostas.*.nota' => 'required|integer|min:0|max:5', // Tabela 3 [cite: 129]
        ]);

        // 2. PREPARAÇÃO DOS DADOS

        // Carrega os 6 Grupos (Sdt1, Sma1...) com seus pesos (pi) e Nmax
        $gruposTemplate = GrupoParametro::all()->keyBy('id');

        // Carrega os 39 Itens e agrupa-os por 'grupo_parametro_id'
        // Teremos um mapa: { 1 (Sdt1) => [item 1, item 2...], 2 (Sdt2) => [item 15...] }
        $itensPorGrupo = ItemParametro::all()->groupBy('grupo_parametro_id');

        // Converte as respostas de [ {id: 1, nota: 3}, ... ] para [ 1 => 3, ... ]
        // Facilita a busca da nota (ni) para cada item
        $respostasMap = collect($dadosValidados['respostas'])->pluck('nota', 'item_parametro_id');

        // 3. LÓGICA DE CÁLCULO (DENTRO DE UMA TRANSAÇÃO)
        $diagnosticoCompleto = DB::transaction(function () use ($dadosValidados, $gruposTemplate, $itensPorGrupo, $respostasMap) {

            $user = Auth::user();
            $empresa = Empresa::find($dadosValidados['empresa_id']);

            // 3a. Cria o Diagnóstico "pai" (Etapa 4)
            $diagnostico = Diagnostico::create([
                'user_id'       => $user->id,
                'empresa_id'    => $empresa->id,
                'dataAnalise'   => now(),
                'escoreFinal'   => 0, // Será atualizado
                'classificacao' => 'Processando...',
            ]);

            // 3b. Cria os 3 Princípios "filhos" (Etapa 3)
            $principioSdt = $diagnostico->principios()->create(['nomePrincipio' => 'Direitos humanos e trabalho', 'escoreObtido' => 0]);
            $principioSma = $diagnostico->principios()->create(['nomePrincipio' => 'Meio ambiente', 'escoreObtido' => 0]);
            $principioSac = $diagnostico->principios()->create(['nomePrincipio' => 'Anticorrupção', 'escoreObtido' => 0]);

            // Map para saber a qual princípio (Sdt, Sma, Sac) cada grupo (Sdt1, Sdt2...) pertence
            $mapPrincipioGrupo = [
                'Sdt1' => $principioSdt,
                'Sdt2' => $principioSdt,
                'Sma1' => $principioSma,
                'Sma2' => $principioSma,
                'Sma3' => $principioSma,
                'Sac1' => $principioSac,
            ];

            // 3c. Itera sobre os 6 Grupos de Parâmetros (Etapa 2)
            foreach ($gruposTemplate as $grupo) {

                // Pega os dados deste grupo (pi e Nmax) da Tabela 4 [cite: 149]
                $pi = $grupo->peso;
                $nmax = $grupo->nota_maxima_grupo;
                $somaNotasGrupo_Eni = 0; // O "Σni" da Tabela 4

                // Pega todos os itens (perguntas) que pertencem a este grupo
                $itensDesteGrupo = $itensPorGrupo->get($grupo->id) ?? collect();

                // Soma as notas (ni) das respostas do usuário para este grupo
                foreach ($itensDesteGrupo as $item) {
                    $notaItem = $respostasMap->get($item->id) ?? 0;
                    $somaNotasGrupo_Eni += $notaItem;
                    $diagnostico->itens()->create([
                        'item_parametro_id' => $item->id,
                        'nota' => $notaItem
                    ]);
                }

                // ** A MÁGICA: Equação 01 **
                $escoreGrupo_si = 0;
                if ($nmax > 0) {
                    $escoreGrupo_si = 100 * ($somaNotasGrupo_Eni / $nmax) * $pi;
                }

                // 3d. Salva o resultado deste Grupo (Sdt1, Sdt2...) na tabela 'parametro_avaliacaos'
                // e associa ao Princípio correto (Sdt, Sma, Sac)
                $principioPai = $mapPrincipioGrupo[$grupo->codigo];
                $principioPai->parametroAvaliacaos()->create([
                    'descricao'     => $grupo->nome,
                    'nota'          => $somaNotasGrupo_Eni, // Armazenamos o Σni
                    'peso'          => $pi,
                    'escore_obtido' => $escoreGrupo_si,
                ]);

                // 3e. Acumula o escore no Princípio "pai" (Equações 02, 03, 04)
                $principioPai->escoreObtido += $escoreGrupo_si;
            }

            // 3f. Salva os escores finais dos 3 Princípios
            $principioSdt->save();
            $principioSma->save();
            $principioSac->save();

            // 3g. Calcula o Escore Final (Equação 05)
            $escoreFinal_Sf = $principioSdt->escoreObtido + $principioSma->escoreObtido + $principioSac->escoreObtido;

            // 3h. Determina a Classificação (Tabela 6) [cite: 173]
            $classificacao = $this->getClassificacao($escoreFinal_Sf);

            // 3i. Atualiza o Diagnóstico "pai" com o resultado final
            $diagnostico->update([
                'escoreFinal'   => $escoreFinal_Sf,
                'classificacao' => $classificacao,
            ]);

            // Recarrega o diagnóstico com todos os seus "filhos" (Princípios e Parâmetros)
            return $diagnostico->load('principios.parametroAvaliacaos');
        });

        // 4. ENVIA A RESPOSTA
        return response()->json($diagnosticoCompleto, 201); // 201 = "Created"
    }


    /**
     * Função auxiliar para determinar a classificação baseada no escore.
     * Tabela 6 do Manual Técnico [cite: 173]
     */
    private function getClassificacao(float $escore): string
    {
        if ($escore <= 250) {
            return "Sustentabilidade inexistente";
        }
        if ($escore <= 500) {
            return "Baixa sustentabilidade";
        }
        if ($escore <= 750) {
            // Estudo de caso (532) cai aqui [cite: 192]
            return "Sustentabilidade moderada";
        }
        return "Sustentável";
    }

    public function show(\App\Models\Diagnostico $diagnostico)
    {
        // O "Route Model Binding" do Laravel já encontra o diagnóstico pelo ID.
        // Apenas carregamos os "filhos" (princípios e parâmetros) e o retornamos.

        // Opcional: Adicionar verificação de segurança
        // if ($diagnostico->user_id !== auth()->id()) {
        //     return response()->json(['message' => 'Não autorizado'], 403);
        // }

        return $diagnostico->load('principios.parametroAvaliacaos');
    }

    public function index(Request $request)
    {
        // Busca todos os diagnósticos
        // pertencentes ao usuário que está fazendo a requisição
        $diagnosticos = $request->user()->diagnosticos()
            ->with('empresa') // Carrega os dados da empresa (útil em breve)
            ->latest()        // Ordena pelos mais recentes primeiro
            ->get();

        return $diagnosticos;
    }

    public function downloadPDF(\App\Models\Diagnostico $diagnostico)
    {
        // 1. (Opcional) Verificação de Segurança:
        if ($diagnostico->user_id !== auth()->id()) {
        return response()->json(['message' => 'Não autorizado'], 403);
        }

        // 2. Carrega os dados (relacionamentos)
        $diagnostico->load('principios');

        // 3. Gera o PDF usando o "molde" Blade que criamos
        $pdf = Pdf::loadView('pdf.relatorio', [
            'diagnostico' => $diagnostico
        ]);

        // 4. Define o nome do arquivo e o envia para o navegador
        $fileName = 'SisDISE_Relatorio_' . $diagnostico->id . '.pdf';

        // stream() envia o PDF para o navegador sem salvar no servidor
        return $pdf->stream($fileName);
    }

    public function gerarPGES(\App\Models\Diagnostico $diagnostico)
{
    // 1. (Opcional) Verificação de Segurança:
    if ($diagnostico->user_id !== auth()->id()) {
       return response()->json(['message' => 'Não autorizado'], 403);
    }

    // 2. A Lógica Principal do PGES
    // Carrega todos os 39 itens (respostas) deste diagnóstico
    // e, para cada item, carrega sua "pergunta" (itemParametro).
    $itensComNotas = $diagnostico->itens()->with('itemParametro')->get();

    // 3. Filtra apenas os itens com "nota baixa" (<= 2)
    $itensParaMelhorar = $itensComNotas->filter(function ($item) {
        return $item->nota <= 2;
    });

    // 4. Retorna a lista de itens a melhorar
    return response()->json($itensParaMelhorar->values());
}
}
