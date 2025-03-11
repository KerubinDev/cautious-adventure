<?php
session_start();

// Obter todas as pontuações armazenadas
$modes = [
    'precision' => ['name' => 'Precisão', 'icon' => '🎯'],
    'reflex' => ['name' => 'Reflexo', 'icon' => '⚡'],
    'tracking' => ['name' => 'Tracking', 'icon' => '👁️'],
    'flick' => ['name' => 'Flick', 'icon' => '💨'],
    'microadjust' => ['name' => 'Micro Ajustes', 'icon' => '🔍'],
    'switching' => ['name' => 'Target Switching', 'icon' => '↔️']
];

// Pontuações simuladas - num sistema real, isso viria de um banco de dados
// Para simulação, vamos criar um histórico fictício se não existir
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
    
    // Datas para os últimos 30 dias
    $dates = [];
    for ($i = 30; $i >= 0; $i--) {
        $dates[] = date('Y-m-d', strtotime("-$i days"));
    }
    
    // Gerar dados aleatórios para cada modo
    foreach ($modes as $mode_key => $mode_info) {
        $_SESSION["{$mode_key}_highscore"] = rand(500, 1000);
        
        foreach ($dates as $date) {
            // Não criar entradas para todos os dias para tornar mais realista
            if (rand(0, 2) > 0) {
                $attempts = rand(1, 3); // Número de tentativas naquele dia
                
                for ($i = 0; $i < $attempts; $i++) {
                    $_SESSION['history'][] = [
                        'mode' => $mode_key,
                        'score' => rand(200, 1000),
                        'date' => $date,
                        'time' => date('H:i', strtotime(rand(8, 22) . ':' . rand(0, 59)))
                    ];
                }
            }
        }
    }
    
    // Ordenar histórico por data e hora
    usort($_SESSION['history'], function($a, $b) {
        $dateA = strtotime($a['date'] . ' ' . $a['time']);
        $dateB = strtotime($b['date'] . ' ' . $b['time']);
        return $dateB - $dateA; // Ordem decrescente (mais recente primeiro)
    });
}

// Obter recordes
$highscores = [];
foreach ($modes as $mode_key => $mode_info) {
    $highscores[$mode_key] = $_SESSION["{$mode_key}_highscore"] ?? 0;
}

// Obter desempenho por modo para o último mês
$performance_by_mode = [];
foreach ($modes as $mode_key => $mode_info) {
    $scores = array_filter($_SESSION['history'], function($entry) use ($mode_key) {
        return $entry['mode'] === $mode_key && 
               strtotime($entry['date']) >= strtotime('-30 days');
    });
    
    if (count($scores) > 0) {
        $avg_score = array_sum(array_column($scores, 'score')) / count($scores);
        $performance_by_mode[$mode_key] = round($avg_score);
    } else {
        $performance_by_mode[$mode_key] = 0;
    }
}

// Obter datas para o gráfico de evolução (últimos 14 dias)
$chart_dates = [];
$chart_data = [];
for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_dates[] = date('d/m', strtotime($date));
    
    // Obter média de pontuação para cada dia, para todos os modos
    $day_scores = array_filter($_SESSION['history'], function($entry) use ($date) {
        return $entry['date'] === $date;
    });
    
    if (count($day_scores) > 0) {
        $avg_score = array_sum(array_column($day_scores, 'score')) / count($day_scores);
        $chart_data[] = round($avg_score);
    } else {
        $chart_data[] = null; // Sem dados para este dia
    }
}

