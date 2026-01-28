<?php
/**
 * Lojinha da Irmã - Página Principal
 */

// Iniciar sessão
session_start();

// Marcar que o usuário visitou
if (isset($_GET['visited']) || isset($_GET['skip'])) {
    $_SESSION['visited_welcome'] = true;
    if (isset($_GET['skip'])) {
        setcookie('skip_welcome', '1', time() + (86400 * 365), '/'); // 1 ano
    }
}

// Carregar configurações
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Produto.php';
require_once __DIR__ . '/classes/Categoria.php';

// Instanciar classes
$produtoModel = new Produto();
$categoriaModel = new Categoria();

// Buscar categorias
$categoriasTipo = $categoriaModel->getAllTipos();
$categoriasEstilo = $categoriaModel->getAllEstilos();

// Verificar se há busca ou filtros
$busca = isset($_GET['busca']) ? sanitize($_GET['busca']) : null;
$estiloFiltro = isset($_GET['estilo']) ? sanitize($_GET['estilo']) : null;
$tipoFiltro = isset($_GET['tipo']) ? (int)$_GET['tipo'] : null;
$filtroEspecial = isset($_GET['filtro']) ? sanitize($_GET['filtro']) : null;
$produtoId = isset($_GET['produto']) ? (int)$_GET['produto'] : null;

// Buscar produto específico se vier do QR code
$produtoEspecifico = null;
if ($produtoId) {
    $produtoEspecifico = $produtoModel->getById($produtoId);
}

// Buscar produtos por categoria
$produtosPorTipo = [];
foreach ($categoriasTipo as $tipo) {
    $produtosPorTipo[$tipo['id']] = [
        'categoria' => $tipo,
        'produtos' => $produtoModel->getByTipo($tipo['id'], 12)
    ];
}

// Buscar destaques e promoções
$destaques = $produtoModel->getDestaques(8);
$promocoes = $produtoModel->getPromocoes(8);

// Se houver busca
$resultadosBusca = null;
if ($busca) {
    $resultadosBusca = $produtoModel->search($busca);
}

// Se houver filtro especial (destaques, promocoes) ou tipo
$produtosFiltrados = null;
$tituloFiltro = null;
if ($filtroEspecial === 'destaques') {
    $produtosFiltrados = $produtoModel->getDestaques(50);
    $tituloFiltro = '✨ Todos os Destaques';
} elseif ($filtroEspecial === 'promocoes') {
    $produtosFiltrados = $produtoModel->getPromocoes(50);
    $tituloFiltro = '🔥 Todas as Ofertas';
} elseif ($tipoFiltro) {
    $produtosFiltrados = $produtoModel->getByTipo($tipoFiltro, 50);
    // Buscar nome da categoria
    foreach ($categoriasTipo as $cat) {
        if ($cat['id'] == $tipoFiltro) {
            $tituloFiltro = $cat['nome'];
            break;
        }
    }
}

// Título da página
$pageTitle = $busca ? "Busca: $busca" : ($tituloFiltro ? $tituloFiltro : "Início");

// Incluir header
include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Descubra seu <span>Estilo</span></h1>
                <p>Explore nossa coleção exclusiva de produtos selecionados com carinho. Roupas, acessórios, decoração e muito mais para você!</p>
                <div class="hero-buttons">
                    <a href="#roupas" class="btn btn-primary btn-lg">Ver Coleção</a>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="btn btn-outline btn-lg">Fale Conosco</a>
                </div>
            </div>
            <div class="hero-image">
                <!-- Espaço para imagem hero se necessário -->
            </div>
        </div>
    </div>
</section>

<!-- Filtro por Estilos -->
<section class="categories-filter">
    <div class="container">
        <div class="filter-scroll">
            <button class="filter-btn active" data-filter="all">Todos</button>
            <?php foreach ($categoriasEstilo as $estilo): ?>
            <button class="filter-btn" data-filter="<?php echo $estilo['slug']; ?>">
                <?php echo $estilo['nome']; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($busca && $resultadosBusca): ?>
<!-- Resultados da Busca -->
<section class="products-section section">
    <div class="container">
        <div class="products-header">
            <h2 class="section-title">Resultados para "<?php echo htmlspecialchars($busca); ?>"</h2>
            <span class="text-muted"><?php echo count($resultadosBusca); ?> produto(s) encontrado(s)</span>
        </div>
        
        <?php if (empty($resultadosBusca)): ?>
        <div class="text-center" style="padding: 3rem;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">Nenhum produto encontrado.</p>
            <a href="catalog.php" class="btn btn-primary" style="margin-top: 1rem;">Ver todos os produtos</a>
        </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach ($resultadosBusca as $produto): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php elseif ($produtosFiltrados): ?>
