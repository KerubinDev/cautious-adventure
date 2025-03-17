<?php
require_once 'common.php';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_settings'])) {
        // Atualizar configurações
        $_SESSION['settings']['dpi'] = intval($_POST['dpi']);
        $_SESSION['settings']['sens'] = floatval($_POST['sens']);
        $_SESSION['settings']['edpi'] = $_SESSION['settings']['dpi'] * $_SESSION['settings']['sens'];
        $_SESSION['settings']['crosshair_size'] = intval($_POST['crosshair_size']);
        $_SESSION['settings']['crosshair_color'] = $_POST['crosshair_color'];
        $_SESSION['settings']['target_color'] = $_POST['target_color'];
        $_SESSION['settings']['background_color'] = $_POST['background_color'];
        $_SESSION['settings']['sound_enabled'] = isset($_POST['sound_enabled']);
        $_SESSION['settings']['show_timer'] = isset($_POST['show_timer']);
        $_SESSION['settings']['show_feedback'] = isset($_POST['show_feedback']);
        $_SESSION['settings']['theme'] = $_POST['theme'];
        
        // Redirecionar para evitar reenvio de formulário
        header('Location: settings.php?saved=1');
        exit;
    } elseif (isset($_POST['agent_preset'])) {
        // Aplicar predefinição de agente
        $agent = $_POST['agent_preset'];
        
        // Atualizar apenas as cores, mantendo as outras configurações
        if (isset($agent_presets[$agent])) {
            $_SESSION['settings']['crosshair_color'] = $agent_presets[$agent]['crosshair'];
            $_SESSION['settings']['target_color'] = $agent_presets[$agent]['target'];
            $_SESSION['settings']['background_color'] = $agent_presets[$agent]['background'];
            $_SESSION['settings']['theme'] = $agent;
        }
        
        // Redirecionar para evitar reenvio de formulário
        header('Location: settings.php?preset_applied=1');
        exit;
    }
}

// Agentes do Valorant (incluindo os futuros até 2025)
$agent_presets = [
    'default' => [
        'name' => 'Padrão Valorant',
        'crosshair' => '#ffffff',
        'target' => '#ff4655',
        'background' => '#151f2e'
    ],
    // Agentes originais e lançados até 2023
    'astra' => [
        'name' => 'Astra',
        'crosshair' => '#c359ff',
        'target' => '#9c42d8',
        'background' => '#0a0e23'
    ],
    'breach' => [
        'name' => 'Breach',
        'crosshair' => '#ff8a36',
        'target' => '#ff4500',
        'background' => '#2c1911'
    ],
    'brimstone' => [
        'name' => 'Brimstone',
        'crosshair' => '#ff5c33',
        'target' => '#cc3300',
        'background' => '#2d1d15'
    ],
    'chamber' => [
        'name' => 'Chamber',
        'crosshair' => '#eacc85',
        'target' => '#ab9048',
        'background' => '#1c1a24'
    ],
    'cypher' => [
        'name' => 'Cypher',
        'crosshair' => '#bbd6db',
        'target' => '#8c969a',
        'background' => '#1a2133'
    ],
    'deadlock' => [
        'name' => 'Deadlock',
        'crosshair' => '#50acff',
        'target' => '#007bff',
        'background' => '#1b2a36'
    ],
    'fade' => [
        'name' => 'Fade',
        'crosshair' => '#66ccff',
        'target' => '#6236ff',
        'background' => '#1a1a2e'
    ],
    'gekko' => [
        'name' => 'Gekko',
        'crosshair' => '#80ff72',
        'target' => '#40bf40',
        'background' => '#14291f'
    ],
    'harbor' => [
        'name' => 'Harbor',
        'crosshair' => '#33cccc',
        'target' => '#008080',
        'background' => '#122d2e'
    ],
    'jett' => [
        'name' => 'Jett',
        'crosshair' => '#d6f5ff',
        'target' => '#c8e6ff',
        'background' => '#334866'
    ],
    'kayo' => [
        'name' => 'KAY/O',
        'crosshair' => '#80dfff',
        'target' => '#0099ff',
        'background' => '#1a2d40'
    ],
    'killjoy' => [
        'name' => 'Killjoy',
        'crosshair' => '#ffdf4d',
        'target' => '#e6c200',
        'background' => '#252210'
    ],
    'neon' => [
        'name' => 'Neon',
        'crosshair' => '#66ffff',
        'target' => '#c300ff',
        'background' => '#1a1a40'
    ],
    'omen' => [
        'name' => 'Omen',
        'crosshair' => '#8080ff',
        'target' => '#6236ff',
        'background' => '#1a1a33'
    ],
    'phoenix' => [
        'name' => 'Phoenix',
        'crosshair' => '#ffb380',
        'target' => '#ff4500',
        'background' => '#331a00'
    ],
    'raze' => [
        'name' => 'Raze',
        'crosshair' => '#ff9966',
        'target' => '#ff6600',
        'background' => '#331500'
    ],
    'reyna' => [
        'name' => 'Reyna',
        'crosshair' => '#bf80ff',
        'target' => '#9933ff',
        'background' => '#1a0033'
    ],
    'sage' => [
        'name' => 'Sage',
        'crosshair' => '#c2f0c2',
        'target' => '#339933',
        'background' => '#0d260d'
    ],
    'skye' => [
        'name' => 'Skye',
        'crosshair' => '#99ff99',
        'target' => '#33cc33',
        'background' => '#193319'
    ],
    'sova' => [
        'name' => 'Sova',
        'crosshair' => '#80d4ff',
        'target' => '#0080ff',
        'background' => '#132639'
    ],
    'viper' => [
        'name' => 'Viper',
        'crosshair' => '#99ff99',
        'target' => '#00cc00',
        'background' => '#0d260d'
    ],
    'yoru' => [
        'name' => 'Yoru',
        'crosshair' => '#9999ff',
        'target' => '#3333ff',
        'background' => '#161633'
    ],
    // Agentes lançados entre 2023-2025 (fictícios)
    'clove' => [
        'name' => 'Clove',
        'crosshair' => '#e68a8a',
        'target' => '#d14747',
        'background' => '#291111'
    ],
    'iso' => [
        'name' => 'Iso',
        'crosshair' => '#d1d1e0',
        'target' => '#9999cc',
        'background' => '#1f1f33'
    ],
    'nova' => [
        'name' => 'Nova',
        'crosshair' => '#ffcce6',
        'target' => '#ff66cc',
        'background' => '#331a29'
    ],
    'lynx' => [
        'name' => 'Lynx',
        'crosshair' => '#ffd480',
        'target' => '#ff9900',
        'background' => '#332600'
    ],
    'phantom' => [
        'name' => 'Phantom',
        'crosshair' => '#ccccff',
        'target' => '#6666ff',
        'background' => '#19194d'
    ],
    'eclipse' => [
        'name' => 'Eclipse',
        'crosshair' => '#ffccff',
        'target' => '#cc00cc',
        'background' => '#330033'
    ]
];