// Obter modos mais jogados
$mode_counts = [];
foreach ($modes as $mode_key => $mode_info) {
    $mode_counts[$mode_key] = count(array_filter($_SESSION['history'], function($entry) use ($mode_key) {
        return $entry['mode'] === $mode_key;
    }));
}
arsort($mode_counts); // Ordenar por contagem (decrescente)
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas | Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #ff4655;
            --secondary: #0f1923;
            --text: #f9f9f9;
            --accent: #28344a;
            --accent-light: #3a4a66;
            --card-bg: #1a2634;
            --success: #3edd87;
            --warning: #f7c948;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--secondary);
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--accent);
            padding-bottom: 1rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .nav {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            background-color: var(--primary);
            color: var(--text);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn:hover {
            transform: scale(1.05);
        }
        
        .btn-secondary {
            background-color: var(--accent);
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        
        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--accent);
        }
        
        .stat-card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stat-card-icon {
            font-size: 1.5rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .progress-bar-container {
            width: 100%;
            height: 8px;
            background-color: var(--accent);
            border-radius: 4px;
            overflow: hidden;
            margin: 0.5rem 0;
        }
        
        .progress-bar {
            height: 100%;
            background-color: var(--primary);
        }
        
        .chart-container {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .chart-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        canvas {
            width: 100%;
            height: 300px;
        }
        
        .history-container {
            background-color: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: var(--accent);
            color: var(--text);
        }
        
        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
        }
        
        th {
            font-weight: bold;
        }
        
        tbody tr {
            border-bottom: 1px solid var(--accent);
        }
        
        tbody tr:hover {
            background-color: var(--accent-light);
        }
        
        .mode-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            background-color: var(--accent);
        }
        
        .mode-badge.precision { background-color: #ff4655; }
        .mode-badge.reflex { background-color: #ffa500; }
        .mode-badge.tracking { background-color: #3edd87; }
        .mode-badge.flick { background-color: #00a8ff; }
        .mode-badge.microadjust { background-color: #9c27b0; }
        .mode-badge.switching { background-color: #ff6b81; }
        
        .most-played {
            display: flex;
            gap: 1rem;
        }
        
        .most-played-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            background-color: var(--accent);
            border-radius: 8px;
        }
        
        .most-played-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .most-played-name {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .most-played-count {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--accent);
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .most-played {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">Valorant Aim Trainer</div>
            <div class="nav">
                <a href="settings.php" class="btn btn-secondary">Configurações</a>
                <a href="index.php" class="btn">Voltar ao Menu</a>
            </div>
        </header>
        
        <h1>Suas Estatísticas</h1>
        
        <!-- Resumo de recordes -->
        <div class="stats-grid">
            <?php foreach ($modes as $mode_key => $mode_info): ?>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title"><?= $mode_info['name'] ?></div>
                        <div class="stat-card-icon"><?= $mode_info['icon'] ?></div>
                    </div>
                    <div class="stat-value"><?= $highscores[$mode_key] ?></div>
                    <div class="stat-label">Recorde Pessoal</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= min(100, ($highscores[$mode_key] / 1000) * 100) ?>%;"></div>
                    </div>
                    <div class="stat-label">Média últimos 30 dias: <?= $performance_by_mode[$mode_key] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Gráfico de evolução -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">Evolução nos Últimos 14 Dias</div>
                <div class="chart-filters">
                    <!-- Poderíamos adicionar filtros aqui -->
                </div>
            </div>
            <canvas id="progressChart"></canvas>
        </div>
        
        <!-- Modos mais jogados -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">Modos Mais Jogados</div>
            </div>
            <div class="most-played">
                <?php 
                $count = 0;
                foreach ($mode_counts as $mode_key => $count_value): 
                    if ($count < 3): // Mostrar apenas os 3 mais jogados
                ?>
                    <div class="most-played-item">
                        <div class="most-played-icon"><?= $modes[$mode_key]['icon'] ?></div>
                        <div class="most-played-name"><?= $modes[$mode_key]['name'] ?></div>
                        <div class="most-played-count"><?= $count_value ?> vezes</div>
                    </div>
                <?php 
                    endif;
                    $count++;
                endforeach; 
                ?>
            </div>
        </div>
        
        <!-- Histórico de treinos -->
        <div class="history-container">
            <div class="table-header">
                <div class="chart-title">Histórico de Treinos</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Modo</th>
                        <th>Pontuação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Mostrar apenas os 10 mais recentes
                    $count = 0;
                    foreach ($_SESSION['history'] as $entry): 
                        if ($count < 10):
                    ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($entry['date'])) ?> <?= $entry['time'] ?></td>
                            <td>
                                <span class="mode-badge <?= $entry['mode'] ?>">
                                    <?= $modes[$entry['mode']]['icon'] ?> <?= $modes[$entry['mode']]['name'] ?>
                                </span>
                            </td>
                            <td><?= $entry['score'] ?></td>
                        </tr>
                    <?php 
                        endif;
                        $count++;
                    endforeach; 
                    ?>
                </tbody>
            </table>
        </div>
        
        <footer>
            <p>© 2025 Valorant Aim Trainer | Inspirado pelo jogo Valorant da Riot Games</p>
            <p>Este é um projeto não oficial e não tem afiliação com a Riot Games.</p>
        </footer>
    </div>
    
    <script>
        // Configurar o gráfico de evolução
        const ctx = document.getElementById('progressChart').getContext('2d');
        
        // Dados para o gráfico
        const chartData = {
            labels: <?= json_encode($chart_dates) ?>,
            datasets: [{
                label: 'Pontuação Média',
                data: <?= json_encode($chart_data) ?>,
                backgroundColor: 'rgba(255, 70, 85, 0.2)',
                borderColor: 'rgba(255, 70, 85, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        };
        
        // Configurações do gráfico
        const chartConfig = {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#f9f9f9'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#28344a',
                        titleColor: '#ff4655',
                        bodyColor: '#f9f9f9',
                        borderColor: '#f9f9f9',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#f9f9f9'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#f9f9f9'
                        }
                    }
                }
            }
        };
        
        // Criar o gráfico
        const progressChart = new Chart(ctx, chartConfig);
    </script>
</body>
</html> 