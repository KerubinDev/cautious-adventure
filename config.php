<?php
session_start();

// Salvar configurações de sensibilidade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['dpi'] = (int)$_POST['dpi'];
    $_SESSION['sens'] = (float)$_POST['sens'];
    $_SESSION['edpi'] = $_SESSION['dpi'] * $_SESSION['sens'];
}

// Redirecionar de volta para a página anterior
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
