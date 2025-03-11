<?php
// Iniciar sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Certificar que eDPI esteja atualizado
$_SESSION['settings']['edpi'] = $_SESSION['settings']['dpi'] * $_SESSION['settings']['sens'];

// Função para gerar as variáveis CSS com base nas configurações
function getThemeCSS() {
    $settings = $_SESSION['settings'];
    
    return "
        :root {
            --primary: {$settings['target_color']};
            --secondary: {$settings['background_color']};
            --crosshair-color: {$settings['crosshair_color']};
            --text: #f9f9f9;
            --accent: #28344a;
            --accent-light: #3a4a66;
            --success: #3edd87;
            --warning: #f7c948;
        }
    ";
}
?> 