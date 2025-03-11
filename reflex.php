<?php
session_start();

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['dpi'])) $_SESSION['dpi'] = 800;
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = 0.5;
if (!isset($_SESSION['edpi'])) $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];

// Recuperar highscore se existir
$highscore = $_SESSION['reflex_highscore'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Reflexo | Valorant Aim Trainer</title>
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
            background-size: 20px 20px;
            cursor: crosshair;
            overflow: hidden;
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            transition: transform 0.1s, opacity 0.2s;
            opacity: 0;
            transform: scale(0.8);
        }
        
        .target.visible {
            opacity: 1;
            transform: scale(1);
        }
        
        .target .inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40%;
            height: 40%;
            border-radius: 50%;
            background-color: var(--secondary);
        }
        
        .target .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 15%;
            height: 15%;
            border-radius: 50%;
            background-color: var(--primary);
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
        
        .reaction-display {
            position: absolute;
            font-weight: bold;
            font-size: 1.2rem;
            opacity: 0;
            transition: opacity 0.2s, transform 0.5s;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.7);
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
        
        /* Barra de progresso para tempo restante */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Modo Reflexo</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Pontuação</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="hits">0</div>
                    <div class="stat-label">Acertos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="avg-reaction">0</div>
                    <div class="stat-label">Reação Média (ms)</div>
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
            <div id="countdown" class="hide"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Reflexo</h2>
                <p>Treine seus reflexos com alvos que aparecem rapidamente. Quanto mais rápido você reagir, mais pontos ganha.</p>
                
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
                        <span class="score-label">Acertos:</span>
                        <span class="score-value" id="final-hits">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Taxa de Acerto:</span>
                        <span class="score-value" id="final-hit-rate">0%</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo de Reação Médio:</span>
                        <span class="score-value" id="final-avg-reaction">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo de Reação Mais Rápido:</span>
                        <span class="score-value" id="final-fastest-reaction">0ms</span>
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
        let hits = 0;
        let missed = 0;
        let timeLeft = 60;
        let timeInterval;
        let reactionTimes = [];
        let difficulty = 'medium'; // Padrão
        let currentTarget = null;
        let targetAppearTime = 0;
        
        // Elementos DOM
        const arena = document.getElementById('arena');
        const scoreElement = document.getElementById('score');
        const hitsElement = document.getElementById('hits');
        const avgReactionElement = document.getElementById('avg-reaction');
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
                appearDuration: {min: 1200, max: 2000},
                waitDuration: {min: 900, max: 1700},
                perfectTime: 350 // Tempo para pontuação perfeita (ms)
            },
            medium: {
                targetSize: 50,
                appearDuration: {min: 900, max: 1600},
                waitDuration: {min: 700, max: 1500},
                perfectTime: 300 // Tempo para pontuação perfeita (ms)
            },
            hard: {
                targetSize: 35,
                appearDuration: {min: 600, max: 1200},
                waitDuration: {min: 500, max: 1200},
                perfectTime: 250 // Tempo para pontuação perfeita (ms)
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
            hits = 0;
            missed = 0;
            timeLeft = 60;
            reactionTimes = [];
            
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
            
            // Iniciar sequência de alvos
            spawnTargetSequence();
        }
        
        // Função para criar a sequência de alvos
        function spawnTargetSequence() {
            if (!gameActive) return;
            
            const settings = difficultySettings[difficulty];
            
            // Calcular tempos aleatórios
            const appearDuration = Math.floor(
                Math.random() * (settings.appearDuration.max - settings.appearDuration.min) + 
                settings.appearDuration.min
            );
            
            const waitDuration = Math.floor(
                Math.random() * (settings.waitDuration.max - settings.waitDuration.min) + 
                settings.waitDuration.min
            );
            
            // Criar e posicionar o alvo
            setTimeout(() => {
                if (!gameActive) return;
                createTarget();
                
                // Agendar próximo alvo
                setTimeout(() => {
                    if (!gameActive) return;
                    if (currentTarget && currentTarget.parentNode) {
                        missed++;
                        currentTarget.remove();
                        currentTarget = null;
                    }
                    spawnTargetSequence();
                }, appearDuration);
            }, waitDuration);
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
            target.style.backgroundColor = `var(--primary)`;
            
            // Adicionar elementos internos
            const inner = document.createElement('div');
            inner.className = 'inner';
            target.appendChild(inner);
            
            const center = document.createElement('div');
            center.className = 'center';
            target.appendChild(center);
            
            // Configurar evento de clique
            target.addEventListener('click', (e) => {
                e.stopPropagation();
                const reactionTime = Date.now() - targetAppearTime;
                handleHit(target, reactionTime);
            });
            
            // Adicionar ao arena
            arena.appendChild(target);
            currentTarget = target;
            
            // Tornar visível com um pequeno delay para o efeito de animação
            setTimeout(() => {
                if (target.parentNode) {
                    target.classList.add('visible');
                    targetAppearTime = Date.now();
                }
            }, 50);
        }
        
        // Função para lidar com acerto
        function handleHit(target, reactionTime) {
            if (!gameActive) return;
            
            hits++;
            reactionTimes.push(reactionTime);
            
            // Calcular pontuação baseada na velocidade de reação
            const settings = difficultySettings[difficulty];
            const perfectTime = settings.perfectTime;
            
            let pointsEarned;
            if (reactionTime <= perfectTime) {
                // Pontuação máxima para reações super rápidas
                pointsEarned = 100;
            } else {
                // Pontuação decresce linearmente até um mínimo de 25 pontos
                const maxPenaltyTime = 1000; // 1 segundo
                const penaltyRatio = Math.min(1, (reactionTime - perfectTime) / (maxPenaltyTime - perfectTime));
                pointsEarned = Math.max(25, Math.floor(100 - (penaltyRatio * 75)));
            }
            
            score += pointsEarned;
            
            // Mostrar feedback visual
            showReactionFeedback(target, reactionTime, pointsEarned);
            
            // Efeito visual e som
            playHitSound();
            
            // Atualizar estatísticas
            updateStats();
            
            // Remover alvo
            target.remove();
            currentTarget = null;
        }
        
        // Mostrar feedback visual da reação
        function showReactionFeedback(target, reactionTime, points) {
            const feedbackEl = document.createElement('div');
            feedbackEl.className = 'reaction-display';
            
            // Definir cor baseada na velocidade
            let color;
            if (points >= 90) color = 'var(--success)';
            else if (points >= 50) color = 'var(--warning)';
            else color = 'var(--primary)';
            
            // Definir texto e estilo
            feedbackEl.textContent = `${reactionTime}ms (+${points})`;
            feedbackEl.style.color = color;
            
            // Posicionar próximo ao alvo
            const rect = target.getBoundingClientRect();
            const arenaRect = arena.getBoundingClientRect();
            
            feedbackEl.style.left = `${rect.left - arenaRect.left + (rect.width / 2)}px`;
            feedbackEl.style.top = `${rect.top - arenaRect.top - 20}px`;
            feedbackEl.style.transform = 'translate(-50%, 0)';
            
            // Adicionar ao arena
            arena.appendChild(feedbackEl);
            
            // Animar e remover
            feedbackEl.style.animation = 'fadeUp 1s forwards';
            setTimeout(() => {
                if (feedbackEl.parentNode) {
                    feedbackEl.remove();
                }
            }, 1000);
        }
        
        // Função para atualizar estatísticas na UI
        function updateStats() {
            scoreElement.textContent = score;
            hitsElement.textContent = hits;
            
            // Calcular e exibir tempo de reação médio
            if (reactionTimes.length > 0) {
                const avgReaction = Math.round(
                    reactionTimes.reduce((sum, time) => sum + time, 0) / reactionTimes.length
                );
                avgReactionElement.textContent = avgReaction;
            } else {
                avgReactionElement.textContent = '0';
            }
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Remover target atual se existir
            if (currentTarget && currentTarget.parentNode) {
                currentTarget.remove();
                currentTarget = null;
            }
            
            // Calcular estatísticas finais
            const totalAttempts = hits + missed;
            const hitRate = totalAttempts > 0 ? Math.round((hits / totalAttempts) * 100) : 0;
            
            let avgReaction = 0;
            let fastestReaction = 0;
            
            if (reactionTimes.length > 0) {
                avgReaction = Math.round(
                    reactionTimes.reduce((sum, time) => sum + time, 0) / reactionTimes.length
                );
                fastestReaction = Math.min(...reactionTimes);
            }
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-hits').textContent = hits;
            document.getElementById('final-hit-rate').textContent = `${hitRate}%`;
            document.getElementById('final-avg-reaction').textContent = `${avgReaction}ms`;
            document.getElementById('final-fastest-reaction').textContent = `${fastestReaction}ms`;
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
                    body: `mode=reflex&score=${score}`
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
            const audio = new Audio('data:audio/wav;base64,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');
        }
    </script>
</body>
</html>
