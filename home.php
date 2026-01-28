<?php
/**
 * KRStore Moda Masculina - Entrada do Site
 * Redireciona para página de boas-vindas ou catálogo
 */

session_start();

// Verificar se o usuário já visitou a página de boas-vindas
if (!isset($_SESSION['visited_welcome']) && !isset($_COOKIE['skip_welcome'])) {
    // Primeira visita - mostrar boas-vindas
    header('Location: welcome.php');
    exit;
}

// Redirecionar para o catálogo
header('Location: catalog.php');
exit;