<!-- Produtos Filtrados -->
<section class="products-section section">
    <div class="container">
        <div class="products-header">
            <h2 class="section-title"><?php echo $tituloFiltro; ?></h2>
            <a href="catalog.php" class="view-all-link">← Voltar ao catálogo</a>
        </div>
        
        <?php if (empty($produtosFiltrados)): ?>
        <div class="text-center" style="padding: 3rem;">
            <p style="color: var(--text-muted); font-size: 1.1rem;">Nenhum produto encontrado nesta categoria.</p>
            <a href="catalog.php" class="btn btn-primary" style="margin-top: 1rem;">Ver todos os produtos</a>
        </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach ($produtosFiltrados as $produto): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php else: ?>

<?php if (!empty($destaques)): ?>
<!-- Destaques -->
<section class="products-section section" data-category="destaques">
    <div class="container">
        <div class="products-header">
            <h2 class="section-title">✨ Destaques</h2>
            <a href="catalog.php?filtro=destaques" class="view-all-link">Ver todos →</a>
        </div>
        
        <div class="carousel-container">
            <button class="carousel-btn prev">‹</button>
            <div class="carousel-track">
                <?php foreach ($destaques as $produto): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="carousel-btn next">›</button>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($promocoes)): ?>
<!-- Promoções -->
<section class="products-section section" style="background: var(--bg-secondary);" data-category="promocoes">
    <div class="container">
        <div class="products-header">
            <h2 class="section-title">🔥 Ofertas Imperdíveis</h2>
            <a href="catalog.php?filtro=promocoes" class="view-all-link">Ver todas →</a>
        </div>
        
        <div class="carousel-container">
            <button class="carousel-btn prev">‹</button>
            <div class="carousel-track">
                <?php foreach ($promocoes as $produto): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="carousel-btn next">›</button>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Seções por Tipo de Produto -->
<?php foreach ($produtosPorTipo as $tipoId => $data): ?>
<?php if (!empty($data['produtos'])): ?>
<section class="products-section section" id="<?php echo $data['categoria']['slug']; ?>" data-category="<?php echo $data['categoria']['slug']; ?>">
    <div class="container">
        <div class="products-header">
            <h2 class="section-title"><?php echo $data['categoria']['nome']; ?></h2>
            <a href="catalog.php?tipo=<?php echo $data['categoria']['id']; ?>" class="view-all-link">Ver todos →</a>
        </div>
        
        <div class="carousel-container">
            <button class="carousel-btn prev">‹</button>
            <div class="carousel-track">
                <?php foreach ($data['produtos'] as $produto): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="carousel-btn next">›</button>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endforeach; ?>

<?php endif; ?>

<!-- Banner WhatsApp -->
<section class="section" style="background: linear-gradient(135deg, #25d366 0%, #128c7e 100%); text-align: center; padding: 3rem 0;">
    <div class="container">
        <h2 style="color: white; font-size: 1.75rem; margin-bottom: 1rem;">Dúvidas? Fale com a gente!</h2>
        <p style="color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">Atendimento personalizado pelo WhatsApp</p>
        <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="btn btn-lg" style="background: white; color: #25d366; font-weight: 700;">
            💬 Chamar no WhatsApp
        </a>
    </div>
</section>

<?php
// Incluir footer
include __DIR__ . '/includes/footer.php';
?>

<?php if ($produtoEspecifico): ?>
<!-- Script para abrir modal do produto específico (QR Code) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aguardar um pouco para garantir que tudo carregou
    setTimeout(function() {
        var produto = {
            id: <?php echo (int)$produtoEspecifico['id']; ?>,
            nome: <?php echo json_encode($produtoEspecifico['nome']); ?>,
            preco: <?php echo (float)($produtoEspecifico['preco_promocional'] ?: $produtoEspecifico['preco']); ?>,
            imagem: <?php echo json_encode($produtoEspecifico['imagem_principal'] ?? ''); ?>,
            tamanhos: <?php echo json_encode($produtoEspecifico['tamanhos'] ?? ''); ?>,
            cores: <?php echo json_encode($produtoEspecifico['cores'] ?? ''); ?>
        };
        
        if (typeof showProductModal === 'function') {
            showProductModal(produto);
        }
    }, 500);
});
</script>
<?php endif; ?>
