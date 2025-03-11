<?php
session_start();

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['dpi'])) $_SESSION['dpi'] = 800;
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = 0.5;
if (!isset($_SESSION['edpi'])) $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];

// Recuperar highscore se existir
$highscore = $_SESSION['tracking_highscore'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Tracking | Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <style>
        :root {
            --primary: #ff4655;
            --secondary: #0f1923;
            --text: #f9f9f9;
            --accent: #28344a;
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
            overflow: hidden;
        }
        
        .container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: rgba(15, 25, 35, 0.9);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 10;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stats {
            display: flex;
            gap: 2rem;
        }
        
        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            opacity: 0.8;
        }
        
        .settings {
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
        
        .arena {
            flex-grow: 1;
            position: relative;
            background-color: #151f2e;
            background-image: 
                linear-gradient(rgba(255, 70, 85, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 70, 85, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            cursor: crosshair;
            overflow: hidden;
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            transition: box-shadow 0.2s;
            cursor: crosshair;
        }
        
        .target .inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background-color: rgba(15, 25, 35, 0.6);
        }
        
        .target .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 25%;
            height: 25%;
            border-radius: 50%;
            background-color: var(--primary);
        }
        
        .target.active {
            box-shadow: 0 0 0 5px rgba(255, 70, 85, 0.5);
        }
        
        #crosshair {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 100;
            opacity: 0.8;
        }
        
        #countdown {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 6rem;
            font-weight: bold;
            color: var(--primary);
            opacity: 0.9;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 100;
        }
        
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 25, 35, 0.9);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .overlay-content {
            max-width: 600px;
            width: 100%;
            background-color: var(--accent);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
        }
        
        .overlay h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        
        .overlay p {
            margin-bottom: 1.5rem;
        }
        
        .score-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        
        .score-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .score-label {
            font-weight: bold;
        }
        
        .difficulty-controls {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .progress-container {
            width: 100%;
            height: 5px;
            background-color: var(--secondary);
            border-radius: 2px;
            overflow: hidden;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        
        .progress-bar {
            height: 100%;
            background-color: var(--primary);
            transition: width 1s linear;
            width: 100%;
        }
        
        .hide {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Modo Tracking</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Pontuação</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="accuracy">0%</div>
                    <div class="stat-label">Precisão</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="multiplier">x1</div>
                    <div class="stat-label">Multiplicador</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="timer">60</div>
                    <div class="stat-label">Tempo</div>
                </div>
            </div>
            <div class="settings">
                <button id="restart-btn" class="btn btn-secondary">Reiniciar</button>
                <a href="index.php" class="btn">Voltar ao Menu</a>
            </div>
        </div>
        
        <div class="arena" id="arena">
            <div id="crosshair"></div>
            <div id="countdown" class="hide"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Tracking</h2>
                <p>Treine sua capacidade de seguir alvos em movimento. Mantenha seu cursor sobre o alvo para pontuar.</p>
                
                <div class="score-grid">
                    <div class="score-item">
                        <span class="score-label">Seu Recorde:</span>
                        <span class="score-value"><?= $highscore ?> pontos</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo:</span>
                        <span class="score-value">60 segundos</span>
                    </div>
                </div>
                
                <h3>Selecione a Dificuldade:</h3>
                <div class="difficulty-controls">
                    <button class="btn difficulty-btn" data-difficulty="easy">Fácil</button>
                    <button class="btn difficulty-btn" data-difficulty="medium">Médio</button>
                    <button class="btn difficulty-btn" data-difficulty="hard">Difícil</button>
                </div>
                
                <button id="start-btn" class="btn">Iniciar Treino</button>
            </div>
        </div>
        
        <!-- Overlay de resultados -->
        <div class="overlay hide" id="result-overlay">
            <div class="overlay-content">
                <h2>Treino Finalizado!</h2>
                
                <div class="score-grid">
                    <div class="score-item">
                        <span class="score-label">Pontuação Final:</span>
                        <span class="score-value" id="final-score">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Precisão Média:</span>
                        <span class="score-value" id="final-accuracy">0%</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Multiplicador Máximo:</span>
                        <span class="score-value" id="final-multiplier">x1</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo de Tracking:</span>
                        <span class="score-value" id="final-tracking-time">0s</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Novo Recorde?</span>
                        <span class="score-value" id="final-highscore">Não</span>
                    </div>
                </div>
                
                <div>
                    <button id="restart-btn-result" class="btn">Tentar Novamente</button>
                    <a href="index.php" class="btn btn-secondary">Voltar ao Menu</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Configurações baseadas no PHP
        const edpi = <?= $_SESSION['edpi'] ?>;
        
        // Variáveis do jogo
        let gameActive = false;
        let score = 0;
        let accuracy = 0;
        let multiplier = 1;
        let timeLeft = 60;
        let timeInterval;
        let difficulty = 'medium'; // Padrão
        let trackingTime = 0;
        let totalChecks = 0;
        let successfulChecks = 0;
        let maxMultiplier = 1;
        
        // Elementos DOM
        const arena = document.getElementById('arena');
        const crosshair = document.getElementById('crosshair');
        const scoreElement = document.getElementById('score');
        const accuracyElement = document.getElementById('accuracy');
        const multiplierElement = document.getElementById('multiplier');
        const timerElement = document.getElementById('timer');
        const progressBar = document.getElementById('progress-bar');
        const startOverlay = document.getElementById('start-overlay');
        const resultOverlay = document.getElementById('result-overlay');
        const startButton = document.getElementById('start-btn');
        const restartButton = document.getElementById('restart-btn');
        const restartButtonResult = document.getElementById('restart-btn-result');
        const countdownElement = document.getElementById('countdown');
        const difficultyButtons = document.querySelectorAll('.difficulty-btn');
        
        // Configurações de dificuldade
        const difficultySettings = {
            easy: {
                targetSize: 80,
                targetSpeed: 1.5,
                targetCount: 1,
                checkInterval: 100, // ms
                pointsPerCheck: 1
            },
            medium: {
                targetSize: 60,
                targetSpeed: 2.5,
                targetCount: 2,
                checkInterval: 100, // ms
                pointsPerCheck: 2
            },
            hard: {
                targetSize: 40,
                targetSpeed: 4,
                targetCount: 3,
                checkInterval: 100, // ms
                pointsPerCheck: 3
            }
        };
        
        // Event listeners
        startButton.addEventListener('click', startCountdown);
        restartButton.addEventListener('click', restartGame);
        restartButtonResult.addEventListener('click', restartGame);
        
        difficultyButtons.forEach(button => {
            button.addEventListener('click', () => {
                difficulty = button.dataset.difficulty;
                
                // Atualizar UI para mostrar a dificuldade selecionada
                difficultyButtons.forEach(btn => {
                    btn.classList.remove('btn-secondary');
                });
                button.classList.add('btn-secondary');
            });
        });
        
        // Movimento do cursor
        arena.addEventListener('mousemove', (e) => {
            const rect = arena.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            crosshair.style.left = `${x}px`;
            crosshair.style.top = `${y}px`;
            
            if (gameActive) {
                checkTargetHover();
            }
        });
        
        // Função para iniciar a contagem regressiva
        function startCountdown() {
            startOverlay.classList.add('hide');
            countdownElement.classList.remove('hide');
            
            let count = 3;
            countdownElement.textContent = count;
            
            const countInterval = setInterval(() => {
                count--;
                
                if (count <= 0) {
                    clearInterval(countInterval);
                    countdownElement.classList.add('hide');
                    startGame();
                } else {
                    countdownElement.textContent = count;
                }
            }, 1000);
        }
        
        // Função para iniciar o jogo
        function startGame() {
            // Resetar variáveis
            gameActive = true;
            score = 0;
            timeLeft = 60;
            trackingTime = 0;
            totalChecks = 0;
            successfulChecks = 0;
            multiplier = 1;
            maxMultiplier = 1;
            
            // Atualizar UI
            updateStats();
            
            // Iniciar timer
            timeInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                progressBar.style.width = `${(timeLeft / 60) * 100}%`;
                
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
            
            // Criar alvos
            const settings = difficultySettings[difficulty];
            for (let i = 0; i < settings.targetCount; i++) {
                createTarget();
            }
            
            // Iniciar verificação de tracking
            const checkInterval = setInterval(() => {
                if (gameActive) {
                    checkTargetHover();
                } else {
                    clearInterval(checkInterval);
                }
            }, settings.checkInterval);
        }
        
        // Função para criar um alvo
        function createTarget() {
            const settings = difficultySettings[difficulty];
            const targetSize = settings.targetSize;
            
            // Calcular posição aleatória
            const arenaRect = arena.getBoundingClientRect();
            const maxX = arenaRect.width - targetSize;
            const maxY = arenaRect.height - targetSize - 5; // Considerando a progress bar
            
            const x = Math.floor(Math.random() * maxX);
            const y = Math.floor(Math.random() * maxY);
            
            // Criar elemento do alvo
            const target = document.createElement('div');
            target.className = 'target';
            target.style.width = `${targetSize}px`;
            target.style.height = `${targetSize}px`;
            target.style.left = `${x}px`;
            target.style.top = `${y}px`;
            target.style.backgroundColor = `rgba(255, 70, 85, 0.8)`;
            
            // Velocidade e direção aleatórias
            const angle = Math.random() * Math.PI * 2;
            const speed = settings.targetSpeed;
            
            target.velocityX = Math.cos(angle) * speed;
            target.velocityY = Math.sin(angle) * speed;
            
            // Adicionar elementos internos
            const inner = document.createElement('div');
            inner.className = 'inner';
            target.appendChild(inner);
            
            const center = document.createElement('div');
            center.className = 'center';
            target.appendChild(center);
            
            // Adicionar ao arena
            arena.appendChild(target);
            
            // Iniciar animação
            animateTarget(target);
        }
        
        // Função para animar um alvo
        function animateTarget(target) {
            if (!gameActive) return;
            
            const arenaRect = arena.getBoundingClientRect();
            const targetRect = target.getBoundingClientRect();
            
            // Atualizar posição
            let x = parseInt(target.style.left);
            let y = parseInt(target.style.top);
            
            x += target.velocityX;
            y += target.velocityY;
            
            // Verificar colisões com bordas
            const maxX = arenaRect.width - targetRect.width;
            const maxY = arenaRect.height - targetRect.height - 5; // Considerando a progress bar
            
            if (x <= 0 || x >= maxX) {
                target.velocityX *= -1;
                x = Math.max(0, Math.min(x, maxX));
            }
            
            if (y <= 0 || y >= maxY) {
                target.velocityY *= -1;
                y = Math.max(0, Math.min(y, maxY));
            }
            
            // Aplicar nova posição
            target.style.left = `${x}px`;
            target.style.top = `${y}px`;
            
            // Continuar animação
            requestAnimationFrame(() => animateTarget(target));
        }
        
        // Função para verificar se o cursor está sobre um alvo
        function checkTargetHover() {
            if (!gameActive) return;
            
            totalChecks++;
            
            const targets = document.querySelectorAll('.target');
            const crosshairRect = crosshair.getBoundingClientRect();
            const crosshairX = crosshairRect.left + crosshairRect.width / 2;
            const crosshairY = crosshairRect.top + crosshairRect.height / 2;
            
            let isTracking = false;
            
            targets.forEach(target => {
                const targetRect = target.getBoundingClientRect();
                
                // Verificar se o centro do crosshair está dentro do alvo
                const centerX = targetRect.left + targetRect.width / 2;
                const centerY = targetRect.top + targetRect.height / 2;
                const radius = targetRect.width / 2;
                
                const distance = Math.sqrt(
                    Math.pow(crosshairX - centerX, 2) + 
                    Math.pow(crosshairY - centerY, 2)
                );
                
                if (distance <= radius) {
                    isTracking = true;
                    target.classList.add('active');
                } else {
                    target.classList.remove('active');
                }
            });
            
            // Atualizar tracking e pontuação
            if (isTracking) {
                successfulChecks++;
                trackingTime += difficultySettings[difficulty].checkInterval / 1000;
                
                // Aumentar multiplicador a cada segundo de tracking contínuo
                if (trackingTime % 1 < 0.1) {
                    multiplier = Math.min(5, Math.floor(trackingTime) + 1);
                    maxMultiplier = Math.max(maxMultiplier, multiplier);
                    updateStats();
                }
                
                // Adicionar pontos
                const pointsEarned = difficultySettings[difficulty].pointsPerCheck * multiplier;
                score += pointsEarned;
            } else {
                // Resetar multiplicador se perder o tracking
                multiplier = 1;
            }
            
            // Calcular precisão
            accuracy = Math.round((successfulChecks / totalChecks) * 100);
            
            // Atualizar UI
            updateStats();
        }
        
        // Função para atualizar estatísticas na UI
        function updateStats() {
            scoreElement.textContent = score;
            accuracyElement.textContent = `${accuracy}%`;
            multiplierElement.textContent = `x${multiplier}`;
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Remover todos os alvos
            document.querySelectorAll('.target').forEach(target => target.remove());
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-accuracy').textContent = `${accuracy}%`;
            document.getElementById('final-multiplier').textContent = `x${maxMultiplier}`;
            document.getElementById('final-tracking-time').textContent = `${trackingTime.toFixed(1)}s`;
            document.getElementById('final-highscore').textContent = isNewHighscore ? 'Sim!' : 'Não';
            
            // Mostrar overlay de resultados
            resultOverlay.classList.remove('hide');
            
            // Salvar pontuação (simulado - na versão final seria via AJAX)
            if (isNewHighscore) {
                fetch('save_score.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `mode=tracking&score=${score}`
                });
            }
        }
        
        // Função para reiniciar o jogo
        function restartGame() {
            // Esconder overlays
            startOverlay.classList.add('hide');
            resultOverlay.classList.add('hide');
            
            // Iniciar contagem regressiva
            startCountdown();
        }
    </script>
</body>
</html> 