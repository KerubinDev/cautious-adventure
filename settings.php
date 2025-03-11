<?php
session_start();

// Verificar se as configurações existem, caso contrário, definir padrões
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'dpi' => 800,
        'sens' => 0.5,
        'edpi' => 400,
        'crosshair_size' => 6,
        'crosshair_color' => '#ffffff',
        'target_color' => '#ff4655',
        'background_color' => '#151f2e',
        'sound_enabled' => true,
        'show_timer' => true,
        'show_feedback' => true,
        'theme' => 'default'
    ];
}

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
        'crosshair' => '#ff99ff',
        'target' => '#ff33ff',
        'background' => '#1a0033'
    ],
    'viper' => [
        'name' => 'Viper',
        'crosshair' => '#ff66ff',
        'target' => '#ff00ff',
        'background' => '#1a0033'
    ],
    'yoru' => [
        'name' => 'Yoru',
        'crosshair' => '#ffccff',
        'target' => '#ff99ff',
        'background' => '#1a0033'
    ]
]; 