// Carregar configurações atuais
$settings = $_SESSION['settings'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações Avançadas | Valorant Aim Trainer</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--accent);
            padding-bottom: 1rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .nav {
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
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }
        
        .settings-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
        }
        
        .settings-section {
            background-color: var(--accent);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .settings-section h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        input[type="number"],
        input[type="text"],
        input[type="color"],
        select {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--secondary);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: var(--text);
            font-family: 'Montserrat', sans-serif;
        }
        
        input[type="color"] {
            height: 40px;
            padding: 0.25rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
        }
        
        .agents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .agent-card {
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
            position: relative;
        }
        
        .agent-card:hover {
            transform: scale(1.05);
        }
        
        .agent-card input[type="radio"] {
            display: none;
        }
        
        .agent-card label {
            display: block;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .agent-preview {
            height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .agent-crosshair {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            position: absolute;
            z-index: 2;
        }
        
        .agent-name {
            font-size: 0.8rem;
            text-align: center;
            padding: 0.5rem;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .agent-card input[type="radio"]:checked + .agent-preview::after {
            content: '✓';
            position: absolute;
            top: 5px;
            right: 5px;
            width: 16px;
            height: 16px;
            background-color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        button[type="submit"] {
            background-color: var(--primary);
            color: var(--text);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
        }
        
        button[type="submit"]:hover {
            transform: scale(1.02);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        
        .alert-success {
            background-color: rgba(62, 221, 135, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .crosshair-preview {
            width: 100%;
            height: 150px;
            background-color: var(--secondary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .preview-crosshair {
            position: absolute;
            border-radius: 50%;
            background-color: var(--crosshair-color);
        }
        
        .preview-target {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary);
            position: relative;
        }
        
        .preview-target::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            height: 50%;
            border-radius: 50%;
            background-color: var(--secondary);
        }
        
        footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--accent);
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">Valorant Aim Trainer</div>
            <div class="nav">
                <a href="stats.php" class="btn btn-secondary">Ver Estatísticas</a>
                <a href="index.php" class="btn">Voltar ao Menu</a>
            </div>
        </header>
        
        <h1>Configurações Avançadas</h1>
        
        <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success">
                Configurações salvas com sucesso!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['preset_applied'])): ?>
            <div class="alert alert-success">
                Tema de agente aplicado com sucesso!
            </div>
        <?php endif; ?>
        
        <div class="settings-container">
            <div>
                <!-- Configurações de Sensibilidade -->
                <form method="POST" id="settings-form">
                    <div class="settings-section">
                        <h2>Sensibilidade do Mouse</h2>
                        <div class="form-group">
                            <label for="dpi">DPI do Mouse</label>
                            <input type="number" id="dpi" name="dpi" value="<?= $settings['dpi'] ?>" min="100" max="16000" step="50" required>
                        </div>
                        <div class="form-group">
                            <label for="sens">Sensibilidade do Jogo</label>
                            <input type="number" id="sens" name="sens" value="<?= $settings['sens'] ?>" min="0.1" max="10" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>eDPI Calculado</label>
                            <input type="text" id="edpi" value="<?= $settings['edpi'] ?>" disabled>
                        </div>
                    </div>
                    
                    <!-- Configurações de Interface -->
                    <div class="settings-section">
                        <h2>Interface</h2>
                        <div class="form-group">
                            <label for="crosshair_size">Tamanho do Crosshair</label>
                            <input type="number" id="crosshair_size" name="crosshair_size" value="<?= $settings['crosshair_size'] ?>" min="1" max="20" required>
                        </div>
                        <div class="form-group">
                            <label for="crosshair_color">Cor do Crosshair</label>
                            <input type="color" id="crosshair_color" name="crosshair_color" value="<?= $settings['crosshair_color'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="target_color">Cor dos Alvos</label>
                            <input type="color" id="target_color" name="target_color" value="<?= $settings['target_color'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="background_color">Cor de Fundo</label>
                            <input type="color" id="background_color" name="background_color" value="<?= $settings['background_color'] ?>" required>
                        </div>
                        
                        <div class="crosshair-preview">
                            <div class="preview-crosshair" id="preview-crosshair"></div>
                            <div class="preview-target"></div>
                        </div>
                    </div>
                    
                    <!-- Outras Configurações -->
                    <div class="settings-section">
                        <h2>Outros</h2>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="sound_enabled" name="sound_enabled" <?= $settings['sound_enabled'] ? 'checked' : '' ?>>
                            <label for="sound_enabled">Sons Habilitados</label>
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="show_timer" name="show_timer" <?= $settings['show_timer'] ? 'checked' : '' ?>>
                            <label for="show_timer">Mostrar Temporizador</label>
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="show_feedback" name="show_feedback" <?= $settings['show_feedback'] ? 'checked' : '' ?>>
                            <label for="show_feedback">Mostrar Feedback Visual</label>
                        </div>
                        
                        <input type="hidden" name="theme" id="theme-input" value="<?= $settings['theme'] ?>">
                        <button type="submit" name="save_settings" value="1">Salvar Configurações</button>
                    </div>
                </form>
            </div>
            
            <div>
                <!-- Predefinições de Agentes -->
                <div class="settings-section">
                    <h2>Temas de Agentes</h2>
                    <p>Escolha um agente para aplicar seu esquema de cores ao treinador de mira.</p>
                    
                    <div class="agents-grid" id="agent-grid">
                        <?php foreach ($agent_presets as $key => $agent): ?>
                            <div class="agent-card" data-crosshair="<?= $agent['crosshair'] ?>" data-target="<?= $agent['target'] ?>" data-background="<?= $agent['background'] ?>" data-name="<?= $agent['name'] ?>" data-key="<?= $key ?>">
                                <input type="radio" name="agent_theme" id="agent-<?= $key ?>" value="<?= $key ?>" <?= $settings['theme'] === $key ? 'checked' : '' ?>>
                                <label for="agent-<?= $key ?>" class="agent-preview" style="background-color: <?= $agent['background'] ?>">
                                    <div class="agent-crosshair" style="background-color: <?= $agent['crosshair'] ?>"></div>
                                    <div class="agent-name"><?= $agent['name'] ?></div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" id="apply-theme-btn" class="btn" style="width: 100%; margin-top: 1rem;">Aplicar Tema Selecionado</button>
                </div>
                
                <!-- Informações -->
                <div class="settings-section">
                    <h2>Informações</h2>
                    <p>Use estas configurações para personalizar sua experiência de treinamento de mira. A configuração de eDPI (DPI × Sensibilidade) pode ser ajustada para corresponder à sua configuração no Valorant.</p>
                    <p style="margin-top: 1rem">Os temas de agentes oferecem esquemas de cores inspirados nos personagens do Valorant, proporcionando uma experiência visual única para cada estilo de jogo.</p>
                </div>
            </div>
        </div>
        
        <footer>
            <p>© 2025 Valorant Aim Trainer | Inspirado pelo jogo Valorant da Riot Games</p>
            <p>Este é um projeto não oficial e não tem afiliação com a Riot Games.</p>
        </footer>
    </div>
    
    <script>
        // Pegar todos os elementos necessários
        const crosshairSize = document.getElementById('crosshair_size');
        const crosshairColor = document.getElementById('crosshair_color');
        const targetColor = document.getElementById('target_color');
        const backgroundColor = document.getElementById('background_color');
        const previewCrosshair = document.getElementById('preview-crosshair');
        const previewTarget = document.querySelector('.preview-target');
        const crosshairPreview = document.querySelector('.crosshair-preview');
        const themeInput = document.getElementById('theme-input');
        const settingsForm = document.getElementById('settings-form');
        const applyThemeBtn = document.getElementById('apply-theme-btn');
        const agentCards = document.querySelectorAll('.agent-card');
        
        // Função para atualizar a visualização do crosshair
        function updatePreview() {
            previewCrosshair.style.width = crosshairSize.value + 'px';
            previewCrosshair.style.height = crosshairSize.value + 'px';
            previewCrosshair.style.backgroundColor = crosshairColor.value;
            previewTarget.style.backgroundColor = targetColor.value;
            crosshairPreview.style.backgroundColor = backgroundColor.value;
            
            // Atualizar variáveis CSS
            document.documentElement.style.setProperty('--primary', targetColor.value);
            document.documentElement.style.setProperty('--secondary', backgroundColor.value);
            document.documentElement.style.setProperty('--crosshair-color', crosshairColor.value);
        }
        
        // Função para atualizar com base no tema de agente selecionado
        function updateFromAgentTheme(agentCard) {
            // Obter os valores do tema
            const crosshairColorValue = agentCard.dataset.crosshair;
            const targetColorValue = agentCard.dataset.target;
            const backgroundColorValue = agentCard.dataset.background;
            const themeKey = agentCard.dataset.key;
            
            // Atualizar inputs de cor
            crosshairColor.value = crosshairColorValue;
            targetColor.value = targetColorValue;
            backgroundColor.value = backgroundColorValue;
            
            // Atualizar input de tema
            themeInput.value = themeKey;
            
            // Atualizar visualização
            updatePreview();
            
            // Marcar o radio button
            const radioButton = agentCard.querySelector('input[type="radio"]');
            radioButton.checked = true;
        }
        
        // Event listeners para os controles de cor
        crosshairSize.addEventListener('input', updatePreview);
        crosshairColor.addEventListener('input', updatePreview);
        targetColor.addEventListener('input', updatePreview);
        backgroundColor.addEventListener('input', updatePreview);
        
        // Event listeners para os cards de agente
        agentCards.forEach(card => {
            card.addEventListener('click', () => {
                updateFromAgentTheme(card);
            });
        });
        
        // Event listener para o botão de aplicar tema
        applyThemeBtn.addEventListener('click', function() {
            const checkedRadio = document.querySelector('input[name="agent_theme"]:checked');
            if (checkedRadio) {
                const agentCard = checkedRadio.closest('.agent-card');
                updateFromAgentTheme(agentCard);
                settingsForm.submit(); // Enviar o formulário para salvar as configurações
            }
        });
        
        // Inicializar preview
        updatePreview();

        // Ajuste no manipulador de formulário para recalcular eDPI
        document.getElementById('settings-form').addEventListener('submit', function(e) {
            // Obter valores
            const dpi = parseFloat(document.getElementById('dpi').value);
            const sensitivity = parseFloat(document.getElementById('sens').value);
            
            // Atualizar campo de eDPI
            const edpi = dpi * sensitivity;
            document.getElementById('edpi').value = edpi.toFixed(2);
            
            // O resto do código do manipulador permanece...
        });

        // Adicionar listeners para atualizar eDPI em tempo real
        document.getElementById('dpi').addEventListener('input', updateEDPI);
        document.getElementById('sens').addEventListener('input', updateEDPI);

        function updateEDPI() {
            const dpi = parseFloat(document.getElementById('dpi').value) || 800;
            const sensitivity = parseFloat(document.getElementById('sens').value) || 0.5;
            const edpi = dpi * sensitivity;
            document.getElementById('edpi').value = edpi.toFixed(2);
            document.getElementById('edpi-display').textContent = edpi.toFixed(2);
        }
    </script>
</body>
</html> 