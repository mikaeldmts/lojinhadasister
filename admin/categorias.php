<?php
/**
 * Admin - Gerenciar Categorias - KRStore
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Categoria.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAuth();

$categoriaModel = new Categoria();

$flash = getFlash();
$errors = [];

// Processar formulário de categoria tipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!Auth::validateCSRF($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de segurança inválido.';
    } else {
        $action = $_POST['action'];
        
        // Criar/Editar Tipo
        if ($action === 'save_tipo') {
            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'slug' => $categoriaModel->generateSlug($_POST['nome'] ?? ''),
                'descricao' => trim($_POST['descricao'] ?? ''),
                'ordem' => (int)($_POST['ordem'] ?? 0),
                'ativo' => isset($_POST['ativo']) ? 1 : 0
            ];
            
            if (empty($data['nome'])) {
                $errors[] = 'O nome da categoria é obrigatório.';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $categoriaModel->updateTipo($id, $data);
                    Auth::logAction('categoria_tipo_update', "Categoria tipo atualizada: {$data['nome']}");
                    redirect('categorias.php', 'Categoria atualizada com sucesso!', 'success');
                } else {
                    $categoriaModel->createTipo($data);
                    Auth::logAction('categoria_tipo_create', "Categoria tipo criada: {$data['nome']}");
                    redirect('categorias.php', 'Categoria criada com sucesso!', 'success');
                }
            }
        }
        
        // Criar/Editar Subtipo
        if ($action === 'save_subtipo') {
            $data = [
                'nome' => trim($_POST['nome'] ?? ''),
                'slug' => $categoriaModel->generateSlug($_POST['nome'] ?? ''),
                'descricao' => trim($_POST['descricao'] ?? ''),
                'cor' => trim($_POST['cor'] ?? '#ffffff'),
                'ordem' => (int)($_POST['ordem'] ?? 0),
                'ativo' => isset($_POST['ativo']) ? 1 : 0
            ];
            
            if (empty($data['nome'])) {
                $errors[] = 'O nome do subtipo é obrigatório.';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $categoriaModel->updateSubtipo($id, $data);
                    Auth::logAction('categoria_subtipo_update', "Subtipo atualizado: {$data['nome']}");
                    redirect('categorias.php', 'Subtipo atualizado com sucesso!', 'success');
                } else {
                    $categoriaModel->createSubtipo($data);
                    Auth::logAction('categoria_subtipo_create', "Subtipo criado: {$data['nome']}");
                    redirect('categorias.php', 'Subtipo criado com sucesso!', 'success');
                }
            }
        }
        
        // Deletar Tipo
        if ($action === 'delete_tipo') {
            $id = (int)($_POST['id'] ?? 0);
            $tipo = $categoriaModel->getTipoById($id);
            if ($tipo) {
                $count = $categoriaModel->countProdutosByTipo($id);
                if ($count > 0) {
                    redirect('categorias.php', "Não é possível excluir. Existem {$count} produto(s) nesta categoria.", 'error');
                } else {
                    $categoriaModel->deleteTipo($id);
                    Auth::logAction('categoria_tipo_delete', "Categoria tipo excluída: {$tipo['nome']}");
                    redirect('categorias.php', 'Categoria excluída com sucesso!', 'success');
                }
            }
        }
        
        // Deletar Subtipo
        if ($action === 'delete_subtipo') {
            $id = (int)($_POST['id'] ?? 0);
            $subtipo = $categoriaModel->getSubtipoById($id);
            if ($subtipo) {
                $categoriaModel->deleteSubtipo($id);
                Auth::logAction('categoria_subtipo_delete', "Subtipo excluído: {$subtipo['nome']}");
                redirect('categorias.php', 'Subtipo excluído com sucesso!', 'success');
            }
        }
    }
}

// Buscar categorias
$categoriasTipo = $categoriaModel->getAllTipos(false);
$categoriasSubtipo = $categoriaModel->getAllSubtipos(false);

$pageTitle = 'Categorias';

include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?>">
        <?php echo $flash['type'] === 'success' ? '✓' : '⚠️'; ?>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>⚠️ Erro:</strong>
        <?php echo htmlspecialchars(implode(', ', $errors)); ?>
    </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Tipos de Produto -->
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">👕 Tipos de Produto</h3>
                <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="openTipoModal()">+ Novo</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Produtos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriasTipo as $tipo): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($tipo['nome']); ?></strong>
                            <br><small style="color: var(--text-muted);"><?php echo $tipo['slug']; ?></small>
                        </td>
                        <td><?php echo $categoriaModel->countProdutosByTipo($tipo['id']); ?></td>
                        <td>
                            <?php if ($tipo['ativo']): ?>
                            <span class="status-badge active">Ativo</span>
                            <?php else: ?>
                            <span class="status-badge inactive">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="action-btn" onclick='editTipo(<?php echo json_encode($tipo); ?>)' title="Editar">✏️</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir esta categoria?');">
                                    <input type="hidden" name="action" value="delete_tipo">
                                    <input type="hidden" name="id" value="<?php echo $tipo['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                                    <button type="submit" class="action-btn delete" title="Excluir">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Subtipos -->
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">🏷️ Subtipos</h3>
                <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="openSubtipoModal()">+ Novo</button>
            </div>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriasSubtipo as $subtipo): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($subtipo['nome']); ?></strong>
                            <br><small style="color: var(--text-muted);"><?php echo $subtipo['slug']; ?></small>
                        </td>
                        <td>
                            <span style="display: inline-block; width: 24px; height: 24px; background: <?php echo $subtipo['cor']; ?>; border-radius: 4px; border: 1px solid var(--admin-border);"></span>
                        </td>
                        <td>
                            <?php if ($subtipo['ativo']): ?>
                            <span class="status-badge active">Ativo</span>
                            <?php else: ?>
                            <span class="status-badge inactive">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="action-btn" onclick='editSubtipo(<?php echo json_encode($subtipo); ?>)' title="Editar">✏️</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Excluir este subtipo?');">
                                    <input type="hidden" name="action" value="delete_subtipo">
                                    <input type="hidden" name="id" value="<?php echo $subtipo['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                                    <button type="submit" class="action-btn delete" title="Excluir">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tipo -->
<div class="admin-modal" id="tipoModal">
    <div class="modal-backdrop" onclick="closeTipoModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="tipoModalTitle">Novo Tipo de Produto</h3>
            <button class="modal-close" onclick="closeTipoModal()">×</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="save_tipo">
                <input type="hidden" name="id" id="tipoId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label required">Nome</label>
                    <input type="text" name="nome" id="tipoNome" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" id="tipoDescricao" class="form-textarea" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" id="tipoOrdem" class="form-input" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <label class="form-check" style="margin-top: 0.5rem;">
                            <input type="checkbox" name="ativo" id="tipoAtivo" value="1" checked>
                            <span>Ativo</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" onclick="closeTipoModal()">Cancelar</button>
                <button type="submit" class="btn-admin btn-admin-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Subtipo -->
<div class="admin-modal" id="subtipoModal">
    <div class="modal-backdrop" onclick="closeSubtipoModal()"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="subtipoModalTitle">Novo Subtipo</h3>
            <button class="modal-close" onclick="closeSubtipoModal()">×</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="save_subtipo">
                <input type="hidden" name="id" id="subtipoId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label required">Nome</label>
                    <input type="text" name="nome" id="subtipoNome" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" id="subtipoDescricao" class="form-textarea" rows="3"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cor</label>
                        <input type="color" name="cor" id="subtipoCor" class="form-input" value="#ffffff" style="height: 42px; padding: 4px;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="ordem" id="subtipoOrdem" class="form-input" value="0" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="ativo" id="subtipoAtivo" value="1" checked>
                        <span>Ativo</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" onclick="closeSubtipoModal()">Cancelar</button>
                <button type="submit" class="btn-admin btn-admin-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal Tipo
function openTipoModal() {
    document.getElementById('tipoModalTitle').textContent = 'Novo Tipo de Produto';
    document.getElementById('tipoId').value = '';
    document.getElementById('tipoNome').value = '';
    document.getElementById('tipoDescricao').value = '';
    document.getElementById('tipoOrdem').value = '0';
    document.getElementById('tipoAtivo').checked = true;
    document.getElementById('tipoModal').classList.add('active');
}

function editTipo(tipo) {
    document.getElementById('tipoModalTitle').textContent = 'Editar Tipo de Produto';
    document.getElementById('tipoId').value = tipo.id;
    document.getElementById('tipoNome').value = tipo.nome;
    document.getElementById('tipoDescricao').value = tipo.descricao || '';
    document.getElementById('tipoOrdem').value = tipo.ordem;
    document.getElementById('tipoAtivo').checked = tipo.ativo == 1;
    document.getElementById('tipoModal').classList.add('active');
}

function closeTipoModal() {
    document.getElementById('tipoModal').classList.remove('active');
}

// Modal Subtipo
function openSubtipoModal() {
    document.getElementById('subtipoModalTitle').textContent = 'Novo Subtipo';
    document.getElementById('subtipoId').value = '';
    document.getElementById('subtipoNome').value = '';
    document.getElementById('subtipoDescricao').value = '';
    document.getElementById('subtipoCor').value = '#ffffff';
    document.getElementById('subtipoOrdem').value = '0';
    document.getElementById('subtipoAtivo').checked = true;
    document.getElementById('subtipoModal').classList.add('active');
}

function editSubtipo(subtipo) {
    document.getElementById('subtipoModalTitle').textContent = 'Editar Subtipo';
    document.getElementById('subtipoId').value = subtipo.id;
    document.getElementById('subtipoNome').value = subtipo.nome;
    document.getElementById('subtipoDescricao').value = subtipo.descricao || '';
    document.getElementById('subtipoCor').value = subtipo.cor || '#ffffff';
    document.getElementById('subtipoOrdem').value = subtipo.ordem;
    document.getElementById('subtipoAtivo').checked = subtipo.ativo == 1;
    document.getElementById('subtipoModal').classList.add('active');
}

function closeSubtipoModal() {
    document.getElementById('subtipoModal').classList.remove('active');
}

// Fechar com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTipoModal();
        closeSubtipoModal();
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
