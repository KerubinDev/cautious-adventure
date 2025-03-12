<?php
require_once 'common.php';

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['dpi'])) $_SESSION['dpi'] = 800;
if (!isset($_SESSION['sens'])) $_SESSION['sens'] = 0.5;
if (!isset($_SESSION['edpi'])) $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];

// Recuperar highscore se existir
$highscore = $_SESSION['microadjust_highscore'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Micro Ajustes | Valorant Aim Trainer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap">
    <style>
        <?= getThemeCSS() ?>
        
        :root {
            --primary: #ff4655;
            --secondary: #0f1923;
            --text: #f9f9f9;
            --accent: #28344a;
            --success: #3edd87;
            --warning: #f7c948;
            --error: #ff4655;
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
            cursor: crosshair;
            overflow: hidden;
        }
        
        .crosshair {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--primary);
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 10;
        }
        
        .spray-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 70, 85, 0.3);
            border: 2px solid var(--primary);
            transition: all 0.3s ease;
        }
        
        .target.active {
            background-color: var(--primary);
            transform: scale(1.2);
        }
        
        .target.hit {
            background-color: rgba(0, 255, 100, 0.5);
            border-color: rgba(0, 255, 100, 0.8);
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
        
        .feedback {
            position: absolute;
            font-size: 0.8rem;
            font-weight: bold;
            pointer-events: none;
            opacity: 0;
            animation: fadeUp 1s forwards;
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
        
        .pattern-preview {
            width: 100px;
            height: 100px;
            background-color: rgba(15, 25, 35, 0.7);
            margin: 0 auto 20px;
            position: relative;
            border-radius: 4px;
        }
        
        /* Adicionando estilo para botão de dificuldade selecionado */
        .difficulty-btn.active {
            background-color: var(--primary);
            border: 2px solid white;
        }
        
        /* Garantir que os elementos clicáveis estejam realmente clicáveis */
        .overlay {
            z-index: 1000; /* Garantir que o overlay esteja acima de tudo */
        }
        
        .difficulty-btn, #start-btn {
            position: relative;
            z-index: 1001; /* Garantir que os botões estejam acima do overlay */
            cursor: pointer !important;
            pointer-events: auto !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Modo Micro Ajustes</div>
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="score">0</div>
                    <div class="stat-label">Pontuação</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="precision">0%</div>
                    <div class="stat-label">Precisão</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="targets-hit">0</div>
                    <div class="stat-label">Alvos Acertados</div>
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
            <div class="crosshair" id="crosshair"></div>
            <div class="spray-pattern" id="spray-pattern">
                <!-- Pattern will be created dynamically -->
            </div>
            <div id="countdown" class="hide"></div>
        </div>
        
        <!-- Overlay inicial -->
        <div class="overlay" id="start-overlay">
            <div class="overlay-content">
                <h2>Modo Micro Ajustes</h2>
                <p>Treine seus micro ajustes de mira para melhorar o controle de recoil. Siga o padrão de pontos com movimentos precisos e rápidos.</p>
                
                <div class="pattern-preview" id="pattern-preview">
                    <div class="target" style="width: 20px; height: 20px; background-color: rgba(255, 70, 85, 0.3);"></div>
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
                        <span class="score-label">Alvos Acertados:</span>
                        <span class="score-value" id="final-targets">0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Precisão:</span>
                        <span class="score-value" id="final-precision">0%</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Padrões Completados:</span>
                        <span class="score-value" id="final-patterns">0</span>
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
    
    <div class="countdown hide" id="countdown">3</div>

    <script>
        // Configurações baseadas no PHP
        const edpi = <?= $_SESSION['settings']['edpi'] ?? 400 ?>;
        
        // Variáveis do jogo
        let gameActive = false;
        let score = 0;
        let hits = 0;
        let misses = 0;
        let targetsCreated = 0;
        let timeLeft = 60;
        let timeInterval;
        let difficulty = 'medium'; // Padrão
        let adjustTimes = [];
        let patternsCompleted = 0;
        let currentPattern = [];
        let currentTargetIndex = 0;
        let patternStartTime = 0;
        
        // Elementos DOM
        const arena = document.getElementById('arena');
        const crosshair = document.getElementById('crosshair');
        const sprayPattern = document.getElementById('spray-pattern');
        const scoreElement = document.getElementById('score');
        const precisionElement = document.getElementById('precision');
        const targetsHitElement = document.getElementById('targets-hit');
        const timerElement = document.getElementById('timer');
        const startOverlay = document.getElementById('start-overlay');
        const resultOverlay = document.getElementById('result-overlay');
        const startButton = document.getElementById('start-btn');
        const restartButton = document.getElementById('restart-btn');
        const restartButtonResult = document.getElementById('restart-btn-result');
        const countdownElement = document.getElementById('countdown');
        const difficultyButtons = document.querySelectorAll('.difficulty-btn');
        const patternPreview = document.getElementById('pattern-preview');
        
        // Configurações de dificuldade
        const difficultySettings = {
            easy: {
                pointCount: 5,
                pointSize: 30,
                showTime: 500,
                tolerance: 30
            },
            medium: {
                pointCount: 7,
                pointSize: 25,
                showTime: 400,
                tolerance: 20
            },
            hard: {
                pointCount: 10,
                pointSize: 20,
                showTime: 300,
                tolerance: 15
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
                
                // Atualizar preview
                updatePatternPreview();
            });
        });
        
        // Movimento do mouse para o crosshair
        arena.addEventListener('mousemove', (e) => {
            const arenaRect = arena.getBoundingClientRect();
            const x = e.clientX - arenaRect.left;
            const y = e.clientY - arenaRect.top;
            
            crosshair.style.left = `${x}px`;
            crosshair.style.top = `${y}px`;
            
            if (gameActive && currentPattern.length > 0 && currentTargetIndex < currentPattern.length) {
                checkTargetProximity(x, y);
            }
        });
        
        // Função para atualizar o preview do padrão
        function updatePatternPreview() {
            patternPreview.innerHTML = '';
            
            const settings = difficultySettings[difficulty];
            const previewSize = 100;
            const pointSize = Math.max(10, settings.pointSize / 2);
            
            const point = document.createElement('div');
            point.className = 'target';
            point.style.width = `${pointSize}px`;
            point.style.height = `${pointSize}px`;
            point.style.left = `${previewSize / 2 - pointSize / 2}px`;
            point.style.top = `${previewSize / 2 - pointSize / 2}px`;
            
            patternPreview.appendChild(point);
        }
        
        // Inicializar o preview
        updatePatternPreview();
        
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
            misses = 0;
            targetsCreated = 0;
            timeLeft = 60;
            adjustTimes = [];
            patternsCompleted = 0;
            currentPattern = [];
            currentTargetIndex = 0;
            
            // Limpar qualquer padrão anterior
            sprayPattern.innerHTML = '';
            
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
            
            // Criar primeiro padrão
            createPattern();
        }
        
        // Função para criar um padrão
        function createPattern() {
            if (!gameActive) return;
            
            const settings = difficultySettings[difficulty];
            const arenaRect = arena.getBoundingClientRect();
            
            // Limpar padrão anterior
            sprayPattern.innerHTML = '';
            currentPattern = [];
            currentTargetIndex = 0;
            
            // Ponto central (primeira posição)
            const centerX = arenaRect.width / 2;
            const centerY = arenaRect.height / 2;
            
            // Criar pontos do padrão
            for (let i = 0; i < settings.pointCount; i++) {
                let x, y;
                
                if (i === 0) {
                    // Primeiro ponto é sempre no centro
                    x = centerX;
                    y = centerY;
                } else {
                    // Outros pontos são baseados no anterior, simulando um padrão de recoil
                    const prevPoint = currentPattern[i - 1];
                    const offsetX = (Math.random() - 0.5) * 60;
                    const offsetY = -Math.random() * 60; // Sempre para cima (simulando recoil)
                    
                    x = prevPoint.x + offsetX;
                    y = prevPoint.y + offsetY;
                    
                    // Garantir que está dentro dos limites
                    x = Math.max(settings.pointSize, Math.min(arenaRect.width - settings.pointSize, x));
                    y = Math.max(settings.pointSize, Math.min(arenaRect.height - settings.pointSize, y));
                }
                
                // Adicionar ao padrão
                currentPattern.push({ x, y });
                
                // Criar elemento visual
                const point = document.createElement('div');
                point.className = 'target';
                point.id = `target-${i}`;
                point.style.width = `${settings.pointSize}px`;
                point.style.height = `${settings.pointSize}px`;
                point.style.left = `${x - settings.pointSize / 2}px`;
                point.style.top = `${y - settings.pointSize / 2}px`;
                
                // Tornar invisível inicialmente (exceto o primeiro)
                if (i > 0) {
                    point.style.opacity = '0';
                } else {
                    point.classList.add('active');
                    patternStartTime = Date.now();
                }
                
                sprayPattern.appendChild(point);
            }
            
            targetsCreated += settings.pointCount;
        }
        
        // Função para verificar proximidade do alvo
        function checkTargetProximity(x, y) {
            if (!gameActive || currentTargetIndex >= currentPattern.length) return;
            
            const settings = difficultySettings[difficulty];
            const currentPoint = currentPattern[currentTargetIndex];
            const distance = Math.sqrt(
                Math.pow(x - currentPoint.x, 2) + 
                Math.pow(y - currentPoint.y, 2)
            );
            
            if (distance <= settings.tolerance) {
                // Acertou o alvo!
                const targetElement = document.getElementById(`target-${currentTargetIndex}`);
                targetElement.classList.remove('active');
                targetElement.classList.add('hit');
                
                // Registrar tempo
                if (currentTargetIndex > 0) {
                    const adjustTime = Date.now() - patternStartTime;
                    adjustTimes.push(adjustTime);
                    patternStartTime = Date.now();
                    
                    // Pontuação
                    hits++;
                    score += Math.max(10, Math.round(50 - distance));
                }
                
                // Próximo alvo
                currentTargetIndex++;
                
                // Verificar se o padrão foi concluído
                if (currentTargetIndex >= currentPattern.length) {
                    patternsCompleted++;
                    setTimeout(createPattern, 500);
                } else {
                    // Mostrar próximo ponto
                    const nextTarget = document.getElementById(`target-${currentTargetIndex}`);
                    nextTarget.style.opacity = '1';
                    nextTarget.classList.add('active');
                }
                
                // Atualizar estatísticas
                updateStats();
            }
        }
        
        // Função para atualizar estatísticas na UI
        function updateStats() {
            scoreElement.textContent = score;
            targetsHitElement.textContent = hits;
            
            const accuracy = targetsCreated > 0 ? Math.round((hits / (currentTargetIndex + patternsCompleted * difficultySettings[difficulty].pointCount)) * 100) : 0;
            precisionElement.textContent = `${accuracy}%`;
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Limpar padrão
            sprayPattern.innerHTML = '';
            
            // Calcular estatísticas finais
            const totalTargets = targetsCreated;
            const precision = totalTargets > 0 ? Math.round((hits / totalTargets) * 100) : 0;
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-targets').textContent = hits;
            document.getElementById('final-precision').textContent = `${precision}%`;
            document.getElementById('final-patterns').textContent = patternsCompleted;
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
                    body: `mode=microadjust&score=${score}`
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