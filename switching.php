<?php
session_start();

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['dpi'])) $_SESSION['dpi'] = 800;
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = 0.5;
if (!isset($_SESSION['edpi'])) $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];

// Recuperar highscore se existir
$highscore = $_SESSION['switching_highscore'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Target Switching | Valorant Aim Trainer</title>
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
            cursor: crosshair;
            transition: transform 0.1s;
        }
        
        .target.active {
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
            z-index: 10;
        }
        
        .target.active::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border: 2px solid rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }
            70% {
                transform: scale(1.1);
                opacity: 0.3;
            }
            100% {
                transform: scale(1);
                opacity: 0.7;
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
            background-color: rgba(15, 25, 35, 0.7);
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
        
        .target-id {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.9rem;
            font-weight: bold;
            color: white;
        }
        
        .switch-line {
            position: absolute;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.3);
            transform-origin: left center;
            z-index: 5;
            pointer-events: none;
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
        
        .feedback {
            position: absolute;
            font-size: 0.9rem;
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
        
        .target-preview {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .preview-target {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary);
            position: relative;
        }
        
        .preview-target .inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 60%;
            border-radius: 50%;
            background-color: rgba(15, 25, 35, 0.7);
        }
        
        .preview-target .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 25%;
            height: 25%;
            border-radius: 50%;
            background-color: var(--primary);
        }
        
        .hide {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Modo Target Switching</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Pontuação</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="switches">0</div>
                    <div class="stat-label">Trocas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="avg-switch-time">0</div>
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
            <div id="countdown" class="hide"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Target Switching</h2>
                <p>Treine sua capacidade de alternar rapidamente entre alvos. Acerte os alvos na ordem marcada. Quanto mais rápido alternar, mais pontos você ganha.</p>
                
                <div class="target-preview">
                    <div class="preview-target">
                        <div class="inner"></div>
                        <div class="center"></div>
                        <div class="target-id">1</div>
                    </div>
                    <div class="preview-target">
                        <div class="inner"></div>
                        <div class="center"></div>
                        <div class="target-id">2</div>
                    </div>
                    <div class="preview-target">
                        <div class="inner"></div>
                        <div class="center"></div>
                        <div class="target-id">3</div>
                    </div>
                </div>
                
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
                        <span class="score-label">Total de Trocas:</span>
                        <span class="score-value" id="final-switches">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo Médio de Troca:</span>
                        <span class="score-value" id="final-avg-switch-time">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Troca Mais Rápida:</span>
                        <span class="score-value" id="final-fastest-switch">0ms</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Sequências Completadas:</span>
                        <span class="score-value" id="final-sequences">0</span>
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
        let switches = 0;
        let switchTimes = [];
        let timeLeft = 60;
        let timeInterval;
        let difficulty = 'medium'; // Padrão
        let sequencesCompleted = 0;
        let targetGroup = [];
        let currentTargetIndex = 0;
        let lastSwitchTime = 0;
        let lastTargetPos = { x: 0, y: 0 };
        
        // Elementos DOM
        const arena = document.getElementById('arena');
        const scoreElement = document.getElementById('score');
        const switchesElement = document.getElementById('switches');
        const avgSwitchTimeElement = document.getElementById('avg-switch-time');
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
                targetCount: 3,
                targetSize: 60,
                minDistance: 150,
                maxDistance: 300,
                minAngleDifference: 35,
                perfectSwitchTime: 600
            },
            medium: {
                targetCount: 4,
                targetSize: 50,
                minDistance: 200,
                maxDistance: 350,
                minAngleDifference: 45,
                perfectSwitchTime: 500
            },
            hard: {
                targetCount: 5,
                targetSize: 40,
                minDistance: 250,
                maxDistance: 400,
                minAngleDifference: 60,
                perfectSwitchTime: 400
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
            switches = 0;
            switchTimes = [];
            timeLeft = 60;
            sequencesCompleted = 0;
            targetGroup = [];
            currentTargetIndex = 0;
            
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
            
            // Iniciar primeiro grupo de alvos
            createTargetGroup();
        }
        
        // Função para criar um grupo de alvos
        function createTargetGroup() {
            if (!gameActive) return;
            
            // Limpar alvos existentes
            targetGroup.forEach(target => {
                if (target.element && target.element.parentNode) {
                    target.element.remove();
                }
            });
            
            // Limpar linhas de switch
            document.querySelectorAll('.switch-line').forEach(line => line.remove());
            
            // Resetar variáveis do grupo
            targetGroup = [];
            currentTargetIndex = 0;
            
            // Configurar com base na dificuldade
            const settings = difficultySettings[difficulty];
            const arenaRect = arena.getBoundingClientRect();
            const centerX = arenaRect.width / 2;
            const centerY = arenaRect.height / 2;
            
            // Criar alvos em posições distribuídas
            for (let i = 0; i < settings.targetCount; i++) {
                // Tenta posicionar de forma que não fique muito próximo dos outros
                let posX, posY, validPosition;
                let attempts = 0;
                
                do {
                    validPosition = true;
                    
                    // Gerar ângulo baseado na posição na sequência, com variação
                    let angle = ((360 / settings.targetCount) * i) + 
                                (Math.random() * 20 - 10);
                    
                    // Converter para radianos
                    angle = angle * Math.PI / 180;
                    
                    // Distância aleatória dentro dos limites
                    const distance = Math.random() * 
                                    (settings.maxDistance - settings.minDistance) + 
                                    settings.minDistance;
                    
                    // Calcular posição
                    posX = centerX + Math.cos(angle) * distance;
                    posY = centerY + Math.sin(angle) * distance;
                    
                    // Verificar se está dentro da arena (considerando margens)
                    const margin = settings.targetSize / 2;
                    if (posX < margin || posX > arenaRect.width - margin ||
                        posY < margin || posY > arenaRect.height - 5 - margin) {
                        validPosition = false;
                    }
                    
                    attempts++;
                } while (!validPosition && attempts < 20);
                
                // Criar target
                const targetElement = document.createElement('div');
                targetElement.className = 'target';
                targetElement.style.width = `${settings.targetSize}px`;
                targetElement.style.height = `${settings.targetSize}px`;
                targetElement.style.left = `${posX - settings.targetSize/2}px`;
                targetElement.style.top = `${posY - settings.targetSize/2}px`;
                targetElement.style.backgroundColor = `var(--primary)`;
                
                // Adicionar elementos internos
                const inner = document.createElement('div');
                inner.className = 'inner';
                targetElement.appendChild(inner);
                
                const center = document.createElement('div');
                center.className = 'center';
                targetElement.appendChild(center);
                
                // Adicionar número do alvo
                const targetId = document.createElement('div');
                targetId.className = 'target-id';
                targetId.textContent = i + 1;
                targetElement.appendChild(targetId);
                
                // Configurar evento de clique
                targetElement.addEventListener('click', (e) => {
                    handleTargetClick(i, e);
                });
                
                // Adicionar ao arena
                arena.appendChild(targetElement);
                
                // Guardar referência
                targetGroup.push({
                    id: i,
                    element: targetElement,
                    x: posX,
                    y: posY
                });
            }
            
            // Desenhar linhas de conexão
            drawSwitchLines();
            
            // Ativar o primeiro alvo
            if (targetGroup.length > 0) {
                targetGroup[0].element.classList.add('active');
                lastSwitchTime = Date.now();
                lastTargetPos = { x: targetGroup[0].x, y: targetGroup[0].y };
            }
        }
        
        // Função para desenhar linhas conectando os alvos
        function drawSwitchLines() {
            for (let i = 0; i < targetGroup.length - 1; i++) {
                const current = targetGroup[i];
                const next = targetGroup[i + 1];
                
                drawSwitchLine(current.x, current.y, next.x, next.y);
            }
        }
        
        // Função para desenhar uma linha entre dois pontos
        function drawSwitchLine(x1, y1, x2, y2) {
            const line = document.createElement('div');
            line.className = 'switch-line';
            
            // Calcular tamanho e rotação da linha
            const dx = x2 - x1;
            const dy = y2 - y1;
            const length = Math.sqrt(dx * dx + dy * dy);
            const angle = Math.atan2(dy, dx) * 180 / Math.PI;
            
            line.style.width = `${length}px`;
            line.style.left = `${x1}px`;
            line.style.top = `${y1}px`;
            line.style.transform = `rotate(${angle}deg)`;
            
            arena.appendChild(line);
        }
        
        // Função para lidar com clique em um alvo
        function handleTargetClick(targetId, event) {
            if (!gameActive) return;
            
            // Verificar se é o alvo correto
            if (targetId === currentTargetIndex) {
                const target = targetGroup[targetId];
                
                // Calcular tempo de troca (não aplicável ao primeiro alvo)
                if (targetId > 0) {
                    const switchTime = Date.now() - lastSwitchTime;
                    switchTimes.push(switchTime);
                    switches++;
                    
                    // Calcular pontuação baseada na velocidade
                    const settings = difficultySettings[difficulty];
                    const perfectTime = settings.perfectSwitchTime;
                    
                    let pointsEarned;
                    if (switchTime <= perfectTime) {
                        // Pontuação máxima para switches rápidos
                        pointsEarned = 30;
                    } else {
                        // Pontuação decresce linearmente até um mínimo de 10 pontos
                        const maxPenaltyTime = 1200; // 1.2 segundos
                        const timeRatio = Math.min(1, (switchTime - perfectTime) / (maxPenaltyTime - perfectTime));
                        pointsEarned = Math.max(10, Math.floor(30 - (timeRatio * 20)));
                    }
                    
                    score += pointsEarned;
                    
                    // Mostrar feedback
                    showFeedback(target.x, target.y, switchTime, pointsEarned);
                    
                    // Desenhar linha de conexão real (trajetória do mouse)
                    const mouseX = event.clientX - arena.getBoundingClientRect().left;
                    const mouseY = event.clientY - arena.getBoundingClientRect().top;
                    const actualLine = document.createElement('div');
                    actualLine.className = 'switch-line';
                    actualLine.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
                    actualLine.style.zIndex = '2';
                    
                    // Calcular tamanho e rotação
                    const dx = mouseX - lastTargetPos.x;
                    const dy = mouseY - lastTargetPos.y;
                    const length = Math.sqrt(dx * dx + dy * dy);
                    const angle = Math.atan2(dy, dx) * 180 / Math.PI;
                    
                    actualLine.style.width = `${length}px`;
                    actualLine.style.left = `${lastTargetPos.x}px`;
                    actualLine.style.top = `${lastTargetPos.y}px`;
                    actualLine.style.transform = `rotate(${angle}deg)`;
                    
                    arena.appendChild(actualLine);
                    
                    // Remover após um tempo
                    setTimeout(() => {
                        if (actualLine.parentNode) actualLine.remove();
                    }, 800);
                } else {
                    // Pontos pelo primeiro alvo
                    score += 10;
                    showFeedback(target.x, target.y, 0, 10);
                }
                
                // Atualizar estatísticas
                updateStats();
                
                // Tocar som
                playHitSound();
                
                // Desativar alvo atual
                target.element.classList.remove('active');
                
                // Atualizar posição do último alvo
                lastTargetPos = { x: target.x, y: target.y };
                
                // Avançar para o próximo alvo ou criar novo grupo
                currentTargetIndex++;
                lastSwitchTime = Date.now();
                
                if (currentTargetIndex < targetGroup.length) {
                    // Ativar próximo alvo
                    targetGroup[currentTargetIndex].element.classList.add('active');
                } else {
                    // Completou o grupo
                    sequencesCompleted++;
                    
                    // Bônus por completar a sequência
                    const bonusPoints = 40;
                    score += bonusPoints;
                    
                    // Mostrar feedback
                    const arenaRect = arena.getBoundingClientRect();
                    showFeedback(
                        arenaRect.width / 2, 
                        arenaRect.height / 2, 
                        0, 
                        bonusPoints, 
                        'SEQUÊNCIA COMPLETADA!'
                    );
                    
                    // Novo grupo após um intervalo
                    setTimeout(() => {
                        if (gameActive) createTargetGroup();
                    }, 1000);
                }
            }
        }
        
        // Função para mostrar feedback visual
        function showFeedback(x, y, time, points, customText) {
            const feedback = document.createElement('div');
            feedback.className = 'feedback';
            
            // Definir cor baseada na velocidade
            let color;
            if (points >= 25) color = 'var(--success)';
            else if (points >= 15) color = 'var(--warning)';
            else color = 'var(--primary)';
            
            // Definir texto
            if (customText) {
                feedback.textContent = customText;
                feedback.style.fontSize = '1.2rem';
            } else {
                feedback.textContent = `${time > 0 ? time + 'ms ' : ''}+${points}`;
            }
            
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
            switchesElement.textContent = switches;
            
            if (switchTimes.length > 0) {
                const avgTime = Math.round(
                    switchTimes.reduce((sum, time) => sum + time, 0) / switchTimes.length
                );
                avgSwitchTimeElement.textContent = avgTime;
            }
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Remover alvos e linhas
            targetGroup.forEach(target => {
                if (target.element && target.element.parentNode) {
                    target.element.remove();
                }
            });
            document.querySelectorAll('.switch-line').forEach(line => line.remove());
            
            // Calcular estatísticas finais
            let avgSwitchTime = 0;
            let fastestSwitch = 0;
            
            if (switchTimes.length > 0) {
                avgSwitchTime = Math.round(
                    switchTimes.reduce((sum, time) => sum + time, 0) / switchTimes.length
                );
                fastestSwitch = Math.min(...switchTimes);
            }
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-switches').textContent = switches;
            document.getElementById('final-avg-switch-time').textContent = `${avgSwitchTime}ms`;
            document.getElementById('final-fastest-switch').textContent = `${fastestSwitch}ms`;
            document.getElementById('final-sequences').textContent = sequencesCompleted;
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
                    body: `mode=switching&score=${score}`
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
            const audio = new Audio('data:audio/wav;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAGDgYtAgAyN+QWaAAihwMWm4G8QQRDiMcCBcH3Cc+CDv/7xA4Tvh9Rz/y8QADBwMWgQAZG/ILNAARQ4GLTcDeIIIhxGOBAuD7hOfBB3/94gcJ3w+o5/5eIAIAAAVwWgQAVQ2ORaIQwEMAJiDg95G4nQL7mQVWI6GwRcfsZAcsKkJvxgxEjzFUgfHoSQ9Qq7KNwqHwuB13MA4a1q/DmBrHgPcmjiGoh//EwC5nGPEmS4RcfkVKOhJf+WOgoxJclFz3kgn//dBA+ya1GhurNn8zb//9NNutNuhz31f////9vt///z+IdAEAAAK4LQIAKobHItEIYCGAExBwe8jcToF9zIKrEdDYIuP2MgOWFSE34wYiR5iqQPj0JIeoVdlG4VD4XA67mAcNa1fhzA1jwHuTRxDUQ//iYBczjHiTJcIuPyKlHQkv/LHQUYkuSi57yQT//uggfZNajQ3Vmz+Zt//+mm3Wm3Q576v////+32///5/EOgA');
        }
    </script>
</body>
</html> 