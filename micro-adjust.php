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
            background-image: 
                radial-gradient(circle, rgba(255, 70, 85, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            cursor: crosshair;
            overflow: hidden;
        }
        
        .target {
            position: absolute;
            border-radius: 50%;
            transition: transform 0.2s;
            cursor: crosshair;
        }
        
        .target-container {
            position: absolute;
            transform: translate(-50%, -50%);
        }
        
        .target .inner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            height: 50%;
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
        
        .micro-target {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--primary);
            transform: translate(-50%, -50%);
            transition: all 0.2s;
        }
        
        .micro-target.hit {
            background-color: var(--success);
            transform: translate(-50%, -50%) scale(1.5);
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
        
        .spray-pattern {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            border-radius: 4px;
            border: 1px dashed rgba(255, 255, 255, 0.2);
            z-index: 1;
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
            margin: 1rem auto;
            width: 120px;
            height: 120px;
            border-radius: 4px;
            border: 1px dashed rgba(255, 255, 255, 0.2);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .pattern-preview .dot {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 70, 85, 0.8);
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
                    <div class="stat-value" id="targets-hit">0/0</div>
                    <div class="stat-label">Alvos</div>
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
            <div id="spray-pattern" class="spray-pattern"></div>
            <div id="countdown" class="hide"></div>
            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>
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
                    <button type="button" onclick="selectDifficulty('easy', this)" class="difficulty-btn active">Fácil</button>
                    <button type="button" onclick="selectDifficulty('medium', this)" class="difficulty-btn">Médio</button>
                    <button type="button" onclick="selectDifficulty('hard', this)" class="difficulty-btn">Difícil</button>
                </div>
                
                <button type="button" onclick="startTraining()" id="start-btn" class="btn">Iniciar Treino</button>
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
                        <span class="score-label">Precisão:</span>
                        <span class="score-value" id="final-precision">0%</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Alvos Acertados:</span>
                        <span class="score-value" id="final-targets-hit">0/0</span>
                    </div>
                    <div class="score-item">
                        <span class="score-label">Tempo Médio de Ajuste:</span>
                        <span class="score-value" id="final-avg-time">0ms</span>
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
        // Variável global para dificuldade
        let difficulty = 'easy';
        
        // Função para selecionar dificuldade
        function selectDifficulty(diff, button) {
            console.log("Dificuldade selecionada:", diff);
            difficulty = diff;
            
            // Remover classe active de todos os botões
            document.querySelectorAll('.difficulty-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Adicionar classe active ao botão clicado
            button.classList.add('active');
        }
        
        // Função para iniciar o treinamento
        function startTraining() {
            console.log("Iniciando treinamento com dificuldade:", difficulty);
            startCountdown();
        }
        
        // Configurações baseadas no PHP
        const edpi = <?= $_SESSION['settings']['edpi'] ?>;
        
        // Variáveis do jogo
        let gameActive = false;
        let score = 0;
        let hits = 0;
        let misses = 0;
        let targetsCreated = 0;
        let timeLeft = 60;
        let timeInterval;
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
        const progressBar = document.getElementById('progress-bar');
        const startOverlay = document.getElementById('start-overlay');
        const resultOverlay = document.getElementById('result-overlay');
        const countdownElement = document.getElementById('countdown');
        const patternPreview = document.getElementById('pattern-preview');
        
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
            currentTargetIndex = 0;
            
            // Atualizar UI
            updateStats();
            
            // Configurar área de padrão
            const settings = difficultySettings[difficulty];
            sprayPattern.style.width = `${settings.patternSize}px`;
            sprayPattern.style.height = `${settings.patternSize}px`;
            
            // Posicionar no centro
            const arenaRect = arena.getBoundingClientRect();
            sprayPattern.style.left = `${arenaRect.width / 2}px`;
            sprayPattern.style.top = `${arenaRect.height / 2}px`;
            
            // Iniciar timer
            timeInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;
                progressBar.style.width = `${(timeLeft / 60) * 100}%`;
                
                if (timeLeft <= 0) {
                    endGame();
                }
            }, 1000);
            
            // Iniciar padrão
            startNewPattern();
        }
        
        // Função para iniciar um novo padrão
        function startNewPattern() {
            if (!gameActive) return;
            
            // Limpar alvos existentes
            document.querySelectorAll('.micro-target').forEach(target => target.remove());
            
            // Escolher um padrão aleatório
            const patternIndex = Math.floor(Math.random() * recoilPatterns.length);
            const settings = difficultySettings[difficulty];
            
            // Obter o subconjunto de pontos baseado na dificuldade
            currentPattern = recoilPatterns[patternIndex].slice(0, settings.pointCount);
            currentTargetIndex = 0;
            patternStartTime = Date.now();
            
            // Criar o primeiro ponto
            createNextTarget();
        }
        
        // Função para criar o próximo alvo do padrão
        function createNextTarget() {
            if (!gameActive || currentTargetIndex >= currentPattern.length) return;
            
            const settings = difficultySettings[difficulty];
            const point = currentPattern[currentTargetIndex];
            const patternRect = sprayPattern.getBoundingClientRect();
            
            // Calcular posição central do padrão
            const centerX = patternRect.left + patternRect.width / 2;
            const centerY = patternRect.top + patternRect.height / 2;
            
            // Criar micro target
            const target = document.createElement('div');
            target.className = 'micro-target';
            target.style.width = `${settings.pointSize}px`;
            target.style.height = `${settings.pointSize}px`;
            target.style.left = `${centerX + point.x}px`;
            target.style.top = `${centerY + point.y}px`;
            
            // Se for o primeiro alvo, destacá-lo
            if (currentTargetIndex === 0) {
                target.style.width = `${settings.pointSize * 1.5}px`;
                target.style.height = `${settings.pointSize * 1.5}px`;
                target.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
            }
            
            arena.appendChild(target);
            targetsCreated++;
            
            // Configurar timeout para erro
            setTimeout(() => {
                if (target.parentNode && !target.classList.contains('hit')) {
                    target.remove();
                    handleMiss(centerX + point.x, centerY + point.y);
                }
            }, settings.targetTimeout);
        }
        
        // Função para lidar com um acerto
        function handleHit(target, x, y) {
            if (!gameActive) return;
            
            // Calcular tempo de ajuste
            const adjustTime = currentTargetIndex === 0 ? 
                0 : Date.now() - (patternStartTime + currentTargetIndex * difficultySettings[difficulty].pointInterval);
            
            if (adjustTime > 0) {
                adjustTimes.push(adjustTime);
            }
            
            // Marcar alvo como acertado
            target.classList.add('hit');
            hits++;
            
            // Adicionar pontos
            const pointValue = 10 * (currentTargetIndex + 1); // Mais pontos para alvos posteriores na sequência
            score += pointValue;
            
            // Mostrar feedback
            showFeedback(x, y, adjustTime, pointValue, true);
            
            // Tocar som
            playHitSound();
            
            // Atualizar estatísticas
            updateStats();
            
            // Avançar para o próximo alvo ou iniciar novo padrão
            currentTargetIndex++;
            
            if (currentTargetIndex < currentPattern.length) {
                // Próximo alvo após um intervalo
                setTimeout(() => {
                    if (gameActive) createNextTarget();
                }, difficultySettings[difficulty].pointInterval);
            } else {
                // Completou o padrão!
                patternsCompleted++;
                
                // Bônus por completar o padrão
                const patternBonus = 50 * difficulty === 'easy' ? 1 : difficulty === 'medium' ? 2 : 3;
                score += patternBonus;
                
                // Mostrar feedback de padrão completo
                const patternRect = sprayPattern.getBoundingClientRect();
                showFeedback(
                    patternRect.left + patternRect.width / 2, 
                    patternRect.top + patternRect.height / 2, 
                    0, 
                    patternBonus, 
                    true, 
                    'PADRÃO COMPLETO!'
                );
                
                // Iniciar novo padrão após um intervalo
                setTimeout(() => {
                    if (gameActive) startNewPattern();
                }, 1500);
            }
        }
        
        // Função para lidar com um erro
        function handleMiss(x, y) {
            if (!gameActive) return;
            
            misses++;
            
            // Penalidade de pontos
            const penalty = -5;
            score = Math.max(0, score + penalty);
            
            // Mostrar feedback
            showFeedback(x, y, 0, penalty, false);
            
            // Atualizar estatísticas
            updateStats();
            
            // Seguir para o próximo alvo de qualquer forma
            currentTargetIndex++;
            
            if (currentTargetIndex < currentPattern.length) {
                // Próximo alvo após um intervalo
                setTimeout(() => {
                    if (gameActive) createNextTarget();
                }, difficultySettings[difficulty].pointInterval);
            } else {
                // Iniciar novo padrão após um intervalo
                setTimeout(() => {
                    if (gameActive) startNewPattern();
                }, 1500);
            }
        }
        
        // Função para mostrar feedback visual
        function showFeedback(x, y, time, points, isHit, text) {
            const feedback = document.createElement('div');
            feedback.className = 'feedback';
            
            // Configurar cor baseada em acerto/erro
            const color = isHit ? points >= 20 ? 'var(--success)' : 'var(--warning)' : 'var(--error)';
            
            // Configurar texto
            if (text) {
                feedback.textContent = text;
            } else {
                feedback.textContent = `${isHit ? (time > 0 ? time + 'ms ' : '') + '+' : ''}${points}`;
            }
            
            feedback.style.color = color;
            feedback.style.left = `${x}px`;
            feedback.style.top = `${y - 20}px`;
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
            
            const totalAttempts = hits + misses;
            const precision = totalAttempts > 0 ? Math.round((hits / totalAttempts) * 100) : 0;
            precisionElement.textContent = `${precision}%`;
            targetsHitElement.textContent = `${hits}/${targetsCreated}`;
        }
        
        // Função para finalizar o jogo
        function endGame() {
            gameActive = false;
            clearInterval(timeInterval);
            
            // Remover alvos
            document.querySelectorAll('.micro-target').forEach(target => target.remove());
            
            // Calcular estatísticas finais
            const totalAttempts = hits + misses;
            const precision = totalAttempts > 0 ? Math.round((hits / totalAttempts) * 100) : 0;
            
            let avgAdjustTime = 0;
            if (adjustTimes.length > 0) {
                avgAdjustTime = Math.round(
                    adjustTimes.reduce((sum, time) => sum + time, 0) / adjustTimes.length
                );
            }
            
            // Verificar se é um novo recorde
            const oldHighscore = <?= $highscore ?>;
            const isNewHighscore = score > oldHighscore;
            
            // Atualizar overlay de resultados
            document.getElementById('final-score').textContent = score;
            document.getElementById('final-precision').textContent = `${precision}%`;
            document.getElementById('final-targets-hit').textContent = `${hits}/${targetsCreated}`;
            document.getElementById('final-avg-time').textContent = `${avgAdjustTime}ms`;
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
        
        // Função para tocar som de acerto
        function playHitSound() {
            const audio = new Audio('data:audio/wav;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAGDgYtAgAyN+QWaAAihwMWm4G8QQRDiMcCBcH3Cc+CDv/7xA4Tvh9Rz/y8QADBwMWgQAZG/ILNAARQ4GLTcDeIIIhxGOBAuD7hOfBB3/94gcJ3w+o5/5eIAIAAAVwWgQAVQ2ORaIQwEMAJiDg95G4nQL7mQVWI6GwRcfsZAcsKkJvxgxEjzFUgfHoSQ9Qq7KNwqHwuB13MA4a1q/DmBrHgPcmjiGoh//EwC5nGPEmS4RcfkVKOhJf+WOgoxJclFz3kgn//dBA+ya1GhurNn8zb//9NNutNuhz31f////9vt///z+IdAEAAAK4LQIAKobHItEIYCGAExBwe8jcToF9zIKrEdDYIuP2MgOWFSE34wYiR5iqQPj0JIeoVdlG4VD4XA67mAcNa1fhzA1jwHuTRxDUQ//iYBczjHiTJcIuPyKlHQkv/LHQUYkuSi57yQT//uggfZNajQ3Vmz+Zt//+mm3Wm3Q576v////+32///5/EOgAAADVghQAAAAA//uQZAUAB1WI0PZugAAAAAoQwAAAEk3nRd2qAAAAACiDgAAAAAAABCqEEQRLCgwpBGMlJkIz8jKhGvj4k6jzRnqasNKIeoh5gI7BJaC1A1AoNBjJgbyApVS4IDlZgDU5WUAxEKDNmmALHzZp0Fkz1FMTmGFl1FMEyodIavcCAUHDWrKAIA4aa2oCgILEBupZgHvAhEBcZ6joQBxS76AgccrFlczBvKLC0QI2cBoCFvfTDAo7eoOQInqDPBtvrDEZBNYN5xwNwxQRfw8ZQ5wQVLvO8OYU+mHvFLlDh05Mdg7BT6YrRPpCBznMB2r//xKJjyyOh+cImr2/4doscwD6neZjuZR4AgAABYAAAABy1xcdQtxYBYYZdifkU
        </script>
    </body>
</html> 