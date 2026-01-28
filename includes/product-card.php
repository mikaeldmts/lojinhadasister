<?php
/**
 * Product Card Component - KRStore
 */

// Calcular desconto se houver preço promocional
$temPromocao = !empty($produto['preco_promocional']) && $produto['preco_promocional'] < $produto['preco'];
$precoAtual = $temPromocao ? $produto['preco_promocional'] : $produto['preco'];
$descontoPercent = $temPromocao ? calcDiscount($produto['preco'], $produto['preco_promocional']) : 0;

// Preparar tamanhos e cores
$tamanhos = !empty($produto['tamanhos']) ? explode(',', $produto['tamanhos']) : [];
$cores = !empty($produto['cores']) ? explode(',', $produto['cores']) : [];

// Verificar se tem variações
$temVariacoes = !empty($produto['variacoes']);
$variacoesJson = $temVariacoes ? htmlspecialchars($produto['variacoes'], ENT_QUOTES, 'UTF-8') : '';

// Preparar galeria de imagens (principal + adicionais)
$todasImagens = [];
if (!empty($produto['imagem_principal'])) {
    $todasImagens[] = $produto['imagem_principal'];
}
if (!empty($produto['imagens_adicionais'])) {
    $imagensAdicionais = json_decode($produto['imagens_adicionais'], true);
    if (is_array($imagensAdicionais)) {
        $todasImagens = array_merge($todasImagens, $imagensAdicionais);
    }
}
$temGaleria = count($todasImagens) > 1;
$cardId = 'card-' . $produto['id'];

// Dados do produto - usando atributos data separados (mais seguro)
$prodId = (int)$produto['id'];
$prodNome = htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8');
$prodPreco = (float)$precoAtual;
$prodImagem = htmlspecialchars($produto['imagem_principal'] ?? '', ENT_QUOTES, 'UTF-8');
$prodTamanhos = htmlspecialchars($produto['tamanhos'] ?? '', ENT_QUOTES, 'UTF-8');
$prodCores = htmlspecialchars($produto['cores'] ?? '', ENT_QUOTES, 'UTF-8');
$prodImagensJson = htmlspecialchars(json_encode($todasImagens), ENT_QUOTES, 'UTF-8');
?>

<article class="product-card" id="<?php echo $cardId; ?>" data-imagens="<?php echo $prodImagensJson; ?>">
    <div class="product-image">
        <?php if ($temGaleria): ?>
        <!-- Galeria com múltiplas imagens -->
        <div class="product-gallery" data-current="0">
            <?php foreach ($todasImagens as $idx => $img): ?>
            <img src="<?php echo htmlspecialchars(strpos($img, 'http') === 0 ? $img : $img); ?>" 
                 alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                 loading="lazy"
                 class="gallery-img <?php echo $idx === 0 ? 'active' : ''; ?>"
                 data-index="<?php echo $idx; ?>">
            <?php endforeach; ?>
            
            <!-- Indicadores -->
            <div class="gallery-dots">
                <?php foreach ($todasImagens as $idx => $img): ?>
                <span class="gallery-dot <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>"></span>
                <?php endforeach; ?>
            </div>
            
            <!-- Setas de navegação -->
            <button type="button" class="gallery-nav gallery-prev" onclick="event.stopPropagation(); galleryNav('<?php echo $cardId; ?>', -1)">‹</button>
            <button type="button" class="gallery-nav gallery-next" onclick="event.stopPropagation(); galleryNav('<?php echo $cardId; ?>', 1)">›</button>
        </div>
        <?php else: ?>
        <!-- Imagem única -->
        <img src="<?php echo htmlspecialchars($produto['imagem_principal']); ?>" 
             alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
             loading="lazy">
        <?php endif; ?>
        
        <!-- Badges -->
        <div class="product-badges">
            <?php if ($temPromocao): ?>
            <span class="badge badge-promo">-<?php echo $descontoPercent; ?>%</span>
            <?php endif; ?>
            <?php if (!empty($produto['destaque'])): ?>
            <span class="badge badge-destaque">Destaque</span>
            <?php endif; ?>
            <?php if ($temGaleria): ?>
            <span class="badge badge-gallery">📷 <?php echo count($todasImagens); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="product-quick-actions">
            <button type="button" class="quick-action-btn btn-open-modal" 
                    title="Ver detalhes"
                    data-id="<?php echo $prodId; ?>"
                    data-nome="<?php echo $prodNome; ?>"
                    data-preco="<?php echo $prodPreco; ?>"
                    data-imagem="<?php echo $prodImagem; ?>"
                    data-tamanhos="<?php echo $prodTamanhos; ?>"
                    data-cores="<?php echo $prodCores; ?>"
                    data-variacoes="<?php echo $variacoesJson; ?>"
                    data-imagens="<?php echo $prodImagensJson; ?>">
                👁️
            </button>
            <button type="button" class="quick-action-btn btn-open-modal" 
                    title="Adicionar ao carrinho"
                    data-id="<?php echo $prodId; ?>"
                    data-nome="<?php echo $prodNome; ?>"
                    data-preco="<?php echo $prodPreco; ?>"
                    data-imagem="<?php echo $prodImagem; ?>"
                    data-tamanhos="<?php echo $prodTamanhos; ?>"
                    data-cores="<?php echo $prodCores; ?>"
                    data-variacoes="<?php echo $variacoesJson; ?>"
                    data-imagens="<?php echo $prodImagensJson; ?>">
                🛒
            </button>
        </div>
    </div>
    
    <div class="product-info">
        <!-- Categoria -->
        <?php if (!empty($produto['tipo_nome'])): ?>
        <span class="product-category"><?php echo htmlspecialchars($produto['tipo_nome']); ?></span>
        <?php endif; ?>
        
        <!-- Nome -->
        <h3 class="product-name"><?php echo htmlspecialchars($produto['nome']); ?></h3>
        
        <!-- Preço -->
        <div class="product-price">
            <span class="price-current"><?php echo formatPrice($precoAtual); ?></span>
            <?php if ($temPromocao): ?>
            <span class="price-old"><?php echo formatPrice($produto['preco']); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Tamanhos disponíveis -->
        <?php if (!empty($tamanhos)): ?>
        <div class="product-sizes">
            <?php foreach (array_slice($tamanhos, 0, 5) as $tam): ?>
            <span class="size-tag"><?php echo trim($tam); ?></span>
            <?php endforeach; ?>
            <?php if (count($tamanhos) > 5): ?>
            <span class="size-tag">+<?php echo count($tamanhos) - 5; ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Botão Adicionar -->
        <button type="button" class="add-to-cart-btn btn-open-modal"
                data-id="<?php echo $prodId; ?>"
                data-nome="<?php echo $prodNome; ?>"
                data-preco="<?php echo $prodPreco; ?>"
                data-imagem="<?php echo $prodImagem; ?>"
                data-tamanhos="<?php echo $prodTamanhos; ?>"
                data-cores="<?php echo $prodCores; ?>"
                data-variacoes="<?php echo $variacoesJson; ?>"
                data-imagens="<?php echo $prodImagensJson; ?>">
            🛒 Adicionar ao Carrinho
        </button>
    </div>
</article>
