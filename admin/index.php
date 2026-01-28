<?php
/**
 * Admin Dashboard - Lojinha da Irmã
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Produto.php';
require_once __DIR__ . '/../classes/Categoria.php';
require_once __DIR__ . '/../includes/functions.php';

// Verificar autenticação
Auth::requireAuth();

$produtoModel = new Produto();
$categoriaModel = new Categoria();

// Estatísticas
$totalProdutos = $produtoModel->count();
$produtosAtivos = $produtoModel->count(true);
$totalTipos = count($categoriaModel->getAllTipos(false));
$totalSubtipos = count($categoriaModel->getAllSubtipos(false));

// Últimos produtos (incluindo inativos)
$ultimosProdutos = $produtoModel->getAllAdmin(10);

// Flash messages
$flash = getFlash();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?>">
        <?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon products">📦</div>
            </div>
            <div class="stat-value"><?php echo $totalProdutos; ?></div>
            <div class="stat-label">Total de Produtos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon categories">✅</div>
            </div>
            <div class="stat-value"><?php echo $produtosAtivos; ?></div>
            <div class="stat-label">Produtos Ativos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon views">👕</div>
            </div>
            <div class="stat-value"><?php echo $totalTipos; ?></div>
            <div class="stat-label">Tipos de Produtos</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orders">�️</div>
            </div>
            <div class="stat-value"><?php echo $totalSubtipos; ?></div>
            <div class="stat-label">Subtipos</div>
        </div>
    </div>
    
    <!-- Recent Products -->
    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">📋 Últimos Produtos</h3>
            <a href="produtos.php" class="btn-admin btn-admin-sm btn-admin-secondary">Ver Todos</a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosProdutos as $produto): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" 
                                 alt="" class="product-thumb">
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                                <span><?php echo htmlspecialchars($produto['estilo_nome'] ?? '-'); ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($produto['tipo_nome'] ?? '-'); ?></td>
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
                    <td><?php echo $produto['estoque']; ?></td>
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
                            <button class="action-btn delete" onclick="deleteProduto(<?php echo $produto['id']; ?>)" title="Excluir">🗑️</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($ultimosProdutos)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        Nenhum produto cadastrado ainda.
                        <br><br>
                        <a href="produto-form.php" class="btn-admin btn-admin-primary">+ Adicionar Primeiro Produto</a>
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
            <p>Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-admin btn-admin-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <form id="deleteForm" method="POST" action="produto-delete.php" style="margin: 0;">
                <input type="hidden" name="id" id="deleteProductId">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                <button type="submit" class="btn-admin btn-admin-danger">Excluir</button>
            </form>
        </div>
    </div>
</div>

<script>
function deleteProduto(id) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
