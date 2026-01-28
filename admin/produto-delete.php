<?php
/**
 * Admin - Deletar Produto - KRStore
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Produto.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('produtos.php', 'Método não permitido.', 'error');
}

// Validar CSRF
if (!Auth::validateCSRF($_POST['csrf_token'] ?? '')) {
    redirect('produtos.php', 'Token de segurança inválido.', 'error');
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    redirect('produtos.php', 'ID do produto inválido.', 'error');
}

$produtoModel = new Produto();
$produto = $produtoModel->getById($id);

if (!$produto) {
    redirect('produtos.php', 'Produto não encontrado.', 'error');
}

if ($produtoModel->delete($id)) {
    Auth::logAction('produto_delete', "Produto excluído: {$produto['nome']} (ID: {$id})");
    redirect('produtos.php', 'Produto excluído com sucesso!', 'success');
} else {
    redirect('produtos.php', 'Erro ao excluir o produto.', 'error');
}
