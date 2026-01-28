<?php
/**
 * KRStore Moda Masculina - Página de Boas-Vindas
 */

// Carregar configurações
require_once __DIR__ . '/config/database.php';

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-dark: #0a0a0a;
            --bg-card: #111111;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent: #00ff88;
            --accent-glow: rgba(0, 255, 136, 0.3);
            --border: #222222;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }

        /* Background Animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-gradient {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            animation: float 20s infinite ease-in-out;
        }

        .bg-gradient:nth-child(1) {
            background: var(--accent);
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }

        .bg-gradient:nth-child(2) {
            background: #00ccff;
            bottom: -200px;
            right: -200px;
            animation-delay: 5s;
        }

        .bg-gradient:nth-child(3) {
            background: #ff00ff;
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(100px, -100px); }
            50% { transform: translate(-100px, 100px); }
            75% { transform: translate(100px, 100px); }
        }

        /* Container */
        .welcome-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        /* Logo */
        .logo {
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out;
        }

        .logo h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            letter-spacing: -0.05em;
            background: linear-gradient(135deg, var(--accent) 0%, #00ccff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 80px var(--accent-glow);
        }

        .logo-subtitle {
            font-size: clamp(1rem, 2vw, 1.5rem);
            font-weight: 300;
            color: var(--text-secondary);
            letter-spacing: 0.3em;
            text-transform: uppercase;
        }

        /* Main Content */
        .welcome-content {
            max-width: 700px;
            margin: 0 auto 3rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .welcome-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .welcome-description {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        /* CTA Button */
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 3rem;
            background: var(--accent);
            color: #000;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 40px var(--accent-glow);
            animation: fadeInUp 1s ease-out 0.4s both;
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 60px var(--accent-glow);
        }

        .cta-icon {
            font-size: 1.5rem;
            transition: transform 0.3s ease;
        }

        .cta-button:hover .cta-icon {
            transform: translateX(5px);
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 900px;
            margin: 4rem auto 0;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .feature {
            padding: 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-10px);
            border-color: var(--accent);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-description {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Social Links */
        .social-links {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 3rem;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        .social-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 50px;
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .social-icon {
            font-size: 1.5rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .features {
                grid-template-columns: 1fr;
            }

            .social-links {
                flex-direction: column;
                align-items: stretch;
            }

            .welcome-container {
                padding: 1.5rem;
            }
        }

        /* Skip Link */
        .skip-welcome {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.75rem 1.5rem;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 50px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .skip-welcome:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
            opacity: 0.5;
        }

        @keyframes bounce {
            0%, 100% { transform: translate(-50%, 0); }
            50% { transform: translate(-50%, 10px); }
        }
    </style>
</head>
<body>
    <!-- Skip Link -->
    <a href="catalog.php?skip=1" class="skip-welcome">Pular →</a>

    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="bg-gradient"></div>
        <div class="bg-gradient"></div>
        <div class="bg-gradient"></div>
    </div>

    <!-- Main Content -->
    <div class="welcome-container">
        <!-- Logo -->
        <div class="logo">
            <h1>KRSTORE</h1>
            <div class="logo-subtitle">Moda Masculina</div>
        </div>

        <!-- Welcome Content -->
        <div class="welcome-content">
            <h2 class="welcome-title">
                Bem-vindo!
            </h2>
            <p class="welcome-description">
                Descubra uma seleção exclusiva de peças que combinam estilo, 
                conforto e qualidade. Do casual ao elegante, encontre o look 
                perfeito para cada momento da sua vida.
            </p>

            <!-- CTA Button -->
            <a href="catalog.php?visited=1" class="cta-button">
                <span>Acessar Catálogo Completo</span>
                <span class="cta-icon">→</span>
            </a>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🔥</div>
                <h3 class="feature-title">Produtos Exclusivos</h3>
                <p class="feature-description">
                    Peças selecionadas com qualidade premium
                </p>
            </div>

            <div class="feature">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">Atendimento Rápido</h3>
                <p class="feature-description">
                    Resposta imediata via WhatsApp
                </p>
            </div>

            <div class="feature">
                <div class="feature-icon">💎</div>
                <h3 class="feature-title">Melhor Custo-Benefício</h3>
                <p class="feature-description">
                    Estilo e qualidade por preços justos
                </p>
            </div>
        </div>

        <!-- Social Links -->
        <div class="social-links">
            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Olá! Vim do site e gostaria de saber mais sobre os produtos." 
               class="social-link" target="_blank" rel="noopener">
                <span class="social-icon">💬</span>
                <span>WhatsApp</span>
            </a>

            <a href="https://instagram.com/<?php echo INSTAGRAM_USER; ?>" 
               class="social-link" target="_blank" rel="noopener">
                <span class="social-icon">📸</span>
                <span>@<?php echo INSTAGRAM_USER; ?></span>
            </a>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator">↓</div>
    </div>
</body>
</html>
