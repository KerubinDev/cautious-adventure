<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Página Secreta</title>
    <style>
        body { background: black; color: lime; font-family: monospace; }
        @keyframes matrix {
            0% { text-shadow: 0 0 10px lime; }
            50% { text-shadow: 0 0 20px lime; }
            100% { text-shadow: 0 0 10px lime; }
        }
    </style>
</head>
<body>
    <h1 style="animation: matrix 2s infinite">🚨 ALERTA: Modo Hacker Ativado 🚨</h1>
    <p>01000110 01001111 01000100 01000001 00101101 01010011 01000101</p>
    <p><a href="game.php" style="color: cyan">🎲 Iniciar Desafio Secreto</a></p>
    <p><a href="index.php" style="color: yellow">🔓 Sair do Modo Hacker</a></p>
</body>
</html>