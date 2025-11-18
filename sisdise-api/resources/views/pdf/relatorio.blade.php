<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório SisDISE: {{ $diagnostico->titulo }}</title>
    <style>
        /* --- Configuração Global --- */
        @page { margin: 2.5cm 1.5cm 2.5cm 1.5cm; }
        body { font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; line-height: 1.5; color: #333; font-size: 11pt; }
        .container { width: 100%; margin: 0 auto; }
        .page-break { page-break-after: always; }

        /* --- Cabeçalho e Rodapé --- */
        header { position: fixed; top: -2cm; left: 0px; right: 0px; height: 50px; text-align: right; font-size: 9pt; color: #777; border-bottom: 1px solid #ddd; }
        footer { position: fixed; bottom: -2cm; left: 0px; right: 0px; height: 50px; font-size: 9pt; color: #777; border-top: 1px solid #ddd; }
        footer .page-number:after { content: counter(page); }

        /* --- Tipografia --- */
        h1 { font-size: 24px; color: #111; margin-bottom: 20px; border-bottom: 2px solid #36a2eb; padding-bottom: 5px; }
        h2 { font-size: 18px; color: #333; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        p { margin-bottom: 15px; text-align: justify; }
        ul, ol { padding-left: 25px; margin-bottom: 15px; }

        /* --- Capa --- */
        .cover-page { text-align: center; margin-top: 150px; page-break-after: always; } /* Adiciona quebra após a capa */
        .cover-title { font-size: 28px; font-weight: bold; color: #1f2937; margin: 0; border: none; }
        .cover-subtitle { font-size: 20px; color: #4b5563; margin-top: 10px; }
        .cover-info { margin-top: 150px; font-size: 14px; color: #374151; }

        /* --- Componentes --- */
        .card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 25px; margin-bottom: 25px; }
        .score-final-card { text-align: center; }
        .score-final { font-size: 64px; font-weight: bold; color: #111827; margin: 10px 0; }
        .classificacao { font-size: 24px; font-weight: bold; }

        /* Cores Intuitivas */
        .text-baixa { color: #dc2626; }
        .text-moderada { color: #f59e0b; }
        .text-alta { color: #16a34a; }
        .bg-baixa { background-color: #dc2626; }
        .bg-moderada { background-color: #f59e0b; }
        .bg-alta { background-color: #16a34a; }

        /* --- Tabelas --- */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background-color: #f9fafb; font-size: 10pt; color: #6b7280; text-transform: uppercase; }
        td { font-size: 11pt; }
        .text-right { text-align: right; }

        /* Cores dos 3 Pilares */
        .color-sdt { color: #166534; } .color-sma { color: #1d4ed8; } .color-sac { color: #b91c1c; }

        /* --- (REGRAS CORRIGIDAS) CONTROLO DE QUEBRA DE PÁGINA --- */
        .section-block {
            page-break-inside: avoid; /* Esta é a regra principal */
        }
        table, tr, .card {
            page-break-inside: avoid;
        }
        /* Removemos 'page-break-after' dos h1, h2, h3 pois é inseguro */
        p, li, ul, ol {
            widows: 3; /* Mínimo 3 linhas de um parágrafo no final da página */
            orphans: 3; /* Mínimo 3 linhas de um parágrafo no início da página */
        }
        /* --- FIM DAS REGRAS CORRIGIDAS --- */

    </style>
</head>
<body>

    @php
        // Definição de Variáveis PHP (Metadados)
        $metadadosPrincipio = [
            'Direitos humanos e trabalho' => ['max' => 300, 'cor' => 'color-sdt'],
            'Meio ambiente' => ['max' => 500, 'cor' => 'color-sma'],
            'Anticorrupção' => ['max' => 200, 'cor' => 'color-sac'],
        ];

        $metadadosParametro = [
            'Proteção dos direitos humanos e trabalho' => ['codigo' => 'Sdt1', 'nmax' => 70],
            'Abusos aos direitos humanos' => ['codigo' => 'Sdt2', 'nmax' => 30],
            'Ações preventivas aos desafios ambientais' => ['codigo' => 'Sma1', 'nmax' => 20],
            'Iniciativas de responsabilidade ambiental' => ['codigo' => 'Sma2', 'nmax' => 25],
            'Estímulo ao desenvolvimento e a difusão de tecnologias ecologicamente corretas' => ['codigo' => 'Sma3', 'nmax' => 30],
            'Ações de combate a corrupção' => ['codigo' => 'Sac1', 'nmax' => 20],
        ];

        $score = round($diagnostico->escoreFinal);
        $percentual = $score / 10;
        $corClass = 'text-baixa'; $corBgClass = 'bg-baixa';
        if ($score > 750) { $corClass = 'text-alta'; $corBgClass = 'bg-alta'; }
        elseif ($score > 500) { $corClass = 'text-moderada'; $corBgClass = 'bg-moderada'; }
    @endphp

    <div class="cover-page">
        <h1 class="cover-title">{{ $diagnostico->titulo }}</h1>
        <h2 class="cover-subtitle">Relatório de Diagnóstico de Sustentabilidade (ESG)</h2>

        <div class="cover-info">
            <p><strong>Empresa Avaliada:</strong> {{ $diagnostico->empresa ? $diagnostico->empresa->razaoSocial : '[Empresa não disponível]' }}</p>
            <p><strong>Data do Diagnóstico:</strong> {{ \Carbon\Carbon::parse($diagnostico->dataAnalise)->format('d/m/Y') }}</p>
            <p><strong>Metodologia:</strong> SisDISE (Baseado no Pacto Global da ONU)</p>
        </div>
    </div>
    <header>
        Relatório SisDISE: {{ $diagnostico->titulo }} | {{ $diagnostico->empresa ? $diagnostico->empresa->razaoSocial : '' }}
    </header>
    <footer>
        Relatório confidencial gerado pelo SisDISE em {{ \Carbon\Carbon::now()->format('d/m/Y') }}.
        <span class="page-number" style="float: right;">Página </span>
    </footer>

    <div class="container">

        <div class="section-block">
            <h1>1. Sumário Executivo</h1>

            <div class="card score-final-card">
                <h2 style="margin-top: 0;">Escore Final de Sustentabilidade (Sf)</h2>
                <div class="score {{ $corClass }}">{{ $score }} <span style="font-size: 24px; color: #6b7280;">/ 1000</span></div>

                <div style="width: 100%; background-color: #e5e7eb; border-radius: 8px; height: 20px;">
                    <div class="{{ $corBgClass }}" style="height: 20px; width: {{ $percentual }}%; border-radius: 8px;"></div>
                </div>

                <p class="classificacao {{ $corClass }}" style="margin-top: 15px;">
                    Classificação: {{ $diagnostico->classificacao }}
                </p>
            </div>

            <p>
                Com base na metodologia SisDISE, o diagnóstico da empresa resultou num Escore Final de <strong>{{ $score }}</strong> (de 1.000 pontos possíveis),
                situando a organização na categoria de <strong>"{{ $diagnostico->classificacao }}"</strong>.
            </p>
            <p>
                Este relatório detalha a pontuação obtida em cada um dos três pilares fundamentais de ESG e, subsequentemente, decompõe o cálculo
                nos seis parâmetros técnicos de sustentabilidade que formam o escore.
            </p>
        </div>

        <div class="section-block">
            <h1>2. Metodologia</h1>
            <p>
                A análise foi conduzida utilizando o SisDISE (Sistema de Diagnóstico da Sustentabilidade Empresarial), fundamentado nos <strong>10 Princípios do Pacto Global das Nações Unidas</strong>.
                A pontuação é calculada com base em 39 itens de avaliação, distribuídos em três pilares centrais:
            </p>
            <ul style="list-style-type: disc; padding-left: 20px;">
                <li><strong>Direitos Humanos e Trabalho (Sdt):</strong> Avalia o cumprimento das leis trabalhistas, a promoção de um ambiente de trabalho seguro e a responsabilidade social.</li>
                <li><strong>Meio Ambiente (Sma):</strong> Avalia as ações preventivas, iniciativas de responsabilidade ambiental e o uso de tecnologias ecologicamente corretas.</li>
                <li><strong>Anticorrupção (Sac):</strong> Avalia a existência de políticas ativas de combate à corrupção, suborno e extorsão.</li>
            </ul>
        </div>

        <div class="page-break"></div>

        <div class="section-block">
            <h1>3. Análise por Pilar de Sustentabilidade</h1>
            <p>A pontuação total é dividida nos três pilares centrais. A tabela a seguir apresenta o desempenho consolidado em cada pilar, indicando o escore obtido versus o máximo possível (conforme Tabela 7 do Manual Técnico).</p>

            <table class="performance-table">
                <thead>
                    <tr>
                        <th>Pilar de Sustentabilidade</th>
                        <th class="text-right">Escore Obtido</th>
                        <th class="text-right">Escore Máximo</th>
                        <th class="text-right">Percentual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diagnostico->principios as $principio)
                        @php
                            $meta = $metadadosPrincipio[$principio->nomePrincipio] ?? ['max' => 1, 'cor' => ''];
                            $percentualPilar = ($principio->escoreObtido / $meta['max']) * 100;
                        @endphp
                        <tr>
                            <td><strong class="{{ $meta['cor'] }}">{{ $principio->nomePrincipio }}</strong></td>
                            <td class="text-right">{{ round($principio->escoreObtido) }}</td>
                            <td class="text-right">{{ $meta['max'] }}</td>
                            <td class="text-right">{{ round($percentualPilar) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h2 style="margin-top: 40px;">Interpretação dos Pilares</h2>
            @foreach($diagnostico->principios as $principio)
                @php
                    $meta = $metadadosPrincipio[$principio->nomePrincipio];
                    $percentualPilar = ($principio->escoreObtido / $meta['max']) * 100;
                @endphp
                <div class="section-block"> <h3>{{ $principio->nomePrincipio }} ({{ round($percentualPilar) }}%)</h3>
                    <p>
                        @if($percentualPilar < 50)
                            O desempenho neste pilar é <strong>crítico</strong>. A pontuação de {{ round($principio->escoreObtido) }} (de {{ $meta['max'] }}) indica lacunas significativas nas práticas de sustentabilidade. Recomenda-se uma revisão imediata dos parâmetros detalhados na secção seguinte.
                        @elseif($percentualPilar < 75)
                            O desempenho neste pilar é <strong>moderado</strong>. A pontuação de {{ round($principio->escoreObtido) }} (de {{ $meta['max'] }}) demonstra que existem políticas em vigor, mas que há oportunidades claras de melhoria para otimizar o alinhamento com as práticas de ESG.
                        @else
                            O desempenho neste pilar é <strong>forte</strong>. A pontuação de {{ round($principio->escoreObtido) }} (de {{ $meta['max'] }}) indica um bom alinhamento com os princípios do Pacto Global, servindo como uma base sólida para a melhoria contínua.
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        <div class="page-break"></div>

        <div class="section-block">
            <h1>4. Análise Técnica por Parâmetro</h1>
            <p>Cada pilar é composto por parâmetros técnicos. A tabela a seguir (baseada na Tabela 4 do Manual Técnico) detalha o cálculo (Equação 01) e a contribuição de cada um dos seis parâmetros para o Escore Final.</p>

            <table class="detailed-table">
                <thead>
                    <tr>
                        <th>Parâmetro de Sustentabilidade (e Código)</th>
                        <th>Pontos (Σni / Nmax)</th>
                        <th>Peso (pi)</th>
                        <th class="text-right">Escore Ponderado (si)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diagnostico->principios->flatMap->parametroAvaliacaos as $param)
                        @php
                            $meta = $metadadosParametro[$param->descricao] ?? ['codigo' => 'N/A', 'nmax' => 0];
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $meta['codigo'] }}</strong>: {{ $param->descricao }}
                            </td>
                            <td>{{ $param->nota }} / {{ $meta['nmax'] }}</td>
                            <td>{{ number_format($param->peso, 1) }}</td>
                            <td class="text-right"><strong>{{ round($param->escore_obtido) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f9fafb; font-weight: bold; border-top: 2px solid #ddd;">
                        <td colspan="3" class="text-right">SCORE FINAL (Σsi)</td>
                        <td class="text-right">{{ round($diagnostico->escoreFinal) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="page-break"></div>

        <div class="section-block">
            <h1>5. Conclusão e Próximos Passos</h1>

            <h2>Conclusão</h2>
            <p>
                Com um escore final de <strong>{{ round($diagnostico->escoreFinal) }}</strong>, a empresa enquadra-se na classificação de <strong>"{{ $diagnostico->classificacao }}"</strong>.
                Isto indica que a organização já iniciou a sua jornada de sustentabilidade, mas que existem lacunas significativas a serem tratadas para atingir um patamar "Sustentável" (acima de 751 pontos).
            </p>
            <p>
                A análise detalhada indica que, embora existam pontos fortes (visíveis nos parâmetros com maior pontuação), existem também oportunidades críticas de melhoria. A implementação de ações corretivas, especialmente nos parâmetros com baixa pontuação, é essencial para mitigar riscos, melhorar a reputação corporativa e avançar na jornada de sustentabilidade.
            </p>

            <h2>Próximos Passos Recomendados</h2>
            <p>
                O diagnóstico (Etapa 1) está completo. Conforme a metodologia PGES (Plano de Gestão Empresarial Sustentável), os próximos passos são:
            </p>
            <ol style="list-style-type: decimal; padding-left: 20px;">
                <li><strong>Etapa 2 (Definição de Ações):</strong> Utilizar o documento "Plano de Ação (PGES)" gerado pelo sistema SisDISE para identificar e analisar todos os parâmetros de avaliação com notas baixas (0, 1 ou 2).</li>
                <li><strong>Etapa 3 (Plano de Trabalho):</strong> Com base nos itens críticos identificados, elaborar um plano de trabalho formal, com metas de curto, médio e longo prazo, para implementar as ações de melhoria.</li>
            </ol>
            <p>
                Recomenda-se a realização de um novo diagnóstico após a implementação do plano de trabalho (Etapa 3) para monitorizar o progresso e o impacto das ações de melhoria no Escore Final de Sustentabilidade.
            </p>
        </div>
    </div>

</body>
</html>
