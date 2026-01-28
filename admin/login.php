<?php
/**
 * Admin Login - KRStore
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';

// Se já está logado, redirecionar
if (Auth::isLogged()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Preencha todos os campos.';
    } elseif (Auth::login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Admin KRStore</title>
    <link rel="icon" href="https://i.ibb.co/whsVT0pp/unnamed-1.jpg" type="image/jpeg">
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
            font-size: 16px !important;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <img src="https://i.ibb.co/whsVT0pp/unnamed-1.jpg" alt="KRStore Logo" class="login-logo">
                <h1 class="login-title">Painel Admin</h1>
                <p class="login-subtitle">KRStore Moda Masculina</p>
            </div>
            
            <?php if ($error): ?>
            <div class="login-error">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label class="form-label" for="username">Usuário</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-input" 
                           placeholder="Digite seu usuário"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Senha</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Digite sua senha"
                           required>
                </div>
                
                <button type="submit" class="login-btn">
                    🔐 Entrar
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">← Voltar para a loja</a>
            </div>
        </div>
    </div>
</body>
</html>
