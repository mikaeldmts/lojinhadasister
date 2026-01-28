<?php
/**
 * Admin Header Include - Lojinha da Irmã
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Lojinha da Irmã</title>
    <link rel="icon" href="https://i.ibb.co/N2HD9wg5/Whats-App-Image-2026-01-28-at-16-03-10.jpg" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Bloquear zoom no admin */
        html, body {
            touch-action: manipulation;
            -ms-touch-action: manipulation;
        }
        input, select, textarea {
            font-size: 16px !important; /* Previne zoom automático em inputs no iOS */
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">
                    <img src="https://i.ibb.co/N2HD9wg5/Whats-App-Image-2026-01-28-at-16-03-10.jpg" alt="Lojinha da Irmã">
                    <span>Lojinha da <b>Irmã</b></span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                        <span class="nav-item-icon">📊</span>
                        <span class="nav-item-text">Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Catálogo</div>
                    <a href="produtos.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'produtos.php' ? 'active' : ''; ?>">
                        <span class="nav-item-icon">📦</span>
                        <span class="nav-item-text">Produtos</span>
                    </a>
                    <a href="produto-form.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'produto-form.php' && !isset($_GET['id']) ? 'active' : ''; ?>">
                        <span class="nav-item-icon">➕</span>
                        <span class="nav-item-text">Novo Produto</span>
                    </a>
                    <a href="categorias.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'categorias.php' ? 'active' : ''; ?>">
                        <span class="nav-item-icon">🏷️</span>
                        <span class="nav-item-text">Categorias</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Sistema</div>
                    <a href="logs.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'logs.php' ? 'active' : ''; ?>">
                        <span class="nav-item-icon">📋</span>
                        <span class="nav-item-text">Logs de Acesso</span>
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <span>🚪</span>
                    <span>Sair</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()" style="display: none;">☰</button>
                    <h1 class="page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                </div>
                
                <div class="topbar-right">
                    <a href="../index.php" target="_blank" class="btn-admin btn-admin-sm btn-admin-secondary">
                        🌐 Ver Loja
                    </a>
                    <div class="admin-user">
                        <div class="admin-avatar">A</div>
                        <span class="admin-name"><?php echo ADMIN_USER; ?></span>
                    </div>
                </div>
            </div>
