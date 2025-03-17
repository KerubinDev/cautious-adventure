<?php
require_once 'common.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <style>
        <?= getThemeCSS() ?>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background-color: var(--secondary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
        }
        
        .settings-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 15px 20px;
            background-color: var(--accent);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .settings-info {
            display: flex;
            gap: 30px;
        }
        
        .setting-item {
            text-align: center;
        }
        
        .setting-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .setting-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            opacity: 0.8;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        h1 {
            font-size: 2.5rem;
            text-align: center;
            margin: 50px 0 30px;
            color: var(--text);
        }
        
        .modes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .mode-card {
            background-color: var(--accent);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .mode-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .mode-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .mode-info {
            padding: 20px;
        }
        
        .mode-title {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--text);
        }
        
        .mode-description {
            font-size: 0.9rem;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        footer {
            background-color: var(--secondary);
            padding: 20px 0;
            margin-top: auto;
            text-align: center;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Valorant Aim Trainer</h1>
            <p style="text-align: center; margin-bottom: 20px;">Aprimore suas habilidades de mira com exercícios específicos para Valorant</p>
            
            <div class="settings-bar">
                <div class="settings-info">
                    <div class="setting-item">
                        <div class="setting-value"><?= $_SESSION['settings']['dpi'] ?></div>
                        <div class="setting-label">DPI</div>
                    </div>
                    <div class="setting-item">
                        <div class="setting-value"><?= $_SESSION['settings']['sensitivity'] ?></div>
                        <div class="setting-label">SENSIBILIDADE</div>
                    </div>
                    <div class="setting-item">
                        <div class="setting-value"><?= $_SESSION['settings']['edpi'] ?></div>
                        <div class="setting-label">EDPI</div>
                    </div>
                </div>
                <div class="action-buttons">
                    <a href="stats.php" class="btn btn-secondary">Ver Estatísticas</a>
                    <a href="settings.php" class="btn">Configurações Avançadas</a>
                </div>
            </div>
        </div>
    </header>
    
    <main class="container">
        <h2 style="margin: 40px 0 20px; text-align: center;">Escolha seu modo de treino</h2>
        
        <div class="modes-grid">
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1589254065878-42c9da997008" alt="Precisão" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Precisão</h3>
                    <p class="mode-description">Treine sua precisão com alvos estáticos. Melhore sua capacidade de acertar headshots consistentes.</p>
                    <a href="precision.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420" alt="Reflexo" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Reflexo</h3>
                    <p class="mode-description">Aumente sua velocidade de reação com alvos que aparecem rapidamente. Treine para situações de duelos.</p>
                    <a href="reflex.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1551808525-51a94da548ce" alt="Tracking" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Tracking</h3>
                    <p class="mode-description">Aprimore sua capacidade de acompanhar alvos em movimento. Ideal para agentes que usam habilidades de movimento.</p>
                    <a href="tracking.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1548191194-b3d4f051fd7d" alt="Flick" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Flick</h3>
                    <p class="mode-description">Treine movimentos rápidos da mira entre dois pontos. Essencial para reações rápidas e precisas.</p>
                    <a href="flick.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1577401239170-897942555fb3" alt="Micro Ajustes" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Micro Ajustes</h3>
                    <p class="mode-description">Melhore o controle fino da mira para ajustes precisos. Perfeito para controle de recoil e ajustes mínimos.</p>
                    <a href="micro-adjust.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <img src="https://images.unsplash.com/photo-1560419555-d3123bcbb23a" alt="Target Switching" class="mode-image">
                <div class="mode-info">
                    <h3 class="mode-title">Target Switching</h3>
                    <p class="mode-description">Pratique a troca rápida entre alvos. Fundamental para eliminar múltiplos inimigos rapidamente.</p>
                    <a href="switching.php" class="btn">Iniciar Treino</a>
                </div>
            </div>
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; 2023 Valorant Aim Trainer | Design inspirado no universo de Valorant | Não afiliado à Riot Games</p>
        </div>
    </footer>
</body>
</html>