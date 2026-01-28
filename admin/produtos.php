<?php
/**
 * Admin - Lista de Produtos - KRStore
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Produto.php';
require_once __DIR__ . '/../classes/Categoria.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAuth();

$produtoModel = new Produto();
$categoriaModel = new Categoria();

// Filtros
$filtroTipo = isset($_GET['tipo']) ? (int)$_GET['tipo'] : null;
$filtroEstilo = isset($_GET['estilo']) ? (int)$_GET['estilo'] : null;
$busca = isset($_GET['busca']) ? sanitize($_GET['busca']) : null;

// Buscar produtos (incluindo inativos no admin)
if ($busca) {
    $produtos = $produtoModel->search($busca, 100);
} elseif ($filtroTipo) {
    $produtos = $produtoModel->getByTipo($filtroTipo, 100);
} elseif ($filtroEstilo) {
    $produtos = $produtoModel->getByEstilo($filtroEstilo, 100);
} else {
    $produtos = $produtoModel->getAllAdmin(100);
}

// Categorias para filtro
$categoriasTipo = $categoriaModel->getAllTipos(false);
$categoriasEstilo = $categoriaModel->getAllEstilos(false);

$flash = getFlash();
$pageTitle = 'Produtos';

include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?>">
        <?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Filters -->
    <div class="admin-card" style="margin-bottom: 1.5rem;">
        <div class="card-body">
            <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-input" placeholder="Nome do produto..." 
                           value="<?php echo htmlspecialchars($busca ?? ''); ?>">
                </div>
                
                <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($categoriasTipo as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" <?php echo $filtroTipo == $tipo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                    <label class="form-label">Estilo</label>
                    <select name="estilo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($categoriasEstilo as $estilo): ?>
                        <option value="<?php echo $estilo['id']; ?>" <?php echo $filtroEstilo == $estilo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($estilo['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-admin btn-admin-secondary">🔍 Filtrar</button>
                <a href="produtos.php" class="btn-admin btn-admin-secondary">✕ Limpar</a>
                <a href="produto-form.php" class="btn-admin btn-admin-primary">+ Novo Produto</a>
            </form>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">📦 Produtos (<?php echo count($produtos); ?>)</h3>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Tipo</th>
                    <th>Estilo</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" 
                                 alt="" class="product-thumb">
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                                <span>ID: <?php echo $produto['id']; ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($produto['tipo_nome'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($produto['estilo_nome'] ?? '-'); ?></td>
                    <td>
                        <?php if ($produto['preco_promocional']): ?>
                        <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.8rem;">
                            <?php echo formatPrice($produto['preco']); ?>
                        </span><br>
                        <span style="color: var(--admin-accent); font-weight: 600;">
                            <?php echo formatPrice($produto['preco_promocional']); ?>
                        </span>
                        <?php else: ?>
                        <?php echo formatPrice($produto['preco']); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color: <?php echo $produto['estoque'] > 0 ? 'var(--admin-success)' : 'var(--admin-error)'; ?>">
                            <?php echo $produto['estoque']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($produto['ativo']): ?>
                        <span class="status-badge active">Ativo</span>
                        <?php else: ?>
                        <span class="status-badge inactive">Inativo</span>
                        <?php endif; ?>
                        <?php if ($produto['destaque']): ?>
                        <span class="status-badge featured">Destaque</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="produto-form.php?id=<?php echo $produto['id']; ?>" class="action-btn" title="Editar">✏️</a>
                            <button class="action-btn delete" onclick="deleteProduto(<?php echo $produto['id']; ?>, '<?php echo addslashes($produto['nome']); ?>')" title="Excluir">🗑️</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($produtos)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <?php if ($busca || $filtroTipo || $filtroEstilo): ?>
                        Nenhum produto encontrado com os filtros selecionados.
                        <?php else: ?>
                        Nenhum produto cadastrado ainda.
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteModal">
    <div class="modal-backdrop" onclick="closeDeleteModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">⚠️ Confirmar Exclusão</h3>
            <button class="modal-close" onclick="closeDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir o produto <strong id="deleteProductName"></strong>?</p>
            <p style="color: var(--admin-error); font-size: 0.9rem; margin-top: 0.5rem;">Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-admin btn-admin-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <form id="deleteForm" method="POST" action="produto-delete.php" style="margin: 0;">
                <input type="hidden" name="id" id="deleteProductId">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                <button type="submit" class="btn-admin btn-admin-danger">🗑️ Excluir</button>
            </form>
        </div>
    </div>
</div>

<script>
function deleteProduto(id, nome) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = nome;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
