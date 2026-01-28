<?php
/**
 * Admin - Logs de Acesso - KRStore
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAuth();

// Buscar logs
$logs = Auth::getLogs(100);

$pageTitle = 'Logs de Acesso';

include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <div class="admin-card">
        <div class="card-header">
            <h3 class="card-title">📋 Logs de Atividades</h3>
            <span style="color: var(--text-muted); font-size: 0.9rem;">Últimas 100 ações</span>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <span style="white-space: nowrap;">
                            <?php echo formatDate($log['criado_em']); ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $actionIcon = '📝';
                        $actionClass = '';
                        switch ($log['acao']) {
                            case 'login':
                                $actionIcon = '🔐';
                                $actionClass = 'color: var(--admin-success);';
                                break;
                            case 'logout':
                                $actionIcon = '🚪';
                                $actionClass = 'color: var(--admin-warning);';
                                break;
                            case 'produto_create':
                                $actionIcon = '➕';
                                $actionClass = 'color: var(--admin-success);';
                                break;
                            case 'produto_update':
                                $actionIcon = '✏️';
                                $actionClass = 'color: var(--admin-info);';
                                break;
                            case 'produto_delete':
                                $actionIcon = '🗑️';
                                $actionClass = 'color: var(--admin-error);';
                                break;
                            case 'categoria_tipo_create':
                            case 'categoria_estilo_create':
                                $actionIcon = '🏷️';
                                $actionClass = 'color: var(--admin-success);';
                                break;
                            case 'categoria_tipo_update':
                            case 'categoria_estilo_update':
                                $actionIcon = '🏷️';
                                $actionClass = 'color: var(--admin-info);';
                                break;
                            case 'categoria_tipo_delete':
                            case 'categoria_estilo_delete':
                                $actionIcon = '🏷️';
                                $actionClass = 'color: var(--admin-error);';
                                break;
                        }
                        ?>
                        <span style="<?php echo $actionClass; ?>">
                            <?php echo $actionIcon; ?> <?php echo htmlspecialchars($log['acao']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($log['descricao']); ?></td>
                    <td>
                        <code style="background: var(--admin-bg); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">
                            <?php echo htmlspecialchars($log['ip']); ?>
                        </code>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        Nenhum log registrado ainda.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
