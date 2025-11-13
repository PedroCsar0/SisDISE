<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório SisDISE #{{ $diagnostico->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; color: #333; }
        .container { width: 95%; margin: 0 auto; }
        h1 { color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
        h2 { color: #4a5568; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin-top: 30px; }
        .card { background-color: #f9f9f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .score-final { text-align: center; }
        .score-final h2 { margin-top: 0; }
        .score-final .score { font-size: 48px; font-weight: bold; color: #4a5568; }
        .score-final .classificacao { font-size: 24px; font-weight: bold; color: #c0a05e; /* Cor Amarelada/Moderada */ }

        .performance-item { margin-bottom: 15px; }
        .performance-item .info { display: block; margin-bottom: 5px; }
        .performance-item .label { font-weight: bold; }
        .performance-item .score { float: right; font-weight: bold; }
        .progress-bar { background-color: #e2e8f0; border-radius: 5px; height: 15px; width: 100%; }
        .progress { background-color: #4299e1; /* Azul */ height: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <header style="text-align: center; margin-bottom: 20px;">
            <h1 style="border: none;">SisDISE - Relatório de Diagnóstico</h1>
        </header>

        <p><strong>Relatório ID:</strong> #{{ $diagnostico->id }}</p>
        <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($diagnostico->dataAnalise)->format('d/m/Y') }}</p>
        <div class="card score-final">
            <h2>Score Final</h2>
            <div class="score">{{ $diagnostico->escoreFinal }} / 1000</div>
            <div class="classificacao">{{ $diagnostico->classificacao }}</div>
        </div>

        <div class="card">
            <h2>Performance por Princípio</h2>

            @php
                $metadados = [
                    'Direitos humanos e trabalho' => ['max' => 300, 'cor' => '#48bb78'], // Verde
                    'Meio ambiente' => ['max' => 500, 'cor' => '#4299e1'], // Azul
                    'Anticorrupção' => ['max' => 200, 'cor' => '#f56565'], // Vermelho
                ];
            @endphp

            @foreach($diagnostico->principios as $principio)
                @php
                    $meta = $metadados[$principio->nomePrincipio] ?? ['max' => 1, 'cor' => '#ccc'];
                    $percentual = ($principio->escoreObtido / $meta['max']) * 100;
                @endphp
                <div class="performance-item">
                    <span class="info">
                        <span class="label">{{ $principio->nomePrincipio }}</span>
                        <span class="score" style="color: {{ $meta['cor'] }};">
                            {{ round($principio->escoreObtido) }} / {{ $meta['max'] }} ({{ round($percentual) }}%)
                        </span>
                    </span>
                    <div class="progress-bar">
                        <div class="progress" style="width: {{ $percentual }}%; background-color: {{ $meta['cor'] }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
