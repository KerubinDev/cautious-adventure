<?php
// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Garantir que a chave 'settings' exista
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [];
}

// Definir valores padrão para todas as chaves necessárias se não existirem
$default_settings = [
    'dpi' => 800,
    'sensitivity' => 0.5,
    'edpi' => 400,
    'crosshair_size' => 2,
    'crosshair_color' => '#ff4655', // Vermelho Valorant
    'target_color' => '#00ff00',
    'bg_color' => '#0f1923',        // Azul escuro Valorant
    'accent_color' => '#28344a',    // Azul médio Valorant
    'text_color' => '#f9f9f9',      // Branco levemente suave
    'sound_enabled' => true,
    'timer_display' => true,
    'visual_feedback' => true
];

// Percorrer todos os valores padrão e garantir que existam na sessão
foreach ($default_settings as $key => $value) {
    if (!isset($_SESSION['settings'][$key])) {
        $_SESSION['settings'][$key] = $value;
    }
}

// Calcular eDPI se não estiver definido ou se DPI/sensibilidade mudaram
if (!isset($_SESSION['settings']['edpi']) || 
    $_SESSION['settings']['edpi'] != $_SESSION['settings']['dpi'] * $_SESSION['settings']['sensitivity']) {
    $_SESSION['settings']['edpi'] = $_SESSION['settings']['dpi'] * $_SESSION['settings']['sensitivity'];
}

// Função para calcular o fator de escala com base na DPI e sensibilidade
function getMouseScaleFactor() {
    // Garantir que os valores existam e sejam numéricos
    $dpi = isset($_SESSION['settings']['dpi']) ? (float)$_SESSION['settings']['dpi'] : 800;
    $sensitivity = isset($_SESSION['settings']['sensitivity']) ? (float)$_SESSION['settings']['sensitivity'] : 0.5;
    $edpi = $dpi * $sensitivity;
    
    // Valor de referência: eDPI de 400 (800 DPI * 0.5 sensibilidade)
    $reference_edpi = 400;
    
    // Calcular fator de escala (quanto maior o eDPI, mais rápido o movimento)
    $scale_factor = $edpi / $reference_edpi;
    
    return $scale_factor;
}

// Função para gerar CSS com as variáveis de tema do Valorant
function getThemeCSS() {
    // Garantir valores padrão para todas as chaves usadas
    $crosshair_color = isset($_SESSION['settings']['crosshair_color']) ? $_SESSION['settings']['crosshair_color'] : '#ff4655';
    $target_color = isset($_SESSION['settings']['target_color']) ? $_SESSION['settings']['target_color'] : '#00ff00';
    $bg_color = isset($_SESSION['settings']['bg_color']) ? $_SESSION['settings']['bg_color'] : '#0f1923';
    $accent_color = isset($_SESSION['settings']['accent_color']) ? $_SESSION['settings']['accent_color'] : '#28344a';
    $text_color = isset($_SESSION['settings']['text_color']) ? $_SESSION['settings']['text_color'] : '#f9f9f9';
    
    $css = "
        :root {
            --primary: {$crosshair_color};
            --primary-hover: " . adjustBrightness($crosshair_color, 20) . ";
            --target: {$target_color};
            --secondary: {$bg_color};
            --accent: {$accent_color};
            --text: {$text_color};
            --mouse-scale: " . getMouseScaleFactor() . ";
        }
    ";
    return $css;
}

// Função auxiliar para ajustar brilho de cores
function adjustBrightness($hex, $percent) {
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) == 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = min(255, max(0, $r + $percent));
    $g = min(255, max(0, $g + $percent));
    $b = min(255, max(0, $b + $percent));
    
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}
?> 