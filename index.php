<?php
/**
 * KRStore Moda Masculina - Entrada Principal
 * Redireciona para página de boas-vindas na primeira visita
 */

session_start();

// Verificar se o usuário já visitou a página de boas-vindas
if (!isset($_SESSION['visited_welcome']) && !isset($_COOKIE['skip_welcome'])) {
    // Primeira visita - mostrar boas-vindas
    header('Location: welcome.php');
    exit;
}

// Marcar visita e redirecionar para o catálogo
$_SESSION['visited_welcome'] = true;
header('Location: catalog.php');
exit;
