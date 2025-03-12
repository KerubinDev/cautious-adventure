<?php
require_once 'common.php';

// Recuperar highscore se existir
$highscore = $_SESSION['flick_highscore'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Flick | Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <style>
        <?= getThemeCSS() ?>
        
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
            cursor: crosshair;
            overflow: hidden;
        }
        
        .reference-point {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background-color: var(--primary);
            border-radius: 50%;
            opacity: 0.8;
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            background-color: var(--primary);
            box-shadow: 0 0 10px rgba(255, 70, 85, 0.5);
            cursor: pointer;
            transform: scale(0);
            animation: target-appear 0.2s forwards;
        }
        
        @keyframes target-appear {
            to { transform: scale(1); }
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
        
        .hide {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Modo Flick</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Pontuação</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="flicks">0</div>
                    <div class="stat-label">Flicks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="avg-speed">0 ms</div>
                    <div class="stat-label">Vel. Média</div>
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
            <div class="reference-point" id="reference-point"></div>
            <div id="countdown" class="hide"></div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Flick</h2>
                <p>Treine sua capacidade de realizar movimentos rápidos e precisos. Use o ponto de referência e acerte os alvos que aparecem rapidamente.</p>
                
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
                        <span class="score-label">Flicks Realizados:</span>
                        <span class="score-value" id="final-flicks">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo de Reação Médio:</span>
                        <span class="score-value" id="final-reaction-time">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Distância Média:</span>
                        <span class="score-value" id="final-distance">0px</span>
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
        const edpi = <?= $_SESSION['settings']['edpi'] ?? 400 ?>;
        
        // Variáveis do jogo
        let gameActive = false;
        let score = 0;
        let flicks = 0;
        let timeLeft = 60;
        let timeInterval;
        let difficulty = 'medium'; // Padrão
        let flickTimes = [];
        let flickDistances = [];
        let currentTarget = null;
        let targetCreationTime = 0;
        let referencePointPos = { x: 0, y: 0 };
        
        // Elementos DOM
        const arena = document.getElementById('arena');
        const referencePoint = document.getElementById('reference-point');
        const scoreElement = document.getElementById('score');
        const flicksElement = document.getElementById('flicks');
        const avgSpeedElement = document.getElementById('avg-speed');
        const timerElement = document.getElementById('timer');
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
                targetSize: 40,
                targetSpeed: 1500,
                targetSpawnRate: 1200
            },
            medium: {
                targetSize: 30,
                targetSpeed: 1200,
                targetSpawnRate: 1000
            },
            hard: {
                targetSize: 20,
                targetSpeed: 900,
                targetSpawnRate: 800
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
            flicks = 0;
            timeLeft = 60;
            flickTimes = [];
            flickDistances = [];
            
            // Atualizar UI
            updateStats();
            
            // Posicionar ponto de referência
            positionReferencePoint();
            
            // Iniciar timer
            timeInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
            
            // Criar primeiro alvo
            createTarget();
        }
        
        // Função para posicionar o ponto de referência
        function positionReferencePoint() {
            const arenaRect = arena.getBoundingClientRect();
            const x = arenaRect.width / 2;
            const y = arenaRect.height / 2;
            
            referencePointPos = { x, y };
        }
        
        // Função para criar um alvo
        function createTarget() {
            if (!gameActive) return;
            
            const settings = difficultySettings[difficulty];
            const arenaRect = arena.getBoundingClientRect();
            
            // Remover alvo anterior se existir
            if (currentTarget) {
                currentTarget.remove();
            }
            
            // Criar novo alvo
            currentTarget = document.createElement('div');
            currentTarget.className = 'target';
            
            // Calcular posição aleatória (mas não muito perto do ponto de referência)
            let x, y, distance;
            do {
                x = Math.random() * (arenaRect.width - settings.targetSize);
                y = Math.random() * (arenaRect.height - settings.targetSize);
                
                const dx = x - referencePointPos.x;
                const dy = y - referencePointPos.y;
                distance = Math.sqrt(dx * dx + dy * dy);
            } while (distance < 100); // Pelo menos 100px de distância
            
            currentTarget.style.width = `${settings.targetSize}px`;
            currentTarget.style.height = `${settings.targetSize}px`;
            currentTarget.style.left = `${x}px`;
            currentTarget.style.top = `${y}px`;
            
            // Adicionar ao arena
            arena.appendChild(currentTarget);
            
            // Registrar tempo de criação
            targetCreationTime = Date.now();
            
            // Adicionar evento de clique
            currentTarget.addEventListener('click', handleTargetClick);
            
            // Auto-ocultar após tempo definido
            setTimeout(() => {
                if (currentTarget && gameActive) {
                    currentTarget.remove();
                    createTarget();
                }
            }, settings.targetSpeed);
        }
        
        // Função para lidar com clique no alvo
        function handleTargetClick() {
            if (!gameActive) return;
            
            // Calcular tempo de reação
            const reactionTime = Date.now() - targetCreationTime;
            flickTimes.push(reactionTime);
            
            // Calcular distância do flick
            const targetRect = currentTarget.getBoundingClientRect();
            const targetX = targetRect.left + targetRect.width / 2;
            const targetY = targetRect.top + targetRect.height / 2;
            
            const dx = targetX - referencePointPos.x;
            const dy = targetY - referencePointPos.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            flickDistances.push(distance);
            
            // Atualizar pontuação
            flicks++;
            score += 10;
            
            // Atualizar estatísticas
            updateStats();
            
            // Criar próximo alvo
            createTarget();
        }
        
        // Função para atualizar estatísticas na UI
        function updateStats() {
            scoreElement.textContent = score;
            flicksElement.textContent = flicks;
            
            // Calcular tempo médio de reação
            if (flickTimes.length > 0) {
                const avgTime = Math.round(flickTimes.reduce((sum, time) => sum + time, 0) / flickTimes.length);
                avgSpeedElement.textContent = `${avgTime} ms`;
            }
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            if (currentTarget) {
                currentTarget.remove();
                currentTarget = null;
            }
            
            // Calcular estatísticas finais
            const avgReactionTime = flickTimes.length > 0 ? 
                Math.round(flickTimes.reduce((sum, time) => sum + time, 0) / flickTimes.length) : 0;
                
            const avgDistance = flickDistances.length > 0 ?
                Math.round(flickDistances.reduce((sum, dist) => sum + dist, 0) / flickDistances.length) : 0;
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-flicks').textContent = flicks;
            document.getElementById('final-reaction-time').textContent = `${avgReactionTime} ms`;
            document.getElementById('final-distance').textContent = `${avgDistance.toFixed(1)} px`;
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
                    body: `mode=flick&score=${score}`
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