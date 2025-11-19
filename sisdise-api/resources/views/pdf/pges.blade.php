<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Plano de Ação (PGES) - SisDISE #{{ $diagnostico->id }}</title>
    <style>
        @page { margin: 2.5cm 1.5cm 2.5cm 1.5cm; }
        body { font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; line-height: 1.5; color: #333; font-size: 11pt; }
        .container { width: 100%; margin: 0 auto; }
        .page-break { page-break-after: always; }

        header { position: fixed; top: -2cm; left: 0px; right: 0px; height: 50px; text-align: right; font-size: 9pt; color: #777; border-bottom: 1px solid #ddd; }
        footer { position: fixed; bottom: -2cm; left: 0px; right: 0px; height: 50px; font-size: 9pt; color: #777; border-top: 1px solid #ddd; }
        footer .page-number:after { content: counter(page); }

        h1 { font-size: 24px; color: #111; margin-bottom: 20px; border-bottom: 2px solid #b91c1c; padding-bottom: 5px; }
        h2 { font-size: 18px; color: #333; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        p { margin-bottom: 15px; text-align: justify; }

        /* Capa */
        .cover-page { text-align: center; margin-top: 150px; page-break-after: always; }
        .cover-title { font-size: 28px; font-weight: bold; color: #1f2937; margin: 0; border: none; }
        .cover-subtitle { font-size: 20px; color: #4b5563; margin-top: 10px; }
        .cover-info { margin-top: 150px; font-size: 14px; color: #374151; }

        .card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px; }

        /* Item de Melhoria (Ponto Crítico) */
        .item-melhoria {
            border: 1px solid #e53e3e;
            background-color: #fff5f5;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            page-break-inside: avoid; /* Evita que o card quebre ao meio */
        }
        .item-melhoria .header {
            border-bottom: 1px dashed #feb2b2;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .item-melhoria .codigo {
            font-size: 1.1em;
            font-weight: bold;
            color: #c53030;
        }
        .item-melhoria .nota {
            float: right;
            font-weight: bold;
            color: #c53030;
            font-size: 1.1em;
        }
        .item-melhoria .descricao {
            margin-top: 5px;
            color: #1f2937;
            font-style: italic;
            font-weight: bold;
        }
        .item-melhoria .criterio {
            margin-top: 10px;
            font-size: 10pt;
            color: #718096;
        }

        /* --- (REGRAS CORRIGIDAS) CONTROLO DE QUEBRA DE PÁGINA --- */
        .section-block {
            page-break-inside: avoid;
        }
        p, li, ul, ol {
            widows: 3;
            orphans: 3;
        }
        /* --- FIM DAS REGRAS CORRIGIDAS --- */
    </style>
</head>
<body>

    <div class="cover-page">
        <h1 class="cover-title">Plano de Gestão Empresarial Sustentável (PGES)</h1>
        <h2 class="cover-subtitle">Identificação de Pontos Críticos</h2>

        <div class="cover-info">
            <p><strong>Empresa Avaliada:</strong> {{ $diagnostico->empresa ? $diagnostico->empresa->razaoSocial : '[Empresa não disponível]' }}</p>
            <p><strong>Baseado no Relatório:</strong> {{ $diagnostico->titulo }} (#{{ $diagnostico->id }})</p>
            <p><strong>Data de Geração:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>
    </div>
    <header>
        SisDISE - Plano de Ação (PGES) | {{ $diagnostico->titulo }}
    </header>
    <footer>
        Documento confidencial gerado pelo SisDISE em {{ \Carbon\Carbon::now()->format('d/m/Y') }}.
        <span class="page-number" style="float: right;">Página </span>
    </footer>

    <div class="container">
        <div class="section-block">
            <h1>1. Introdução ao Plano de Ação</h1>
            <p>
                Este documento representa a <strong>Etapa 2 (Definição de Ações)</strong> do Plano de Gestão Empresarial Sustentável (PGES), conforme a metodologia SisDISE. Ele foi gerado automaticamente pelo sistema com base nos resultados do Diagnóstico "#{{ $diagnostico->id }}".
            </p>
            <p>
                O objetivo deste plano é listar, de forma objetiva, todos os parâmetros de avaliação que receberam uma **nota baixa (igual ou inferior a 2)**. Estes parâmetros representam as maiores oportunidades de melhoria e os riscos mais significativos para a empresa.
            </p>
            <p>
                A classificação atual da empresa é <strong>"{{ $diagnostico->classificacao }}"</strong> (Score: {{ round($diagnostico->escoreFinal) }}), o que reforça a necessidade de implementação de ações corretivas.
            </p>

            <h2>Critérios de Avaliação (Tabela 3)</h2>
            <p>
                As notas são atribuídas numa escala de 0 a 5. Os itens abaixo são sinalizados porque as suas notas indicam ausência de conformidade ou apenas o cumprimento mínimo da legislação:
            </p>
            <ul style="list-style-type: none; padding-left: 0;">
                <li><strong>Nota 0:</strong> Não cumpre as leis/normas.</li>
                <li><strong>Nota 1:</strong> Cumpre parcialmente as leis/normas.</li>
                <li><strong>Nota 2:</strong> Cumpre somente o que é exigido pelas leis/normas.</li>
            </ul>
        </div>

        <div class="page-break"></div>

        <div class="section-block">
            <h1>2. Itens de Ação Prioritária</h1>
            <p>
                Um total de <strong>{{ count($itensParaMelhorar) }}</strong> (de 39) parâmetros foram identificados como críticos. Recomenda-se que a gestão crie um plano de trabalho (Etapa 3) para endereçar os seguintes itens:
            </p>

            @php
                // Mapeamento dos critérios da Tabela 3
                $criterios = [
                    0 => "Não cumpre as leis/normas.",
                    1 => "Cumpre parcialmente as leis/normas.",
                    2 => "Cumpre somente o que é exigido pelas leis/normas."
                ];
            @endphp

            @if(count($itensParaMelhorar) > 0)
                @foreach($itensParaMelhorar as $item)
                    <div class="item-melhoria">
                        <div class="header">
                            <span class="nota">Nota Recebida: {{ $item->nota }}</span>
                            <div class="codigo">
                                Parâmetro: {{ $item->itemParametro->codigo_item }}
                            </div>
                        </div>
                        <div class="descricao">
                            "{{ $item->itemParametro->descricao }}"
                        </div>
                        <div class="criterio">
                            <strong>Justificativa da Nota:</strong> O critério para a nota {{ $item->nota }} é: "{{ $criterios[$item->nota] ?? 'Nota baixa' }}"
                        </div>
                        <div style="margin-top: 10px; background-color: #f0fff4; padding: 10px; border-radius: 5px; border: 1px solid #c6f6d5;">
                        <strong style="color: #2f855a;">Recomendação Técnica:</strong>
                        <p style="maragin: 5px 0 0 0; font-size: 10pt; color: #2f855a;">
                            {{ $item->itemParametro->recomendacao ?? 'Definir plano de ação corretiva.' }}
                        </p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card" style="background-color: #f0fff4; border-color: #9ae6b4; text-align: center; page-break-inside: avoid;">
                    <h2 style="margin-top: 0; color: #2f855a;">Resultado Excelente</h2>
                    <p style="font-weight: bold; color: #2f855a;">Nenhum item foi classificado com nota baixa (≤ 2).</p>
                    <p>O diagnóstico não identificou pontos críticos de melhoria imediata com base nesta metodologia.</p>
                </div>
            @endif
        </div>

        <div class="section-block">
            <h2 style="margin-top: 40px;">3. Próximos Passos (Etapa 3)</h2>
            <p>
                Com esta lista de ações prioritárias, a gestão da empresa deve agora focar-se na <strong>Etapa 3: Plano de Trabalho</strong>.
                Isto envolve:
            </p>
            <ol style="list-style-type: decimal; padding-left: 20px;">
                <li>Atribuir um responsável (departamento ou indivíduo) para cada item de melhoria listado.</li>
                <li>Definir prazos (curto, médio, longo prazo) para a implementação das soluções.</li>
                <li>Alocar os recursos necessários (financeiros, humanos, tecnológicos) para a execução.</li>
                <li>Estabelecer métricas claras para monitorizar o sucesso e a conclusão de cada ação.</li>
            </ol>
        </div>
    </div>

</body>
</html>
