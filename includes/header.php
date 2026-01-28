<?php
/**
 * Header Component - Lojinha da Irmã
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lojinha da Irmã - Loja de variedades com produtos exclusivos. Roupas, acessórios, decoração e muito mais!">
    <meta name="keywords" content="loja de variedades, roupas femininas, roupas masculinas, acessórios, decoração, presentes">
    <meta name="author" content="Lojinha da Irmã">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?>">
    <meta property="og:description" content="Loja de variedades com produtos exclusivos">
    <meta property="og:image" content="https://i.ibb.co/N2HD9wg5/Whats-App-Image-2026-01-28-at-16-03-10.jpg">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:type" content="website">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="https://i.ibb.co/N2HD9wg5/Whats-App-Image-2026-01-28-at-16-03-10.jpg" type="image/jpeg">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <a href="catalog.php" class="logo">
                <img src="https://i.ibb.co/N2HD9wg5/Whats-App-Image-2026-01-28-at-16-03-10.jpg" alt="Lojinha da Irmã Logo">
                <span class="logo-text">Lojinha da <span>Irmã</span></span>
            </a>
            
            <!-- Navigation -->
            <nav class="nav-menu">
                <a href="catalog.php" class="nav-link active">Ínicio</a>
                <a href="catalog.php#roupas" class="nav-link">Roupas</a>
                <a href="catalog.php#acessorios" class="nav-link">Acessórios</a>
                <a href="catalog.php#calcados" class="nav-link">Calçados</a>
                <a href="catalog.php#decoracao" class="nav-link">Decoração</a>
            </nav>
            
            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Search -->
                <div class="search-box">
                    <form action="catalog.php" method="GET">
                        <span class="search-icon">🔍</span>
                        <input type="text" name="busca" class="search-input" placeholder="Buscar produtos...">
                    </form>
                </div>
                
                <!-- Instagram -->
                <a href="https://instagram.com/<?php echo INSTAGRAM_USER; ?>" target="_blank" class="btn btn-secondary btn-sm" title="Instagram">
                    📷 @<?php echo INSTAGRAM_USER; ?>
                </a>
                
                <!-- Cart Button -->
                <button class="cart-btn open-cart">
                    🛒 Carrinho
                    <span class="cart-count" style="display: none;">0</span>
                </button>
                
                <!-- Mobile Menu -->
                <button class="mobile-menu-btn">☰</button>
            </div>
        </div>
    </header>
