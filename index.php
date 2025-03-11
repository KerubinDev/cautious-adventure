<?php
// config.php
session_start();

// Salvar configurações de sensibilidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['dpi'] = (int)$_POST['dpi'];
    $_SESSION['sens'] = (float)$_POST['sens'];
    $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];
}

// Calcular eDPI (effective DPI)
$edpi = $_SESSION['edpi'] ?? 400; // Valor padrão
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <style>
        :root {
            --primary: #ff4655;
            --secondary: #0f1923;
            --text: #f9f9f9;
            --accent: #28344a;
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
            text-align: center;
            margin-bottom: 3rem;
        }
        
        h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        p.subtitle {
            font-size: 1.2rem;
            opacity: 0.8;
        }
        
        .settings-bar {
            background-color: var(--accent);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .settings-bar form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .settings-bar label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .settings-bar input {
            background-color: var(--secondary);
            color: var(--text);
            border: 1px solid var(--primary);
            padding: 0.5rem;
            border-radius: 4px;
            width: 80px;
        }
        
        .settings-bar button {
            background-color: var(--primary);
            color: var(--text);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
        }
        
        .settings-bar button:hover {
            transform: scale(1.05);
        }
        
        .modes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .mode-card {
            background-color: var(--accent);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .mode-card:hover {
            transform: translateY(-10px);
        }
        
        .mode-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        
        .mode-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(0deg, rgba(15, 25, 35, 0.8) 0%, rgba(15, 25, 35, 0) 50%);
        }
        
        .mode-content {
            padding: 1.5rem;
        }
        
        .mode-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }
        
        .mode-description {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .mode-btn {
            display: inline-block;
            background-color: var(--primary);
            color: var(--text);
            text-decoration: none;
            padding: 0.7rem 1.5rem;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .mode-btn:hover {
            background-color: #ff2a3c;
        }
        
        .difficulty {
            display: flex;
            gap: 5px;
            margin-bottom: 0.5rem;
        }
        
        .difficulty-point {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: var(--primary);
            opacity: 0.3;
        }
        
        .difficulty-point.active {
            opacity: 1;
        }
        
        footer {
            text-align: center;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid #28344a;
            font-size: 0.9rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Valorant Aim Trainer</h1>
            <p class="subtitle">Aprimore suas habilidades de mira com exercícios específicos para Valorant</p>
        </header>
        
        <div class="settings-bar">
            <form method="POST" action="config.php">
                <label>
                    DPI:
                    <input type="number" name="dpi" value="<?= $_SESSION['dpi'] ?>" required>
            </label>
            
                <label>
                    Sensibilidade:
                    <input type="number" step="0.1" name="sens" value="<?= $_SESSION['sens'] ?>" required>
            </label>
            
                <button type="submit">Salvar Configurações</button>
        </form>
            
            <div>
                <strong>eDPI:</strong> <?= $_SESSION['edpi'] ?>
            </div>
    </div>

        <h2>Escolha seu modo de treino</h2>
        
        <div class="modes-grid">
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1560253023-3ec5d502959f?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point"></span>
                    </div>
                    <h3 class="mode-title">Precisão</h3>
                    <p class="mode-description">Treine sua precisão com alvos estáticos. Melhore sua capacidade de acertar headshots consistentes.</p>
                    <a href="precision.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                    </div>
                    <h3 class="mode-title">Reflexo</h3>
                    <p class="mode-description">Aumente sua velocidade de reação com alvos que aparecem rapidamente. Treine para situações de duelos.</p>
                    <a href="reflex.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                    </div>
                    <h3 class="mode-title">Tracking</h3>
                    <p class="mode-description">Aprimore sua capacidade de acompanhar alvos em movimento. Ideal para agentes que usam habilidades de movimento.</p>
                    <a href="tracking.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1551103782-8ab07afd45c1?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point"></span>
                        <span class="difficulty-point"></span>
                    </div>
                    <h3 class="mode-title">Flick</h3>
                    <p class="mode-description">Treine sua capacidade de realizar flicks rápidos e precisos. Essencial para jogadores de Operator e Sheriff.</p>
                    <a href="flick.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1614680376573-df3480f0c6ff?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                    </div>
                    <h3 class="mode-title">Micro Ajustes</h3>
                    <p class="mode-description">Melhore sua capacidade de realizar pequenos ajustes na mira. Perfeito para melhorar spray control.</p>
                    <a href="micro-adjust.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
            
            <div class="mode-card">
                <div class="mode-image" style="background-image: url('https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&q=80&w=2000')"></div>
                <div class="mode-content">
                    <div class="difficulty">
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                        <span class="difficulty-point active"></span>
                    </div>
                    <h3 class="mode-title">Target Switching</h3>
                    <p class="mode-description">Treine para alternar rapidamente entre alvos. Essencial para situações de múltiplos inimigos.</p>
                    <a href="switching.php" class="mode-btn">Iniciar Treino</a>
                </div>
            </div>
        </div>
        
        <footer>
            <p>© 2023 Valorant Aim Trainer | Inspirado pelo jogo Valorant da Riot Games</p>
            <p>Este é um projeto não oficial e não tem afiliação com a Riot Games.</p>
        </footer>
    </div>
</body>
</html>