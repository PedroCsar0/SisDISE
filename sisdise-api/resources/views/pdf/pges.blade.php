<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Plano de Ação (PGES) - SisDISE #{{ $diagnostico->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; color: #333; }
        .container { width: 95%; margin: 0 auto; }
        h1 { color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
        h2 { color: #4a5568; margin-top: 30px; }
        .header-info { background-color: #f9f9f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; }

        .item-melhoria {
            border: 1px solid #e53e3e; /* Borda Vermelha */
            background-color: #fff5f5; /* Fundo Vermelho Claro */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .item-melhoria .codigo {
            font-size: 1.1em;
            font-weight: bold;
            color: #c53030; /* Vermelho Escuro */
        }
        .item-melhoria .nota {
            font-weight: bold;
            float: right;
            color: #c53030;
            font-size: 1.1em;
        }
        .item-melhoria .descricao {
            margin-top: 5px;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="container">
        <header style="text-align: center; margin-bottom: 20px;">
            <h1 style="border: none;">Plano de Gestão Empresarial Sustentável (PGES)</h1>
        </header>

        <div class="header-info">
            <p><strong>Relatório de Referência:</strong> #{{ $diagnostico->id }}</p>
            <p><strong>Data do Diagnóstico:</strong> {{ \Carbon\Carbon::parse($diagnostico->dataAnalise)->format('d/m/Y') }}</p>
            <p><strong>Classificação Atual:</strong> {{ $diagnostico->classificacao }} (Score: {{ $diagnostico->escoreFinal }})</p>
        </div>

        <h2>Plano de Ação (Itens Críticos)</h2>
        <p>Os seguintes parâmetros foram identificados com nota baixa (≤ 2) e requerem ação imediata para melhoria.</p>

        <hr style="margin-top: 20px; margin-bottom: 20px;">

        @if(count($itensParaMelhorar) > 0)
            @foreach($itensParaMelhorar as $item)
                <div class="item-melhoria">
                    <span class="nota">Nota: {{ $item->nota }}</span>
                    <div class="codigo">
                        Parâmetro: {{ $item->itemParametro->codigo_item }}
                    </div>
                    <div class="descricao">
                        {{ $item->itemParametro->descricao }}
                    </div>
                </div>
            @endforeach
        @else
            <div style="background-color: #f0fff4; border: 1px solid #9ae6b4; padding: 15px; border-radius: 8px; text-align: center;">
                <p style="font-weight: bold; color: #2f855a;">Parabéns! Nenhum item foi classificado com nota baixa (≤ 2).</p>
            </div>
        @endif
    </div>
</body>
</html>
