<?php
/**
 * Admin - Formulário de Produto - KRStore
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

// Verificar se é edição
$isEdit = isset($_GET['id']);
$produto = null;

if ($isEdit) {
    $produto = $produtoModel->getById((int)$_GET['id']);
    if (!$produto) {
        redirect('produtos.php', 'Produto não encontrado.', 'error');
    }
}

// Categorias
$categoriasTipo = $categoriaModel->getAllTipos(false);
$categoriasEstilo = $categoriaModel->getAllEstilos(false);

$errors = [];
$success = false;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!Auth::validateCSRF($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de segurança inválido.';
    }
    
    // Coletar dados
    $data = [
        'nome' => trim($_POST['nome'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'preco' => (float)str_replace(',', '.', $_POST['preco'] ?? 0),
        'preco_promocional' => !empty($_POST['preco_promocional']) ? (float)str_replace(',', '.', $_POST['preco_promocional']) : null,
        'categoria_tipo_id' => (int)($_POST['categoria_tipo_id'] ?? 0),
        'categoria_estilo_id' => !empty($_POST['categoria_estilo_id']) ? (int)$_POST['categoria_estilo_id'] : null,
        'imagem_principal' => trim($_POST['imagem_principal'] ?? ''),
        'imagens_adicionais' => null,
        'tamanhos' => trim($_POST['tamanhos'] ?? 'P,M,G,GG'),
        'cores' => trim($_POST['cores'] ?? ''),
        'variacoes' => null,
        'estoque' => (int)($_POST['estoque'] ?? 0),
        'destaque' => isset($_POST['destaque']) ? 1 : 0,
        'ativo' => isset($_POST['ativo']) ? 1 : 0
    ];
    
    // Processar variações se houver
    $usarVariacoes = isset($_POST['usar_variacoes']) && $_POST['usar_variacoes'] == '1';
    if ($usarVariacoes && !empty($_POST['var_nome'])) {
        $variacoes = [];
        foreach ($_POST['var_nome'] as $i => $nome) {
            if (!empty($nome)) {
                $variacoes[] = [
                    'nome' => trim($nome),
                    'tamanhos' => trim($_POST['var_tamanhos'][$i] ?? ''),
                    'cores' => trim($_POST['var_cores'][$i] ?? '')
                ];
            }
        }
        if (!empty($variacoes)) {
            $data['variacoes'] = json_encode($variacoes, JSON_UNESCAPED_UNICODE);
            // Limpar tamanhos/cores gerais quando usar variações
            $data['tamanhos'] = '';
            $data['cores'] = '';
        }
    }
    
    // Processar upload de imagem se houver
    if (isset($_FILES['imagem_upload']) && $_FILES['imagem_upload']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/products/';
        
        // Criar diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $file = $_FILES['imagem_upload'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validar tipo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'A imagem deve ter no máximo 5MB.';
        } else {
            // Gerar nome único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'produto_' . time() . '_' . uniqid() . '.' . strtolower($extension);
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Salvar caminho relativo
                $data['imagem_principal'] = 'uploads/products/' . $newFileName;
            } else {
                $errors[] = 'Erro ao fazer upload da imagem.';
            }
        }
    }
    
    // Se não houver upload e tiver URL, manter a URL
    if (empty($data['imagem_principal']) && !empty($_POST['imagem_url'])) {
        $data['imagem_principal'] = trim($_POST['imagem_url']);
    }
    
    // Se for edição e não mudou a imagem, manter a anterior
    if ($isEdit && empty($data['imagem_principal']) && !empty($produto['imagem_principal'])) {
        $data['imagem_principal'] = $produto['imagem_principal'];
    }
    
    // Processar imagens adicionais
    $imagensAdicionais = [];
    
    // Manter imagens existentes
    if (!empty($_POST['imagens_adicionais_existentes'])) {
        $imagensAdicionais = $_POST['imagens_adicionais_existentes'];
    }
    
    // Adicionar URLs de imagens
    if (!empty($_POST['imagens_adicionais_urls'])) {
        foreach ($_POST['imagens_adicionais_urls'] as $url) {
            $url = trim($url);
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $imagensAdicionais[] = $url;
            }
        }
    }
    
    // Upload de múltiplas imagens adicionais
    if (!empty($_FILES['imagens_adicionais_upload']['name'][0])) {
        $uploadDir = __DIR__ . '/../uploads/products/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        foreach ($_FILES['imagens_adicionais_upload']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['imagens_adicionais_upload']['error'][$i] === UPLOAD_ERR_OK) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);
                
                if (in_array($mimeType, $allowedTypes) && $_FILES['imagens_adicionais_upload']['size'][$i] <= $maxSize) {
                    $extension = pathinfo($_FILES['imagens_adicionais_upload']['name'][$i], PATHINFO_EXTENSION);
                    $newFileName = 'produto_' . time() . '_' . uniqid() . '.' . strtolower($extension);
                    $uploadPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        $imagensAdicionais[] = 'uploads/products/' . $newFileName;
                    }
                }
            }
        }
    }
    
    if (!empty($imagensAdicionais)) {
        $data['imagens_adicionais'] = json_encode($imagensAdicionais, JSON_UNESCAPED_UNICODE);
    }
    
    // Validações
    if (empty($data['nome'])) {
        $errors[] = 'O nome do produto é obrigatório.';
    }
    
    if ($data['preco'] <= 0) {
        $errors[] = 'O preço deve ser maior que zero.';
    }
    
    if ($data['categoria_tipo_id'] <= 0) {
        $errors[] = 'Selecione uma categoria de tipo.';
    }
    
    if (empty($data['imagem_principal']) && !$isEdit) {
        $errors[] = 'A imagem principal é obrigatória para novos produtos.';
    }
    
    // Gerar slug
    $data['slug'] = $produtoModel->generateSlug($data['nome'], $isEdit ? $produto['id'] : null);
    
    // Salvar
    if (empty($errors)) {
        if ($isEdit) {
            if ($produtoModel->update($produto['id'], $data)) {
                Auth::logAction('produto_update', "Produto atualizado: {$data['nome']} (ID: {$produto['id']})");
                redirect('produtos.php', 'Produto atualizado com sucesso!', 'success');
            } else {
                $errors[] = 'Erro ao atualizar o produto.';
            }
        } else {
            $newId = $produtoModel->create($data);
            if ($newId) {
                Auth::logAction('produto_create', "Produto criado: {$data['nome']} (ID: {$newId})");
                redirect('produtos.php', 'Produto criado com sucesso!', 'success');
            } else {
                $errors[] = 'Erro ao criar o produto.';
            }
        }
    }
    
    // Se houve erro, manter os dados no formulário
    $produto = array_merge($produto ?? [], $data);
}

$pageTitle = $isEdit ? 'Editar Produto' : 'Novo Produto';

include __DIR__ . '/includes/header.php';
?>

<div class="admin-content">
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <strong>⚠️ Corrija os seguintes erros:</strong>
        <ul style="margin: 0.5rem 0 0 1.5rem;">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" class="admin-card" enctype="multipart/form-data">
        <div class="card-header">
            <h3 class="card-title"><?php echo $isEdit ? '✏️ Editar Produto' : '➕ Novo Produto'; ?></h3>
        </div>
        
        <div class="card-body">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            
            <!-- Nome e Preço -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Nome do Produto</label>
                    <input type="text" name="nome" class="form-input" 
                           value="<?php echo htmlspecialchars($produto['nome'] ?? ''); ?>" 
                           placeholder="Ex: Camiseta Básica Preta" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Preço (R$)</label>
                    <input type="text" name="preco" class="form-input" 
                           value="<?php echo isset($produto['preco']) ? number_format($produto['preco'], 2, ',', '') : ''; ?>" 
                           placeholder="99,90" required>
                </div>
            </div>
            
            <!-- Preço Promocional e Estoque -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Preço Promocional (R$)</label>
                    <input type="text" name="preco_promocional" class="form-input" 
                           value="<?php echo isset($produto['preco_promocional']) && $produto['preco_promocional'] ? number_format($produto['preco_promocional'], 2, ',', '') : ''; ?>" 
                           placeholder="79,90 (deixe vazio se não houver)">
                    <span class="form-hint">Deixe vazio se não houver promoção</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estoque</label>
                    <input type="number" name="estoque" class="form-input" 
                           value="<?php echo $produto['estoque'] ?? 0; ?>" min="0">
                </div>
            </div>
            
            <!-- Categorias -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Tipo de Produto</label>
                    <select name="categoria_tipo_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categoriasTipo as $tipo): ?>
                        <option value="<?php echo $tipo['id']; ?>" 
                                <?php echo ($produto['categoria_tipo_id'] ?? '') == $tipo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tipo['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estilo</label>
                    <select name="categoria_estilo_id" class="form-select">
                        <option value="">Selecione (opcional)...</option>
                        <?php foreach ($categoriasEstilo as $estilo): ?>
                        <option value="<?php echo $estilo['id']; ?>" 
                                <?php echo ($produto['categoria_estilo_id'] ?? '') == $estilo['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($estilo['nome']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Descrição -->
            <div class="form-group">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-textarea" rows="4" 
                          placeholder="Descreva o produto..."><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
            </div>
            
            <!-- Imagem -->
            <div class="form-group">
                <label class="form-label required">Imagem do Produto</label>
                
                <!-- Abas para escolher método -->
                <div class="image-tabs" style="display: flex; gap: 0; margin-bottom: 1rem;">
                    <button type="button" class="image-tab active" onclick="switchImageTab('upload')" id="tab-upload"
                            style="padding: 0.5rem 1rem; background: var(--admin-primary); color: white; border: none; border-radius: 6px 0 0 6px; cursor: pointer;">
                        📤 Upload
                    </button>
                    <button type="button" class="image-tab" onclick="switchImageTab('url')" id="tab-url"
                            style="padding: 0.5rem 1rem; background: var(--admin-bg-tertiary); color: var(--admin-text-secondary); border: 1px solid var(--admin-border); border-radius: 0 6px 6px 0; cursor: pointer;">
                        🔗 URL Externa
                    </button>
                </div>
                
                <!-- Upload de arquivo -->
                <div id="upload-section">
                    <div class="upload-area" id="uploadArea" 
                         style="border: 2px dashed var(--admin-border); border-radius: 8px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s;">
                        <input type="file" name="imagem_upload" id="imagemUpload" accept="image/*" 
                               style="display: none;" onchange="previewUploadedImage(this)">
                        <div id="uploadPlaceholder">
                            <span style="font-size: 3rem; display: block; margin-bottom: 0.5rem;">📷</span>
                            <span style="color: var(--admin-text-secondary);">Clique ou arraste uma imagem aqui</span>
                            <br><small style="color: var(--admin-text-muted);">JPG, PNG, WebP ou GIF (máx. 5MB)</small>
                        </div>
                        <div id="uploadPreview" style="display: none;">
                            <img id="uploadPreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                            <br><button type="button" onclick="clearUpload()" style="margin-top: 0.5rem; padding: 0.25rem 0.5rem; background: var(--admin-danger); color: white; border: none; border-radius: 4px; cursor: pointer;">✕ Remover</button>
                        </div>
                    </div>
                </div>
                
                <!-- URL externa -->
                <div id="url-section" style="display: none;">
                    <input type="text" name="imagem_url" class="form-input" id="imagemUrlInput"
                           value="<?php echo (isset($produto['imagem_principal']) && strpos($produto['imagem_principal'], 'http') === 0) ? htmlspecialchars($produto['imagem_principal']) : ''; ?>" 
                           placeholder="https://exemplo.com/imagem.jpg">
                    <span class="form-hint">Cole a URL da imagem (ImgBB, Imgur, etc)</span>
                </div>
                
                <!-- Preview da imagem atual (edição) -->
                <?php if (!empty($produto['imagem_principal'])): ?>
                <div class="current-image" style="margin-top: 1rem; padding: 1rem; background: var(--admin-bg-tertiary); border-radius: 8px;">
                    <small style="color: var(--admin-text-muted);">Imagem atual:</small>
                    <br>
                    <img src="<?php echo htmlspecialchars(strpos($produto['imagem_principal'], 'http') === 0 ? $produto['imagem_principal'] : '../' . $produto['imagem_principal']); ?>" 
                         alt="Imagem atual" style="max-width: 150px; border-radius: 8px; margin-top: 0.5rem;">
                    <input type="hidden" name="imagem_principal" value="<?php echo htmlspecialchars($produto['imagem_principal']); ?>">
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Imagens Adicionais -->
            <?php 
            $imagensAdicionais = !empty($produto['imagens_adicionais']) ? json_decode($produto['imagens_adicionais'], true) : [];
            ?>
            <div class="form-group">
                <label class="form-label">📸 Imagens Adicionais (opcional)</label>
                <span class="form-hint" style="display: block; margin-bottom: 0.75rem;">Adicione mais fotos do produto para o cliente ver</span>
                
                <div id="imagens-adicionais-container" style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <?php if (!empty($imagensAdicionais)): ?>
                        <?php foreach ($imagensAdicionais as $i => $imgUrl): ?>
                        <div class="img-adicional-item" style="position: relative; width: 100px; height: 100px; border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars(strpos($imgUrl, 'http') === 0 ? $imgUrl : '../' . $imgUrl); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <input type="hidden" name="imagens_adicionais_existentes[]" value="<?php echo htmlspecialchars($imgUrl); ?>">
                            <button type="button" onclick="removerImagemAdicional(this)" style="position: absolute; top: 2px; right: 2px; background: var(--admin-danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; line-height: 1;">✕</button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="file" name="imagens_adicionais_upload[]" id="imagensAdicionaisUpload" accept="image/*" multiple 
                           style="display: none;" onchange="previewImagensAdicionais(this)">
                    <button type="button" onclick="document.getElementById('imagensAdicionaisUpload').click()" 
                            style="background: var(--admin-bg-tertiary); border: 1px solid var(--admin-border); border-radius: 6px; padding: 0.5rem 1rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; color: var(--admin-text-secondary);">
                        📤 Upload de fotos
                    </button>
                    <span style="color: var(--admin-text-muted);">ou</span>
                    <input type="text" id="urlImagemAdicional" class="form-input" placeholder="Cole URL da imagem" style="flex: 1; max-width: 300px;">
                    <button type="button" onclick="adicionarUrlImagem()" 
                            style="background: var(--admin-primary); color: white; border: none; border-radius: 6px; padding: 0.5rem 1rem; cursor: pointer;">
                        + Adicionar
                    </button>
                </div>
                
                <div id="preview-imagens-novas" style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 0.75rem;"></div>
            </div>
            
            <!-- Modo de Tamanhos/Cores -->
            <?php 
            $temVariacoes = !empty($produto['variacoes']);
            $variacoes = $temVariacoes ? json_decode($produto['variacoes'], true) : [];
            ?>
            
            <div class="form-group">
                <label class="form-label">Modo de Tamanhos e Cores</label>
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <label class="form-check" style="padding: 0.75rem 1rem; background: var(--admin-bg-tertiary); border-radius: 8px; cursor: pointer;">
                        <input type="radio" name="usar_variacoes" value="0" <?php echo !$temVariacoes ? 'checked' : ''; ?> onchange="toggleVariacoes(false)">
                        <span><strong>Simples</strong> - Um produto com tamanhos/cores únicos</span>
                    </label>
                    <label class="form-check" style="padding: 0.75rem 1rem; background: var(--admin-bg-tertiary); border-radius: 8px; cursor: pointer;">
                        <input type="radio" name="usar_variacoes" value="1" <?php echo $temVariacoes ? 'checked' : ''; ?> onchange="toggleVariacoes(true)">
                        <span><strong>Variações</strong> - Múltiplos itens na mesma foto</span>
                    </label>
                </div>
            </div>
            
            <!-- Tamanhos e Cores SIMPLES -->
            <div id="modo-simples" style="<?php echo $temVariacoes ? 'display:none;' : ''; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tamanhos Disponíveis</label>
                        <input type="text" name="tamanhos" class="form-input" id="tamanhos-simples"
                               value="<?php echo htmlspecialchars($produto['tamanhos'] ?? 'P,M,G,GG'); ?>" 
                               placeholder="P,M,G,GG,XG">
                        <span class="form-hint">Separe por vírgula (ex: P,M,G,GG)</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Cores Disponíveis</label>
                        <input type="text" name="cores" class="form-input" id="cores-simples"
                               value="<?php echo htmlspecialchars($produto['cores'] ?? ''); ?>" 
                               placeholder="Preto,Branco,Azul">
                        <span class="form-hint">Separe por vírgula (ex: Preto,Branco,Azul)</span>
                    </div>
                </div>
            </div>
            
            <!-- Variações MÚLTIPLAS -->
            <div id="modo-variacoes" style="<?php echo $temVariacoes ? '' : 'display:none;'; ?>">
                <div style="background: var(--admin-bg-tertiary); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="color: var(--admin-text-secondary); margin-bottom: 1rem;">
                        📦 Adicione cada item que aparece na foto separadamente (ex: 3 camisas diferentes)
                    </p>
                    
                    <div id="variacoes-container">
                        <?php if ($temVariacoes && !empty($variacoes)): ?>
                            <?php foreach ($variacoes as $i => $var): ?>
                            <div class="variacao-item" style="background: var(--admin-bg-secondary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                    <strong style="color: var(--admin-primary);">Item <?php echo $i + 1; ?></strong>
                                    <button type="button" onclick="removerVariacao(this)" style="background: var(--admin-danger); color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer;">✕</button>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Nome do Item *</label>
                                        <input type="text" name="var_nome[]" class="form-input" value="<?php echo htmlspecialchars($var['nome']); ?>" placeholder="Ex: Camisa Azul">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Tamanhos</label>
                                        <input type="text" name="var_tamanhos[]" class="form-input" value="<?php echo htmlspecialchars($var['tamanhos']); ?>" placeholder="P,M,G">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Cores</label>
                                        <input type="text" name="var_cores[]" class="form-input" value="<?php echo htmlspecialchars($var['cores']); ?>" placeholder="Azul,Marinho">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Item padrão inicial -->
                            <div class="variacao-item" style="background: var(--admin-bg-secondary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                    <strong style="color: var(--admin-primary);">Item 1</strong>
                                    <button type="button" onclick="removerVariacao(this)" style="background: var(--admin-danger); color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer;">✕</button>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Nome do Item *</label>
                                        <input type="text" name="var_nome[]" class="form-input" placeholder="Ex: Camisa Azul">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Tamanhos</label>
                                        <input type="text" name="var_tamanhos[]" class="form-input" placeholder="P,M,G">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Cores</label>
                                        <input type="text" name="var_cores[]" class="form-input" placeholder="Azul,Marinho">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" onclick="adicionarVariacao()" 
                            style="background: var(--admin-primary); color: white; border: none; border-radius: 6px; padding: 0.5rem 1rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        ➕ Adicionar mais um item
                    </button>
                </div>
            </div>
            
            <!-- Opções -->
            <div class="form-group">
                <label class="form-label">Opções</label>
                <div style="display: flex; gap: 2rem;">
                    <label class="form-check">
                        <input type="checkbox" name="ativo" value="1" 
                               <?php echo (!isset($produto['ativo']) || $produto['ativo']) ? 'checked' : ''; ?>>
                        <span>Produto Ativo</span>
                    </label>
                    
                    <label class="form-check">
                        <input type="checkbox" name="destaque" value="1" 
                               <?php echo !empty($produto['destaque']) ? 'checked' : ''; ?>>
                        <span>Produto em Destaque</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card-header" style="border-top: 1px solid var(--admin-border); border-bottom: none;">
            <div></div>
            <div style="display: flex; gap: 1rem;">
                <a href="produtos.php" class="btn-admin btn-admin-secondary">← Cancelar</a>
                <button type="submit" class="btn-admin btn-admin-primary">
                    <?php echo $isEdit ? '💾 Salvar Alterações' : '✓ Criar Produto'; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Alternar entre abas de upload e URL
function switchImageTab(tab) {
    var uploadSection = document.getElementById('upload-section');
    var urlSection = document.getElementById('url-section');
    var tabUpload = document.getElementById('tab-upload');
    var tabUrl = document.getElementById('tab-url');
    
    if (tab === 'upload') {
        uploadSection.style.display = 'block';
        urlSection.style.display = 'none';
        tabUpload.style.background = 'var(--admin-primary)';
        tabUpload.style.color = 'white';
        tabUrl.style.background = 'var(--admin-bg-tertiary)';
        tabUrl.style.color = 'var(--admin-text-secondary)';
    } else {
        uploadSection.style.display = 'none';
        urlSection.style.display = 'block';
        tabUrl.style.background = 'var(--admin-primary)';
        tabUrl.style.color = 'white';
        tabUpload.style.background = 'var(--admin-bg-tertiary)';
        tabUpload.style.color = 'var(--admin-text-secondary)';
    }
}

// Upload area - clique
document.getElementById('uploadArea').addEventListener('click', function() {
    document.getElementById('imagemUpload').click();
});

// Upload area - drag and drop
var uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--admin-primary)';
    this.style.background = 'rgba(0, 255, 136, 0.05)';
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--admin-border)';
    this.style.background = 'transparent';
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--admin-border)';
    this.style.background = 'transparent';
    
    var files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('imagemUpload').files = files;
        previewUploadedImage(document.getElementById('imagemUpload'));
    }
});

// Preview da imagem uploadada
function previewUploadedImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('uploadPreviewImg').src = e.target.result;
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('uploadPreview').style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Limpar upload
function clearUpload() {
    document.getElementById('imagemUpload').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('uploadPreview').style.display = 'none';
}

// ==========================================
// VARIAÇÕES DO PRODUTO
// ==========================================

// Alternar entre modo simples e variações
function toggleVariacoes(usarVariacoes) {
    var modoSimples = document.getElementById('modo-simples');
    var modoVariacoes = document.getElementById('modo-variacoes');
    
    if (usarVariacoes) {
        modoSimples.style.display = 'none';
        modoVariacoes.style.display = 'block';
    } else {
        modoSimples.style.display = 'block';
        modoVariacoes.style.display = 'none';
    }
}

// Contador para numerar os itens
var variacaoCount = document.querySelectorAll('.variacao-item').length || 1;

// Adicionar nova variação
function adicionarVariacao() {
    variacaoCount++;
    var container = document.getElementById('variacoes-container');
    
    var html = '<div class="variacao-item" style="background: var(--admin-bg-secondary); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem;">' +
        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">' +
            '<strong style="color: var(--admin-primary);">Item ' + variacaoCount + '</strong>' +
            '<button type="button" onclick="removerVariacao(this)" style="background: var(--admin-danger); color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer;">✕</button>' +
        '</div>' +
        '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">' +
            '<div>' +
                '<label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Nome do Item *</label>' +
                '<input type="text" name="var_nome[]" class="form-input" placeholder="Ex: Camisa Preta">' +
            '</div>' +
            '<div>' +
                '<label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Tamanhos</label>' +
                '<input type="text" name="var_tamanhos[]" class="form-input" placeholder="M,G,GG">' +
            '</div>' +
            '<div>' +
                '<label style="font-size: 0.85rem; color: var(--admin-text-secondary);">Cores</label>' +
                '<input type="text" name="var_cores[]" class="form-input" placeholder="Preto">' +
            '</div>' +
        '</div>' +
    '</div>';
    
    container.insertAdjacentHTML('beforeend', html);
    renumerarVariacoes();
}

// Remover variação
function removerVariacao(btn) {
    var items = document.querySelectorAll('.variacao-item');
    if (items.length > 1) {
        btn.closest('.variacao-item').remove();
        renumerarVariacoes();
    } else {
        alert('É necessário ter pelo menos 1 item!');
    }
}

// Renumerar variações
function renumerarVariacoes() {
    var items = document.querySelectorAll('.variacao-item');
    items.forEach(function(item, index) {
        var label = item.querySelector('strong');
        if (label) {
            label.textContent = 'Item ' + (index + 1);
        }
    });
    variacaoCount = items.length;
}

// ==========================================
// IMAGENS ADICIONAIS
// ==========================================

// Preview de múltiplas imagens
function previewImagensAdicionais(input) {
    var container = document.getElementById('preview-imagens-novas');
    
    if (input.files) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            var reader = new FileReader();
            
            reader.onload = (function(fileName) {
                return function(e) {
                    var div = document.createElement('div');
                    div.className = 'img-adicional-item';
                    div.style.cssText = 'position: relative; width: 100px; height: 100px; border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden;';
                    div.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">' +
                        '<span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,255,136,0.8); color: #000; font-size: 10px; padding: 2px; text-align: center;">Nova</span>';
                    container.appendChild(div);
                };
            })(file.name);
            
            reader.readAsDataURL(file);
        }
    }
}

// Adicionar imagem por URL
function adicionarUrlImagem() {
    var input = document.getElementById('urlImagemAdicional');
    var url = input.value.trim();
    
    if (!url) {
        alert('Cole uma URL de imagem!');
        return;
    }
    
    var container = document.getElementById('imagens-adicionais-container');
    
    var div = document.createElement('div');
    div.className = 'img-adicional-item';
    div.style.cssText = 'position: relative; width: 100px; height: 100px; border: 1px solid var(--admin-border); border-radius: 8px; overflow: hidden;';
    div.innerHTML = '<img src="' + url + '" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.remove(); alert(\'URL de imagem inválida!\');">' +
        '<input type="hidden" name="imagens_adicionais_urls[]" value="' + url + '">' +
        '<button type="button" onclick="removerImagemAdicional(this)" style="position: absolute; top: 2px; right: 2px; background: var(--admin-danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; line-height: 1;">✕</button>';
    
    container.appendChild(div);
    input.value = '';
}

// Remover imagem adicional
function removerImagemAdicional(btn) {
    btn.closest('.img-adicional-item').remove();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
