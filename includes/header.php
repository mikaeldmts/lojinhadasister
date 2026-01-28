<?php
/**
 * Header Component - KRStore
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KRStore Moda Masculina - A melhor loja de roupas masculinas. Camisetas, camisas, calças e bermudas com estilo.">
    <meta name="keywords" content="moda masculina, roupas masculinas, camisetas, camisas, calças, bermudas, streetwear">
    <meta name="author" content="KRStore">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?>">
    <meta property="og:description" content="A melhor loja de moda masculina do Brasil">
    <meta property="og:image" content="https://i.ibb.co/whsVT0pp/unnamed-1.jpg">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:type" content="website">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="https://i.ibb.co/whsVT0pp/unnamed-1.jpg" type="image/jpeg">
    
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
                <img src="https://i.ibb.co/whsVT0pp/unnamed-1.jpg" alt="KRStore Logo">
                <span class="logo-text">KR<span>Store</span></span>
            </a>
            
            <!-- Navigation -->
            <nav class="nav-menu">
                <a href="catalog.php" class="nav-link active">Ínicio</a>
                <a href="catalog.php#camisetas" class="nav-link">Camisetas</a>
                <a href="catalog.php#camisas" class="nav-link">Camisas</a>
                <a href="catalog.php#calcas" class="nav-link">Calças</a>
                <a href="catalog.php#bermudas" class="nav-link">Bermudas</a>
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
