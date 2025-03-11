<?php
session_start();

// Salvar pontuação de acordo com o modo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    $score = (int)($_POST['score'] ?? 0);
    
    // Validar modo
    $validModes = ['precision', 'reflex', 'tracking', 'flick', 'micro-adjust', 'switching'];
    if (in_array($mode, $validModes) && $score > 0) {
        $highscoreKey = "{$mode}_highscore";
        
        // Atualizar recorde se necessário
        if (!isset($_SESSION[$highscoreKey]) || $score > $_SESSION[$highscoreKey]) {
            $_SESSION[$highscoreKey] = $score;
        }
    }
}

// Não é necessário retornar nada, mas poderíamos retornar um JSON com status
echo json_encode(['success' => true]);
?> 