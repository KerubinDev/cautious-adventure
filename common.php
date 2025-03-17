<?php
// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar valores padrão se não existirem
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'dpi' => 800,
        'sensitivity' => 0.5,
        'edpi' => 800 * 0.5,
        'crosshair_size' => 2,
        'crosshair_color' => '#ff4655',
        'target_color' => '#00ff00',
        'bg_color' => '#0f1923',
        'accent_color' => '#28344a',
        'text_color' => '#f9f9f9',
        'sound_enabled' => true,
        'timer_display' => true,
        'visual_feedback' => true
    ];
}

// Função para calcular o fator de escala com base na DPI e sensibilidade
function getMouseScaleFactor() {
    $dpi = $_SESSION['settings']['dpi'];
    $sensitivity = $_SESSION['settings']['sensitivity'];
    $edpi = $dpi * $sensitivity;
    
    // Valor de referência: eDPI de 400 (800 DPI * 0.5 sensibilidade)
    $reference_edpi = 400;
    
    // Calcular fator de escala (quanto maior o eDPI, mais rápido o movimento)
    $scale_factor = $edpi / $reference_edpi;
    
    return $scale_factor;
}

// Função para gerar CSS com as variáveis de tema
function getThemeCSS() {
    $css = "
        :root {
            --primary: {$_SESSION['settings']['crosshair_color']};
            --primary-hover: " . adjustBrightness($_SESSION['settings']['crosshair_color'], 20) . ";
            --target: {$_SESSION['settings']['target_color']};
            --secondary: {$_SESSION['settings']['bg_color']};
            --accent: {$_SESSION['settings']['accent_color']};
            --text: {$_SESSION['settings']['text_color']};
            --mouse-scale: " . getMouseScaleFactor() . ";
        }
    ";
    return $css;
}

// Função auxiliar para ajustar brilho de cores
function adjustBrightness($hex, $percent) {
    $hex = ltrim($hex, '#');
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = min(255, max(0, $r + $percent));
    $g = min(255, max(0, $g + $percent));
    $b = min(255, max(0, $b + $percent));
    
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}
?> 