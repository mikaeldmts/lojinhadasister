<?php
// Script de Debug - Verificar Produtos
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Produto.php';

echo "<h1>Debug - Produtos</h1>";

$produtoModel = new Produto();

echo "<h2>1. Teste de Conexão</h2>";
$db = Database::getInstance();
if ($db) {
    echo "✅ Conexão estabelecida<br>";
    echo "Tipo de objeto: " . get_class($db) . "<br>";
    echo "É mysqli? " . ($db instanceof mysqli ? 'Sim' : 'Não') . "<br>";
} else {
    echo "❌ Erro na conexão<br>";
    die();
}

echo "<h2>2. Contagem de Produtos</h2>";
$total = $produtoModel->count();
$ativos = $produtoModel->count(true);
echo "Total de produtos: $total<br>";
echo "Produtos ativos: $ativos<br>";

echo "<h2>3. Listar Produtos (Admin)</h2>";

// Debug detalhado
echo "Testando query direta no mysqli...<br>";
$sql = "SELECT p.id, p.nome, p.preco, p.ativo FROM produtos p LIMIT 5";
$result = $db->query($sql);
echo "Resultado da query: " . ($result ? "OK" : "ERRO - " . $db->error) . "<br>";
if ($result) {
    echo "Número de linhas: " . $result->num_rows . "<br>";
}

echo "<br>Testando getAllAdmin()...<br>";
$produtos = $produtoModel->getAllAdmin(5);
echo "Produtos via getAllAdmin: " . count($produtos) . "<br><br>";

if (empty($produtos)) {
    echo "<strong>⚠️ getAllAdmin retornou vazio!</strong><br><br>";
    
    // Testar query direta
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Mas a query direta funciona. Veja:</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Ativo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
            echo "<td>" . ($row['ativo'] ? 'Sim' : 'Não') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<br><p><strong>CONCLUSÃO: O problema está no método getAllAdmin() ou fetchAllAssoc()</strong></p>";
    }
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Ativo</th><th>Tipo</th></tr>";
    foreach ($produtos as $p) {
        echo "<tr>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . htmlspecialchars($p['nome']) . "</td>";
        echo "<td>R$ " . number_format($p['preco'], 2, ',', '.') . "</td>";
        echo "<td>" . ($p['ativo'] ? 'Sim' : 'Não') . "</td>";
        echo "<td>" . ($p['tipo_nome'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>4. Verificar Categorias de Tipo</h2>";
require_once __DIR__ . '/classes/Categoria.php';
$categoriaModel = new Categoria();
$tipos = $categoriaModel->getAllTipos(false);
echo "Categorias de tipo: " . count($tipos) . "<br>";
if (!empty($tipos)) {
    foreach ($tipos as $tipo) {
        echo "- " . $tipo['nome'] . " (ID: " . $tipo['id'] . ")<br>";
    }
}

echo "<br><br><a href='catalog.php'>← Voltar ao catálogo</a> | <a href='admin/'>Admin</a>";
