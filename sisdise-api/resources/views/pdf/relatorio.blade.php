<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SisDISE: {{ $diagnostico->titulo }}</title>
    <style>
        /* Define a fonte padrão e o @page para margens */
        @page {
            margin: 2.5cm 1.5cm 2.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 11pt;
        }

        /* --- Estrutura --- */
        .container { width: 100%; margin: 0 auto; }
        .page-break { page-break-after: always; }

        /* --- Capa --- */
        .cover-page {
            text-align: center;
            margin-top: 200px;
        }
        .cover-title {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            margin: 0;
        }
        .cover-subtitle {
            font-size: 20px;
            color: #4a5568;
            margin-top: 10px;
        }
        .cover-info {
            margin-top: 150px;
            font-size: 14px;
        }

        /* --- Cabeçalho e Rodapé (em todas as páginas, exceto capa) --- */
        header {
            position: fixed;
            top: -2cm; /* Puxa para a margem superior */
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: right;
            font-size: 9pt;
            color: #888;
            border-bottom: 1px solid #e2e8f0;
        }
        footer {
            position: fixed;
            bottom: -2cm; /* Puxa para a margem inferior */
            left: 0px;
            right: 0px;
            height: 50px;
            font-size: 9pt;
            color: #888;
            border-top: 1px solid #e2e8f0;
        }
        footer .page-number:after {
            content: counter(page); /* Contador de página automático */
        }

        /* --- Conteúdo --- */
        h1 {
            font-size: 22px;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }
        h2 {
            font-size: 18px;
            color: #4a5568;
            margin-top: 30px;
        }
        p {
            margin-bottom: 15px;
        }
        .card {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Tabela de Performance (Gráfico de Barras) */
        .performance-table { width: 100%; border-collapse: collapse; }
        .performance-table th, .performance-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .performance-table th { background-color: #f7fafc; }
        .progress-bar { background-color: #e2e8f0; border-radius: 5px; height: 18px; width: 100%; }
        .progress { height: 18px; border-radius: 5px; }
        .color-sdt { background-color: #48bb78; } /* Verde */
        .color-sma { background-color: #4299e1; } /* Azul */
        .color-sac { background-color: #f56565; } /* Vermelho */

        /* Score Final (Gauge) */
        .score-final-card { text-align: center; }
        .score-final { font-size: 48px; font-weight: bold; color: #2d3748; margin: 10px 0; }
        .classificacao { font-size: 24px; font-weight: bold; color: #c0a05e; } /* Amarelo/Ouro */

    </style>
</head>
<body>

    <div class="cover-page">
        <h1 class="cover-title">{{ $diagnostico->titulo }}</h1>
        <h2 class="cover-subtitle">Metodologia SisDISE</h2>

        <div class="cover-info">
            <p><strong>Empresa Avaliada:</strong> {{ $diagnostico->empresa ? $diagnostico->empresa->razaoSocial : '[Empresa não disponível]' }}</p>
            <p><strong>Data do Diagnóstico:</strong> {{ \Carbon\Carbon::parse($diagnostico->dataAnalise)->format('d/m/Y') }}</p>
            <p><strong>ID do Relatório:</strong> #{{ $diagnostico->id }}</p>
        </div>
    </div>

    <div class="page-break"></div>

    <header>
        SisDISE - Relatório de Diagnóstico | {{ $diagnostico->empresa ? $diagnostico->empresa->razaoSocial : '' }}
    </header>

    <footer>
        <span class="page-number">Página </span>
    </footer>

    <div class="container">
        <h1>1. Introdução</h1>
        <p>
            Este documento apresenta o resultado do Diagnóstico de Sustentabilidade Empresarial (SisDISE) realizado na data de {{ \Carbon\Carbon::parse($diagnostico->dataAnalise)->format('d/m/Y') }}.
            O objetivo deste relatório é fornecer uma avaliação quantitativa do alinhamento da empresa com as práticas de <strong>ESG (Environmental, Social and Governance)</strong>.
        </p>
        <p>
            A avaliação serve como o primeiro passo para a elaboração de um Plano de Gestão Empresarial Sustentável (PGES), identificando pontos fortes e áreas que necessitam de melhoria imediata.
        </p>

        <h2>Metodologia Aplicada</h2>
        <p>
            A análise foi conduzida utilizando o **SisDISE (Sistema de Diagnóstico da Sustentabilidade Empresarial)**, uma metodologia desenvolvida por Fernandes (2023).
            [cite_start]Esta metodologia é fundamentada nos **10 Princípios do Pacto Global das Nações Unidas** e avalia a empresa em três pilares centrais [cite: 284-285]:
        </p>
        <ul style="list-style-type: disc; padding-left: 20px;">
            <li><strong>Direitos Humanos e Trabalho (Sdt):</strong> Avalia o cumprimento das leis trabalhistas, a promoção de um ambiente de trabalho seguro e a responsabilidade social com a comunidade.</li>
            <li><strong>Meio Ambiente (Sma):</strong> Avalia as ações preventivas, iniciativas de responsabilidade ambiental e o uso de tecnologias ecologicamente corretas.</li>
            <li><strong>Anticorrupção (Sac):</strong> Avalia a existência de políticas ativas de combate à corrupção, suborno e extorsão.</li>
        </ul>
        <p>
            Um total de 39 parâmetros foram avaliados e pontuados (com notas de 0 a 5), resultando nos escores apresentados na secção seguinte.
        </p>

        <div class="page-break"></div>

        <h1>2. Resultados do Diagnóstico</h1>

        <h2>Escore Final e Classificação</h2>
        <div class="card score-final-card">
            <p style="font-size: 14px; color: #718096; margin: 0;">O escore final (Sf) varia de 0 a 1.000 pontos.</p>
            <div class="score">{{ round($diagnostico->escoreFinal) }}</div>
            <p style="font-size: 16px; margin-top: 0; margin-bottom: 10px;">Classificação (Tabela 6):</p>
            <div class="classificacao">{{ $diagnostico->classificacao }}</div>
        </div>

        <h2>Performance por Princípio (Tabela 7)</h2>
        <div class="card">
            @php
                $metadados = [
                    'Direitos humanos e trabalho' => ['max' => 300, 'cor' => 'color-sdt'],
                    'Meio ambiente' => ['max' => 500, 'cor' => 'color-sma'],
                    'Anticorrupção' => ['max' => 200, 'cor' => 'color-sac'],
                ];
            @endphp

            <table class="performance-table">
                @foreach($diagnostico->principios as $principio)
                    @php
                        $meta = $metadados[$principio->nomePrincipio] ?? ['max' => 1, 'cor' => ''];
                        $percentual = ($principio->escoreObtido / $meta['max']) * 100;
                    @endphp
                    <tr>
                        <td style="width: 30%;">
                            <strong style="color: {{ $meta['cor'] == 'color-sdt' ? '#2f855a' : ($meta['cor'] == 'color-sma' ? '#2b6cb0' : '#c53030') }};">
                                {{ $principio->nomePrincipio }}
                            </strong>
                        </td>
                        <td style="width: 50%;">
                            <div class="progress-bar">
                                <div class="progress {{ $meta['cor'] }}" style="width: {{ $percentual }}%;"></div>
                            </div>
                        </td>
                        <td style="width: 20%; text-align: right;">
                            <strong>{{ round($principio->escoreObtido) }} / {{ $meta['max'] }}</strong>
                            ({{ round($percentual) }}%)
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="page-break"></div>

        <h1>3. Análise e Próximos Passos</h1>

        <h2>Análise dos Resultados</h2>
        <p>
            Com um escore final de <strong>{{ round($diagnostico->escoreFinal) }}</strong>, a empresa enquadra-se na classificação de <strong>"{{ $diagnostico->classificacao }}"</strong>.
            Isto indica que a empresa já possui práticas de sustentabilidade implementadas, mas ainda existem lacunas significativas para atingir um patamar "Sustentável" (acima de 751 pontos).
        </p>
        <p>A análise detalhada por princípio revela que:</p>
        <ul style="list-style-type: disc; padding-left: 20px;">
            @foreach($diagnostico->principios as $principio)
                @php
                    $meta = $metadados[$principio->nomePrincipio];
                    $percentual = ($principio->escoreObtido / $meta['max']) * 100;
                @endphp
                <li>
                    <strong>{{ $principio->nomePrincipio }}:</strong>
                    Atingiu {{ round($percentual) }}% do escore máximo ({{ round($principio->escoreObtido) }} de {{ $meta['max'] }}).
                    @if($percentual < 50)
                        Esta é uma área <strong>crítica</strong> que exige atenção imediata.
                    @elseif($percentual < 75)
                        Esta é uma área com <strong>oportunidades claras de melhoria</strong>.
                    @else
                        Esta é uma área de <strong>bom desempenho</strong>.
                    @endif
                </li>
            @endforeach
        </ul>

        <h2>Próximos Passos Recomendados</h2>
        <p>
            O diagnóstico (Etapa 1) está completo. Conforme a metodologia PGES (Plano de Gestão Empresarial Sustentável), os próximos passos são:
        </p>
        <ol style="padding-left: 20px;">
            [cite_start]<li><strong>Etapa 2 (Definição de Ações):</strong> Utilizar a funcionalidade "Gerar Plano de Ação (PGES)" no sistema para identificar todos os parâmetros de avaliação com notas baixas (0, 1 ou 2) [cite: 281-282].</li>
            [cite_start]<li><strong>Etapa 3 (Plano de Trabalho):</strong> Elaborar um plano de trabalho formal, com metas de curto, médio e longo prazo, para implementar as ações definidas na Etapa 2[cite: 283].</li>
        </ol>
        <p>A implementação destas ações é fundamental para melhorar o alinhamento da empresa com os princípios de ESG e avançar na sua jornada de sustentabilidade.</p>
    </div>

</body>
</html>
