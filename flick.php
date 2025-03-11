<?php
session_start();

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['dpi'])) $_SESSION['dpi'] = 800;
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = 0.5;
if (!isset($_SESSION['edpi'])) $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];

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
                radial-gradient(circle, rgba(255, 70, 85, 0.05) 1px, transparent 1px);
            background-size: 25px 25px;
            cursor: crosshair;
            overflow: hidden;
        }
        
        .reference-point {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 5;
        }
        
        .reference-point::before {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            transition: transform 0.1s;
            animation: pulse 1.5s infinite alternate;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 70, 85, 0.7);
            }
            100% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 70, 85, 0);
            }
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
        
        .target:hover {
            transform: scale(1.05);
        }
        
        .flick-line {
            position: absolute;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.3);
            transform-origin: left center;
            pointer-events: none;
            z-index: 4;
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
        
        .flick-feedback {
            position: absolute;
            font-size: 0.8rem;
            font-weight: bold;
            opacity: 0;
            animation: fadeUp 1s forwards;
            pointer-events: none;
        }
        
        @keyframes fadeUp {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-30px);
            }
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
                    <div class="stat-value" id="avg-speed">0</div>
                    <div class="stat-label">Tempo Médio (ms)</div>
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
            <div id="reference-point" class="reference-point"></div>
            <div id="countdown" class="hide"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
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
                        <span class="score-label">Tempo Médio de Flick:</span>
                        <span class="score-value" id="final-avg-speed">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Flick Mais Rápido:</span>
                        <span class="score-value" id="final-fastest">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Distância Média:</span>
                        <span class="score-value" id="final-avg-distance">0px</span>
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
                targetSize: 65,
                minDistance: 100,
                maxDistance: 300,
                targetInterval: {min: 1000, max: 1500},
                maxFlickTime: 800 // Tempo máximo para pontuação máxima
            },
            medium: {
                targetSize: 50,
                minDistance: 150,
                maxDistance: 400,
                targetInterval: {min: 800, max: 1300},
                maxFlickTime: 600 // Tempo máximo para pontuação máxima
            },
            hard: {
                targetSize: 35,
                minDistance: 200,
                maxDistance: 500,
                targetInterval: {min: 600, max: 1000},
                maxFlickTime: 400 // Tempo máximo para pontuação máxima
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
            
            // Posicionar ponto de referência no centro
            const arenaRect = arena.getBoundingClientRect();
            referencePointPos = {
                x: arenaRect.width / 2,
                y: arenaRect.height / 2
            };
            
            referencePoint.style.left = `${referencePointPos.x}px`;
            referencePoint.style.top = `${referencePointPos.y}px`;
            
            // Iniciar timer
            timeInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                progressBar.style.width = `${(timeLeft / 60) * 100}%`;
                
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
            
            // Iniciar sequência de alvos
            createNewTarget();
        }
        
        // Função para criar um novo alvo
        function createNewTarget() {
            if (!gameActive) return;
            
            // Remover alvo atual se existir
            if (currentTarget && currentTarget.parentNode) {
                currentTarget.remove();
            }
            
            const settings = difficultySettings[difficulty];
            const targetSize = settings.targetSize;
            
            // Calcular posição aleatória em um raio específico
            const arenaRect = arena.getBoundingClientRect();
            
            // Determinar distância aleatória entre min e max
            const distance = Math.random() * (settings.maxDistance - settings.minDistance) + settings.minDistance;
            
            // Determinar ângulo aleatório
            const angle = Math.random() * Math.PI * 2;
            
            // Calcular posição
            let x = referencePointPos.x + Math.cos(angle) * distance;
            let y = referencePointPos.y + Math.sin(angle) * distance;
            
            // Garantir que o alvo não saia da arena
            const margin = targetSize / 2;
            x = Math.max(margin, Math.min(x, arenaRect.width - margin));
            y = Math.max(margin, Math.min(y, arenaRect.height - 5 - margin)); // Considerando a progress bar
            
            // Criar linha de flick
            const flickLine = document.createElement('div');
            flickLine.className = 'flick-line';
            flickLine.style.left = `${referencePointPos.x}px`;
            flickLine.style.top = `${referencePointPos.y}px`;
            
            // Calcular tamanho e rotação da linha
            const dx = x - referencePointPos.x;
            const dy = y - referencePointPos.y;
            const lineLength = Math.sqrt(dx * dx + dy * dy);
            const angle_deg = Math.atan2(dy, dx) * 180 / Math.PI;
            
            flickLine.style.width = `${lineLength}px`;
            flickLine.style.transform = `rotate(${angle_deg}deg)`;
            
            arena.appendChild(flickLine);
            
            // Criar elemento do alvo
            const target = document.createElement('div');
            target.className = 'target';
            target.style.width = `${targetSize}px`;
            target.style.height = `${targetSize}px`;
            target.style.left = `${x - targetSize/2}px`;
            target.style.top = `${y - targetSize/2}px`;
            target.style.backgroundColor = `var(--primary)`;
            
            // Adicionar elementos internos
            const inner = document.createElement('div');
            inner.className = 'inner';
            target.appendChild(inner);
            
            const center = document.createElement('div');
            center.className = 'center';
            target.appendChild(center);
            
            // Configurar evento de clique
            target.addEventListener('click', () => {
                handleFlick(target, x, y, flickLine);
            });
            
            // Adicionar ao arena e armazenar referência
            arena.appendChild(target);
            currentTarget = target;
            targetCreationTime = Date.now();
            
            // Remover a linha após um tempo para não poluir a interface
            setTimeout(() => {
                if (flickLine.parentNode) {
                    flickLine.remove();
                }
            }, 1000);
        }
        
        // Função para lidar com um flick
        function handleFlick(target, targetX, targetY, flickLine) {
            if (!gameActive) return;
            
            // Calcular tempo de flick
            const flickTime = Date.now() - targetCreationTime;
            flickTimes.push(flickTime);
            
            // Calcular distância
            const distance = Math.sqrt(
                Math.pow(targetX - referencePointPos.x, 2) + 
                Math.pow(targetY - referencePointPos.y, 2)
            );
            flickDistances.push(distance);
            
            // Calcular pontuação com base na velocidade
            const settings = difficultySettings[difficulty];
            const maxFlickTime = settings.maxFlickTime;
            
            let pointsEarned;
            if (flickTime <= maxFlickTime) {
                // Pontuação máxima para flicks rápidos
                pointsEarned = 100;
            } else {
                // Pontuação decresce linearmente até um mínimo de 25 pontos
                const maxTime = 1500; // 1.5 segundos
                const timeRatio = Math.min(1, (flickTime - maxFlickTime) / (maxTime - maxFlickTime));
                pointsEarned = Math.max(25, Math.floor(100 - (timeRatio * 75)));
            }
            
            score += pointsEarned;
            flicks++;
            
            // Mostrar feedback visual
            showFlickFeedback(targetX, targetY, flickTime, pointsEarned);
            
            // Remover alvo e linha
            target.remove();
            if (flickLine && flickLine.parentNode) {
                flickLine.remove();
            }
            
            // Atualizar estatísticas
            updateStats();
            
            // Tocar som de acerto
            playHitSound();
            
            // Atualizar posição do ponto de referência para a posição do último alvo
            referencePointPos = { x: targetX, y: targetY };
            referencePoint.style.left = `${referencePointPos.x}px`;
            referencePoint.style.top = `${referencePointPos.y}px`;
            
            // Criar próximo alvo após um intervalo
            const interval = Math.random() * 
                (settings.targetInterval.max - settings.targetInterval.min) + 
                settings.targetInterval.min;
            
            setTimeout(() => {
                if (gameActive) {
                    createNewTarget();
                }
            }, interval);
        }
        
        // Função para mostrar feedback visual sobre o flick
        function showFlickFeedback(x, y, time, points) {
            const feedback = document.createElement('div');
            feedback.className = 'flick-feedback';
            
            // Definir cor baseada na velocidade
            let color;
            if (points >= 90) color = 'var(--success)';
            else if (points >= 50) color = 'var(--warning)';
            else color = 'var(--primary)';
            
            // Definir texto e estilo
            feedback.textContent = `${time}ms (+${points})`;
            feedback.style.color = color;
            feedback.style.left = `${x}px`;
            feedback.style.top = `${y - 30}px`;
            feedback.style.transform = 'translateX(-50%)';
            
            // Adicionar ao arena
            arena.appendChild(feedback);
            
            // Remover após animação
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.remove();
                }
            }, 1000);
        }
        
        // Função para atualizar estatísticas na UI
        function updateStats() {
            scoreElement.textContent = score;
            flicksElement.textContent = flicks;
            
            if (flickTimes.length > 0) {
                const avgTime = Math.round(
                    flickTimes.reduce((sum, time) => sum + time, 0) / flickTimes.length
                );
                avgSpeedElement.textContent = avgTime;
            }
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Remover alvo atual se existir
            if (currentTarget && currentTarget.parentNode) {
                currentTarget.remove();
            }
            
            // Calcular estatísticas finais
            let avgFlickTime = 0;
            let fastestFlick = 0;
            let avgDistance = 0;
            
            if (flickTimes.length > 0) {
                avgFlickTime = Math.round(
                    flickTimes.reduce((sum, time) => sum + time, 0) / flickTimes.length
                );
                fastestFlick = Math.min(...flickTimes);
            }
            
            if (flickDistances.length > 0) {
                avgDistance = Math.round(
                    flickDistances.reduce((sum, dist) => sum + dist, 0) / flickDistances.length
                );
            }
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-flicks').textContent = flicks;
            document.getElementById('final-avg-speed').textContent = `${avgFlickTime}ms`;
            document.getElementById('final-fastest').textContent = `${fastestFlick}ms`;
            document.getElementById('final-avg-distance').textContent = `${avgDistance}px`;
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
        
        // Função para tocar som de acerto
        function playHitSound() {
            const audio = new Audio('data:audio/wav;base64,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
        </script>
    </div>
</body>
</html> 