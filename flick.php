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
        
        .target {
            position: absolute;
            border-radius: 50%;
            cursor: pointer;
            transform: scale(0);
            animation: target-appear 0.2s forwards;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: var(--secondary);
        }
        
        .target.first {
            background-color: #4CAF50; /* Verde para o primeiro alvo */
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }
        
        .target.second {
            background-color: var(--primary); /* Vermelho para o segundo alvo */
            box-shadow: 0 0 10px rgba(255, 70, 85, 0.5);
            display: none; /* Oculto inicialmente */
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
            <div id="countdown" class="hide"></div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Flick</h2>
                <p>Treine sua capacidade de realizar movimentos rápidos e precisos. Clique no alvo VERDE e rapidamente "flike" para o alvo VERMELHO quando ele aparecer.</p>
                
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
        let firstTarget = null;
        let secondTarget = null;
        let firstClickTime = 0;
        let firstTargetPos = { x: 0, y: 0 };
        let targetPairId = 0;
        
        // Elementos DOM
        const arena = document.getElementById('arena');
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
                targetSize: 50,
                minDistance: 100,
                maxDistance: 300,
                flickTimeout: 2000
            },
            medium: {
                targetSize: 40,
                minDistance: 150,
                maxDistance: 400,
                flickTimeout: 1500
            },
            hard: {
                targetSize: 30,
                minDistance: 200,
                maxDistance: 500,
                flickTimeout: 1000
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
            targetPairId = 0;
            
            // Limpar alvos anteriores
            if (firstTarget) firstTarget.remove();
            if (secondTarget) secondTarget.remove();
            firstTarget = null;
            secondTarget = null;
            
            // Atualizar UI
            updateStats();
            
            // Iniciar timer
            timeInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
            
            // Criar primeiro par de alvos
            createTargetPair();
        }
        
        // Função para criar um par de alvos
        function createTargetPair() {
            if (!gameActive) return;
            
            // Limpar alvos anteriores se existirem
            if (firstTarget) firstTarget.remove();
            if (secondTarget) secondTarget.remove();
            
            const settings = difficultySettings[difficulty];
            const arenaRect = arena.getBoundingClientRect();
            
            // Calcular tamanho ajustado dos alvos com base na dificuldade
            const targetSize = settings.targetSize;
            
            // Gerar ID único para este par
            targetPairId++;
            
            // Criar primeiro alvo (VERDE)
            firstTarget = document.createElement('div');
            firstTarget.className = 'target first';
            firstTarget.id = `target-first-${targetPairId}`;
            firstTarget.textContent = '1';
            
            // Posição aleatória para o primeiro alvo
            const firstX = Math.random() * (arenaRect.width - targetSize * 2) + targetSize;
            const firstY = Math.random() * (arenaRect.height - targetSize * 2) + targetSize;
            
            firstTarget.style.width = `${targetSize}px`;
            firstTarget.style.height = `${targetSize}px`;
            firstTarget.style.left = `${firstX}px`;
            firstTarget.style.top = `${firstY}px`;
            
            // Guardar posição para calcular distância do flick depois
            firstTargetPos = { x: firstX + targetSize/2, y: firstY + targetSize/2 };
            
            // Adicionar evento de clique ao primeiro alvo
            firstTarget.addEventListener('click', handleFirstTargetClick);
            
            // Criar segundo alvo (VERMELHO - oculto inicialmente)
            secondTarget = document.createElement('div');
            secondTarget.className = 'target second';
            secondTarget.id = `target-second-${targetPairId}`;
            secondTarget.textContent = '2';
            
            // Posição para o segundo alvo (distância controlada)
            let secondX, secondY, distance;
            
            do {
                // Ângulo aleatório
                const angle = Math.random() * Math.PI * 2;
                // Distância aleatória entre min e max
                distance = settings.minDistance + Math.random() * (settings.maxDistance - settings.minDistance);
                
                secondX = firstX + Math.cos(angle) * distance;
                secondY = firstY + Math.sin(angle) * distance;
                
                // Verificar limites da arena
                if (secondX < targetSize) secondX = targetSize;
                if (secondX > arenaRect.width - targetSize) secondX = arenaRect.width - targetSize;
                if (secondY < targetSize) secondY = targetSize;
                if (secondY > arenaRect.height - targetSize) secondY = arenaRect.height - targetSize;
                
                // Recalcular distância após ajuste
                const dx = secondX - firstX;
                const dy = secondY - firstY;
                distance = Math.sqrt(dx * dx + dy * dy);
                
            } while (distance < settings.minDistance); // Garantir distância mínima
            
            secondTarget.style.width = `${targetSize}px`;
            secondTarget.style.height = `${targetSize}px`;
            secondTarget.style.left = `${secondX}px`;
            secondTarget.style.top = `${secondY}px`;
            
            // Adicionar evento de clique ao segundo alvo
            secondTarget.addEventListener('click', handleSecondTargetClick);
            
            // Adicionar alvos à arena
            arena.appendChild(firstTarget);
            arena.appendChild(secondTarget);
        }
        
        // Função para lidar com clique no primeiro alvo
        function handleFirstTargetClick() {
            if (!gameActive) return;
            
            // Registrar tempo do clique
            firstClickTime = Date.now();
            
            // Mostrar o segundo alvo
            secondTarget.style.display = 'flex';
            
            // Iniciar timeout para o flick
            const settings = difficultySettings[difficulty];
            setTimeout(() => {
                // Se o segundo alvo ainda estiver visível, o usuário não conseguiu acertar a tempo
                if (secondTarget && secondTarget.style.display !== 'none' && gameActive) {
                    // Criar novo par de alvos
                    createTargetPair();
                }
            }, settings.flickTimeout);
        }
        
        // Função para lidar com clique no segundo alvo
        function handleSecondTargetClick() {
            if (!gameActive || !firstClickTime) return;
            
            // Calcular tempo do flick
            const flickTime = Date.now() - firstClickTime;
            flickTimes.push(flickTime);
            
            // Calcular distância do flick
            const targetRect = secondTarget.getBoundingClientRect();
            const targetX = targetRect.left + targetRect.width / 2;
            const targetY = targetRect.top + targetRect.height / 2;
            
            const dx = targetX - firstTargetPos.x;
            const dy = targetY - firstTargetPos.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            flickDistances.push(distance);
            
            // Pontuação com bônus por velocidade
            flicks++;
            
            // Bônus por velocidade: quanto mais rápido, mais pontos
            const settings = difficultySettings[difficulty];
            const speedBonus = Math.max(0, Math.round(10 * (1 - flickTime / settings.flickTimeout)));
            score += 10 + speedBonus;
            
            // Atualizar estatísticas
            updateStats();
            
            // Esconder o segundo alvo
            secondTarget.style.display = 'none';
            
            // Criar próximo par de alvos
            setTimeout(createTargetPair, 300);
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
            
            // Limpar alvos
            if (firstTarget) firstTarget.remove();
            if (secondTarget) secondTarget.remove();
            firstTarget = null;
            secondTarget = null;
            
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