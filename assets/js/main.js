/**
 * KRStore - JavaScript Principal
 * Carrinho e Funcionalidades
 */

console.log('KRStore JS carregado!');

// ==========================================
// GALERIA DE IMAGENS NOS CARDS
// ==========================================
var galleryIntervals = {};

// Navegar na galeria (manual)
function galleryNav(cardId, direction) {
    var card = document.getElementById(cardId);
    if (!card) return;
    
    var gallery = card.querySelector('.product-gallery');
    if (!gallery) return;
    
    var images = gallery.querySelectorAll('.gallery-img');
    var dots = gallery.querySelectorAll('.gallery-dot');
    var current = parseInt(gallery.dataset.current) || 0;
    
    // Calcular novo índice
    var newIndex = current + direction;
    if (newIndex < 0) newIndex = images.length - 1;
    if (newIndex >= images.length) newIndex = 0;
    
    // Atualizar imagens
    images.forEach(function(img, i) {
        img.classList.toggle('active', i === newIndex);
    });
    
    // Atualizar dots
    dots.forEach(function(dot, i) {
        dot.classList.toggle('active', i === newIndex);
    });
    
    gallery.dataset.current = newIndex;
    
    // Resetar timer automático
    resetGalleryTimer(cardId);
}

// Ir para imagem específica (click no dot)
function galleryGoTo(cardId, index) {
    var card = document.getElementById(cardId);
    if (!card) return;
    
    var gallery = card.querySelector('.product-gallery');
    if (!gallery) return;
    
    var images = gallery.querySelectorAll('.gallery-img');
    var dots = gallery.querySelectorAll('.gallery-dot');
    
    images.forEach(function(img, i) {
        img.classList.toggle('active', i === index);
    });
    
    dots.forEach(function(dot, i) {
        dot.classList.toggle('active', i === index);
    });
    
    gallery.dataset.current = index;
    resetGalleryTimer(cardId);
}

// Timer automático para trocar imagens
function startGalleryTimer(cardId) {
    if (galleryIntervals[cardId]) return;
    
    galleryIntervals[cardId] = setInterval(function() {
        galleryNav(cardId, 1);
    }, 3000); // Troca a cada 3 segundos
}

function stopGalleryTimer(cardId) {
    if (galleryIntervals[cardId]) {
        clearInterval(galleryIntervals[cardId]);
        delete galleryIntervals[cardId];
    }
}

function resetGalleryTimer(cardId) {
    stopGalleryTimer(cardId);
    startGalleryTimer(cardId);
}

// Inicializar galerias quando a página carrega
function initGalleries() {
    var cards = document.querySelectorAll('.product-card[data-imagens]');
    
    cards.forEach(function(card) {
        var cardId = card.id;
        var gallery = card.querySelector('.product-gallery');
        if (!gallery) return;
        
        // Iniciar timer automático
        startGalleryTimer(cardId);
        
        // Pausar ao passar o mouse
        card.addEventListener('mouseenter', function() {
            stopGalleryTimer(cardId);
        });
        
        // Retomar ao tirar o mouse
        card.addEventListener('mouseleave', function() {
            startGalleryTimer(cardId);
        });
        
        // Click nos dots
        var dots = gallery.querySelectorAll('.gallery-dot');
        dots.forEach(function(dot) {
            dot.addEventListener('click', function(e) {
                e.stopPropagation();
                var index = parseInt(this.dataset.index);
                galleryGoTo(cardId, index);
            });
        });
    });
}

// ==========================================
// CARRINHO DE COMPRAS
// ==========================================
const Cart = {
    items: [],
    
    init() {
        this.load();
        this.updateUI();
        this.bindEvents();
    },
    
    load() {
        const saved = localStorage.getItem('krstore_cart');
        if (saved) {
            try {
                this.items = JSON.parse(saved);
            } catch (e) {
                this.items = [];
            }
        }
    },
    
    save() {
        localStorage.setItem('krstore_cart', JSON.stringify(this.items));
    },
    
    add(product) {
        const existingIndex = this.items.findIndex(
            item => item.id === product.id && 
                    item.tamanho === product.tamanho && 
                    item.cor === product.cor
        );
        
        if (existingIndex > -1) {
            this.items[existingIndex].quantidade++;
        } else {
            this.items.push({
                ...product,
                quantidade: 1
            });
        }
        
        this.save();
        this.updateUI();
        this.showToast('Produto adicionado ao carrinho!', 'success');
    },
    
    remove(index) {
        this.items.splice(index, 1);
        this.save();
        this.updateUI();
        this.renderItems();
    },
    
    updateQuantity(index, delta) {
        const item = this.items[index];
        if (item) {
            item.quantidade += delta;
            if (item.quantidade <= 0) {
                this.remove(index);
            } else {
                this.save();
                this.updateUI();
                this.renderItems();
            }
        }
    },
    
    clear() {
        this.items = [];
        this.save();
        this.updateUI();
        this.renderItems();
    },
    
    getTotal() {
        return this.items.reduce((total, item) => {
            return total + (item.preco * item.quantidade);
        }, 0);
    },
    
    getCount() {
        return this.items.reduce((count, item) => count + item.quantidade, 0);
    },
    
    updateUI() {
        const countElements = document.querySelectorAll('.cart-count');
        const count = this.getCount();
        
        countElements.forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
        });
    },
    
    renderItems() {
        const container = document.getElementById('cart-items');
        if (!container) return;
        
        if (this.items.length === 0) {
            container.innerHTML = `
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <p>Seu carrinho está vazio</p>
                    <p style="font-size: 0.85rem; margin-top: 0.5rem;">Adicione produtos para continuar</p>
                </div>
            `;
        } else {
            container.innerHTML = this.items.map((item, index) => `
                <div class="cart-item" data-index="${index}">
                    <div class="cart-item-image">
                        <img src="${item.imagem}" alt="${item.nome}" loading="lazy">
                    </div>
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.nome}</div>
                        <div class="cart-item-details">
                            ${item.tamanho ? `Tam: ${item.tamanho}` : ''}
                            ${item.cor ? ` | Cor: ${item.cor}` : ''}
                        </div>
                        <div class="cart-item-price">R$ ${item.preco.toFixed(2).replace('.', ',')}</div>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button class="qty-btn" onclick="Cart.updateQuantity(${index}, -1)">−</button>
                                <span class="qty-value">${item.quantidade}</span>
                                <button class="qty-btn" onclick="Cart.updateQuantity(${index}, 1)">+</button>
                            </div>
                            <button class="remove-item" onclick="Cart.remove(${index})" title="Remover">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        
        // Atualizar totais
        this.updateTotals();
    },
    
    updateTotals() {
        const subtotalEl = document.getElementById('cart-subtotal');
        const totalEl = document.getElementById('cart-total');
        const total = this.getTotal();
        
        if (subtotalEl) {
            subtotalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        if (totalEl) {
            totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
    },
    
    bindEvents() {
        // Abrir carrinho
        document.querySelectorAll('.cart-btn, .open-cart').forEach(btn => {
            btn.addEventListener('click', () => this.open());
        });
        
        // Fechar carrinho
        document.querySelectorAll('.cart-close, .cart-overlay').forEach(el => {
            el.addEventListener('click', () => this.close());
        });
        
        // Finalizar compra
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => this.checkout());
        }
    },
    
    open() {
        this.renderItems();
        document.querySelector('.cart-overlay')?.classList.add('active');
        document.querySelector('.cart-sidebar')?.classList.add('active');
        document.body.style.overflow = 'hidden';
    },
    
    close() {
        document.querySelector('.cart-overlay')?.classList.remove('active');
        document.querySelector('.cart-sidebar')?.classList.remove('active');
        document.body.style.overflow = '';
    },
    
    checkout() {
        if (this.items.length === 0) {
            this.showToast('Seu carrinho está vazio!', 'error');
            return;
        }
        
        // Pegar dados do cliente
        var customerName = document.getElementById('customer-name').value.trim();
        var customerPhone = document.getElementById('customer-phone').value.trim();
        var customerCep = document.getElementById('customer-cep').value.trim();
        var customerAddress = document.getElementById('customer-address').value.trim();
        
        // Validar campos obrigatórios
        if (!customerName) {
            this.showToast('Por favor, informe seu nome!', 'error');
            document.getElementById('customer-name').focus();
            return;
        }
        
        if (!customerPhone) {
            this.showToast('Por favor, informe seu telefone!', 'error');
            document.getElementById('customer-phone').focus();
            return;
        }
        
        // Montar mensagem para WhatsApp (sem emojis problemáticos)
        var message = '*PEDIDO - KRSTORE MODA MASCULINA*\n';
        message += '================================\n\n';
        
        message += '*DADOS DO CLIENTE:*\n';
        message += 'Nome: ' + customerName + '\n';
        message += 'Telefone: ' + customerPhone + '\n';
        if (customerCep) message += 'CEP: ' + customerCep + '\n';
        if (customerAddress) message += 'Endereco: ' + customerAddress + '\n';
        message += '\n================================\n';
        message += '*ITENS DO PEDIDO:*\n';
        message += '--------------------------------\n';
        
        var self = this;
        this.items.forEach(function(item, index) {
            message += '\n' + (index + 1) + '. ' + item.nome + '\n';
            if (item.tamanho) message += '   Tamanho: ' + item.tamanho + '\n';
            if (item.cor) message += '   Cor: ' + item.cor + '\n';
            message += '   Qtd: ' + item.quantidade + '\n';
            message += '   Preco: R$ ' + (item.preco * item.quantidade).toFixed(2).replace('.', ',') + '\n';
        });
        
        message += '\n--------------------------------\n';
        message += '*TOTAL: R$ ' + this.getTotal().toFixed(2).replace('.', ',') + '*\n';
        message += '================================\n\n';
        message += 'Pedido realizado via vendaskrstore.shop';
        
        // Codificar e abrir WhatsApp
        var encodedMessage = encodeURIComponent(message);
        var whatsappNumber = '5585985009840';
        var whatsappUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodedMessage;
        
        window.open(whatsappUrl, '_blank');
        
        // Limpar carrinho após enviar
        this.clear();
        this.close();
        this.showToast('Pedido enviado! Aguarde nosso contato.', 'success');
    },
    
    showToast(message, type = 'success') {
        const container = document.querySelector('.toast-container') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span>${type === 'success' ? '✓' : '✕'}</span>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    },
    
    createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }
};

// ==========================================
// CARROSSEL DE PRODUTOS
// ==========================================
const Carousel = {
    init() {
        document.querySelectorAll('.carousel-container').forEach(container => {
            const track = container.querySelector('.carousel-track');
            const prevBtn = container.querySelector('.carousel-btn.prev');
            const nextBtn = container.querySelector('.carousel-btn.next');
            
            if (!track) return;
            
            const scrollAmount = 300;
            
            prevBtn?.addEventListener('click', () => {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            
            nextBtn?.addEventListener('click', () => {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
            
            // Atualizar visibilidade dos botões
            const updateButtons = () => {
                if (prevBtn) {
                    prevBtn.style.opacity = track.scrollLeft > 0 ? '1' : '0.3';
                }
                if (nextBtn) {
                    const maxScroll = track.scrollWidth - track.clientWidth;
                    nextBtn.style.opacity = track.scrollLeft < maxScroll - 10 ? '1' : '0.3';
                }
            };
            
            track.addEventListener('scroll', updateButtons);
            updateButtons();
        });
    }
};

// ==========================================
// ADICIONAR AO CARRINHO
// ==========================================
function addToCart(productId, nome, preco, imagem, tamanho = null, cor = null) {
    console.log('addToCart chamado:', {productId, nome, preco, imagem, tamanho, cor});
    Cart.add({
        id: productId,
        nome: nome,
        preco: parseFloat(preco),
        imagem: imagem,
        tamanho: tamanho,
        cor: cor
    });
}

// Variável global para armazenar produto atual no modal
var currentModalProduct = null;
var modalGalleryIndex = 0;
var modalGalleryImages = [];
var modalGalleryInterval = null;

// Modal de seleção de tamanho/cor
function showProductModal(product) {
    console.log('showProductModal chamado:', product);
    const modal = document.getElementById('product-modal');
    if (!modal) {
        console.error('Modal #product-modal não encontrado!');
        return;
    }
    
    currentModalProduct = product;
    
    // Configurar galeria de imagens no modal
    modalGalleryImages = product.imagens && product.imagens.length > 0 ? product.imagens : [product.imagem];
    modalGalleryIndex = 0;
    
    setupModalGallery();
    
    document.getElementById('modal-product-name').textContent = product.nome;
    document.getElementById('modal-product-price').textContent = `R$ ${product.preco.toFixed(2).replace('.', ',')}`;
    
    const tamanhosContainer = document.getElementById('modal-tamanhos');
    const coresContainer = document.getElementById('modal-cores');
    
    // Verificar se tem variações
    if (product.variacoes && product.variacoes.length > 0) {
        // MODO VARIAÇÕES - Usar select/dropdown
        tamanhosContainer.innerHTML = `
            <label style="margin-bottom: 0.5rem; display: block; font-weight: 600;">📦 Escolha o item:</label>
            <select id="variacao-select" class="modal-select" onchange="onVariationChange()">
                <option value="" disabled selected>Selecione ⬇</option>
                ${product.variacoes.map((v, i) => `
                    <option value="${i}">${v.nome}</option>
                `).join('')}
            </select>
        `;
        tamanhosContainer.style.display = 'block';
        
        // Esconder cores até selecionar variação
        coresContainer.innerHTML = '';
        coresContainer.style.display = 'none';
        
    } else {
        // MODO SIMPLES - Tamanhos e cores únicos
        const tamanhos = product.tamanhos ? product.tamanhos.split(',') : [];
        const cores = product.cores ? product.cores.split(',') : [];
        
        if (tamanhos.length > 0) {
            tamanhosContainer.innerHTML = `
                <label>Tamanho:</label>
                <div class="size-options">
                    ${tamanhos.map((t, i) => `
                        <label class="size-option">
                            <input type="radio" name="tamanho" value="${t.trim()}" ${i === 0 ? 'checked' : ''}>
                            <span>${t.trim()}</span>
                        </label>
                    `).join('')}
                </div>
            `;
            tamanhosContainer.style.display = 'block';
        } else {
            tamanhosContainer.style.display = 'none';
        }
        
        if (cores.length > 0) {
            coresContainer.innerHTML = `
                <label>Cor:</label>
                <div class="color-options">
                    ${cores.map((c, i) => `
                        <label class="color-option">
                            <input type="radio" name="cor" value="${c.trim()}" ${i === 0 ? 'checked' : ''}>
                            <span>${c.trim()}</span>
                        </label>
                    `).join('')}
                </div>
            `;
            coresContainer.style.display = 'block';
        } else {
            coresContainer.style.display = 'none';
        }
    }
    
    // Botão de adicionar
    const addBtn = document.getElementById('modal-add-btn');
    addBtn.onclick = () => {
        let itemNome = product.nome;
        let tamanho = null;
        let cor = null;
        
        if (product.variacoes && product.variacoes.length > 0) {
            // Modo variações
            const varSelect = document.getElementById('variacao-select');
            if (!varSelect || varSelect.value === '') {
                Cart.showToast('Selecione um item primeiro!', 'error');
                return;
            }
            const varIndex = parseInt(varSelect.value);
            const variacao = product.variacoes[varIndex];
            itemNome = product.nome + ' - ' + variacao.nome;
            tamanho = document.querySelector('input[name="var_tamanho"]:checked')?.value || null;
            cor = document.querySelector('input[name="var_cor"]:checked')?.value || null;
        } else {
            // Modo simples
            tamanho = document.querySelector('input[name="tamanho"]:checked')?.value || null;
            cor = document.querySelector('input[name="cor"]:checked')?.value || null;
        }
        
        addToCart(product.id, itemNome, product.preco, product.imagem, tamanho, cor);
        closeProductModal();
    };
    
    // Guardar produto para geração de stories
    currentProductForStories = product;
    
    // Guardar variações para o evento de seleção
    window.currentProductVariations = product.variacoes;
    
    // Botão de compartilhar stories
    var storiesBtn = document.getElementById('share-stories-btn');
    if (storiesBtn) {
        storiesBtn.onclick = function() {
            generateInstagramStories();
        };
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Atualizar detalhes da variação selecionada
function updateVariationDetails(variacoes, index, container) {
    const v = variacoes[index];
    const tamanhos = v.tamanhos ? v.tamanhos.split(',') : [];
    const cores = v.cores ? v.cores.split(',') : [];
    
    let html = '';
    
    if (tamanhos.length > 0) {
        html += `
            <label style="margin-top: 0.5rem;">Tamanho:</label>
            <div class="size-options">
                ${tamanhos.map((t, i) => `
                    <label class="size-option">
                        <input type="radio" name="var_tamanho" value="${t.trim()}" ${i === 0 ? 'checked' : ''}>
                        <span>${t.trim()}</span>
                    </label>
                `).join('')}
            </div>
        `;
    }
    
    if (cores.length > 0) {
        html += `
            <label style="margin-top: 0.5rem;">Cor:</label>
            <div class="color-options">
                ${cores.map((c, i) => `
                    <label class="color-option">
                        <input type="radio" name="var_cor" value="${c.trim()}" ${i === 0 ? 'checked' : ''}>
                        <span>${c.trim()}</span>
                    </label>
                `).join('')}
            </div>
        `;
    }
    
    container.innerHTML = html;
    container.style.display = html ? 'block' : 'none';
}

// Quando seleciona uma variação no dropdown
function onVariationChange() {
    var select = document.getElementById('variacao-select');
    if (!select || select.value === '' || !currentModalProduct || !currentModalProduct.variacoes) return;
    
    var index = parseInt(select.value);
    var coresContainer = document.getElementById('modal-cores');
    updateVariationDetails(currentModalProduct.variacoes, index, coresContainer);
}

// Selecionar variação (para compatibilidade)
function selectVariation(index) {
    var select = document.getElementById('variacao-select');
    if (select) {
        select.value = index;
        onVariationChange();
    }
}

// ==========================================
// GALERIA DE IMAGENS NO MODAL
// ==========================================

function setupModalGallery() {
    var galleryContainer = document.getElementById('modal-gallery');
    var dotsContainer = document.getElementById('modal-gallery-dots');
    var prevBtn = document.getElementById('modal-gallery-prev');
    var nextBtn = document.getElementById('modal-gallery-next');
    
    if (!galleryContainer) return;
    
    // Limpar galeria
    galleryContainer.innerHTML = '';
    dotsContainer.innerHTML = '';
    
    // Adicionar imagens
    modalGalleryImages.forEach(function(imgSrc, i) {
        var img = document.createElement('img');
        img.src = imgSrc;
        img.alt = 'Imagem do produto';
        img.className = 'modal-gallery-img' + (i === 0 ? ' active' : '');
        img.dataset.index = i;
        galleryContainer.appendChild(img);
        
        // Adicionar dot
        var dot = document.createElement('span');
        dot.className = 'modal-gallery-dot' + (i === 0 ? ' active' : '');
        dot.dataset.index = i;
        dot.onclick = function() {
            modalGalleryGoTo(parseInt(this.dataset.index));
        };
        dotsContainer.appendChild(dot);
    });
    
    // Mostrar/esconder navegação
    var hasMultiple = modalGalleryImages.length > 1;
    prevBtn.style.display = hasMultiple ? 'flex' : 'none';
    nextBtn.style.display = hasMultiple ? 'flex' : 'none';
    dotsContainer.style.display = hasMultiple ? 'flex' : 'none';
    
    // Iniciar timer automático se houver múltiplas imagens
    stopModalGalleryTimer();
    if (hasMultiple) {
        startModalGalleryTimer();
    }
}

function modalGalleryNav(direction) {
    var newIndex = modalGalleryIndex + direction;
    if (newIndex < 0) newIndex = modalGalleryImages.length - 1;
    if (newIndex >= modalGalleryImages.length) newIndex = 0;
    modalGalleryGoTo(newIndex);
}

function modalGalleryGoTo(index) {
    modalGalleryIndex = index;
    
    var galleryContainer = document.getElementById('modal-gallery');
    var dotsContainer = document.getElementById('modal-gallery-dots');
    
    if (!galleryContainer) return;
    
    var images = galleryContainer.querySelectorAll('.modal-gallery-img');
    var dots = dotsContainer.querySelectorAll('.modal-gallery-dot');
    
    images.forEach(function(img, i) {
        img.classList.toggle('active', i === index);
    });
    
    dots.forEach(function(dot, i) {
        dot.classList.toggle('active', i === index);
    });
    
    // Resetar timer
    resetModalGalleryTimer();
}

function startModalGalleryTimer() {
    if (modalGalleryInterval) return;
    modalGalleryInterval = setInterval(function() {
        modalGalleryNav(1);
    }, 3500);
}

function stopModalGalleryTimer() {
    if (modalGalleryInterval) {
        clearInterval(modalGalleryInterval);
        modalGalleryInterval = null;
    }
}

function resetModalGalleryTimer() {
    stopModalGalleryTimer();
    if (modalGalleryImages.length > 1) {
        startModalGalleryTimer();
    }
}

function closeProductModal() {
    const modal = document.getElementById('product-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        stopModalGalleryTimer();
    }
}

// ==========================================
// GERADOR DE STORIES PARA INSTAGRAM
// ==========================================
var currentProductForStories = null;

function generateInstagramStories() {
    if (!currentProductForStories) {
        alert('Nenhum produto selecionado!');
        return;
    }
    
    var product = currentProductForStories;
    var canvas = document.getElementById('stories-canvas');
    var ctx = canvas.getContext('2d');
    
    // Proporções do Stories: 1080x1920 (9:16)
    canvas.width = 1080;
    canvas.height = 1920;
    
    // Fundo gradiente escuro
    var gradient = ctx.createLinearGradient(0, 0, 0, 1920);
    gradient.addColorStop(0, '#0a0a0a');
    gradient.addColorStop(0.5, '#111111');
    gradient.addColorStop(1, '#0a0a0a');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, 1080, 1920);
    
    // Adicionar brilho verde sutil no topo
    var glowGradient = ctx.createRadialGradient(540, 200, 0, 540, 200, 600);
    glowGradient.addColorStop(0, 'rgba(0, 255, 136, 0.15)');
    glowGradient.addColorStop(1, 'rgba(0, 255, 136, 0)');
    ctx.fillStyle = glowGradient;
    ctx.fillRect(0, 0, 1080, 800);
    
    // Logo/Nome da loja no topo
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 48px Inter, Arial, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('KRStore', 540, 100);
    
    ctx.fillStyle = '#00ff88';
    ctx.font = '24px Inter, Arial, sans-serif';
    ctx.fillText('MODA MASCULINA', 540, 140);
    
    // Carregar imagem do produto
    var productImg = new Image();
    productImg.crossOrigin = 'anonymous';
    productImg.onload = function() {
        // Área da imagem do produto (quadrada com borda arredondada simulada)
        var imgX = 90;
        var imgY = 200;
        var imgW = 900;
        var imgH = 900;
        
        // Borda verde neon
        ctx.strokeStyle = '#00ff88';
        ctx.lineWidth = 4;
        ctx.strokeRect(imgX - 2, imgY - 2, imgW + 4, imgH + 4);
        
        // Desenhar imagem
        ctx.drawImage(productImg, imgX, imgY, imgW, imgH);
        
        // Nome do produto
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 52px Inter, Arial, sans-serif';
        ctx.textAlign = 'center';
        
        // Quebrar nome se muito longo
        var nome = product.nome;
        if (nome.length > 25) {
            var palavras = nome.split(' ');
            var linha1 = '';
            var linha2 = '';
            palavras.forEach(function(palavra) {
                if (linha1.length + palavra.length < 20) {
                    linha1 += (linha1 ? ' ' : '') + palavra;
                } else {
                    linha2 += (linha2 ? ' ' : '') + palavra;
                }
            });
            ctx.fillText(linha1, 540, 1180);
            if (linha2) ctx.fillText(linha2, 540, 1240);
        } else {
            ctx.fillText(nome, 540, 1200);
        }
        
        // Preço
        ctx.fillStyle = '#00ff88';
        ctx.font = 'bold 72px Inter, Arial, sans-serif';
        ctx.fillText('R$ ' + product.preco.toFixed(2).replace('.', ','), 540, 1320);
        
        // Tamanhos disponíveis
        if (product.tamanhos) {
            ctx.fillStyle = '#888888';
            ctx.font = '28px Inter, Arial, sans-serif';
            ctx.fillText('Tamanhos: ' + product.tamanhos, 540, 1390);
        }
        
        // Linha divisória
        ctx.strokeStyle = '#333333';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(200, 1450);
        ctx.lineTo(880, 1450);
        ctx.stroke();
        
        // QR Code - usando API externa
        var productUrl = 'https://vendaskrstore.shop/catalog.php?produto=' + product.id;
        var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(productUrl) + '&bgcolor=0a0a0a&color=00ff88';
        
        var qrImg = new Image();
        qrImg.crossOrigin = 'anonymous';
        qrImg.onload = function() {
            // QR Code
            ctx.drawImage(qrImg, 100, 1500, 180, 180);
            
            // Texto ao lado do QR
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 32px Inter, Arial, sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText('Escaneie e compre!', 320, 1560);
            
            ctx.fillStyle = '#888888';
            ctx.font = '24px Inter, Arial, sans-serif';
            ctx.fillText('vendaskrstore.shop', 320, 1600);
            
            // Instagram
            ctx.fillStyle = '#00ff88';
            ctx.font = 'bold 28px Inter, Arial, sans-serif';
            ctx.fillText('@krstore2026', 320, 1650);
            
            // WhatsApp
            ctx.fillStyle = '#25d366';
            ctx.font = '24px Inter, Arial, sans-serif';
            ctx.fillText('(85) 98500-9840', 320, 1690);
            
            // Marca d'água
            ctx.fillStyle = 'rgba(255,255,255,0.3)';
            ctx.font = '18px Inter, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Arraste para cima para comprar', 540, 1860);
            
            // Download da imagem
            downloadStories(canvas);
        };
        
        qrImg.onerror = function() {
            // Se QR falhar, gerar sem ele
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 32px Inter, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('vendaskrstore.shop', 540, 1550);
            
            ctx.fillStyle = '#00ff88';
            ctx.font = 'bold 28px Inter, Arial, sans-serif';
            ctx.fillText('@krstore2026', 540, 1600);
            
            ctx.fillStyle = '#25d366';
            ctx.font = '24px Inter, Arial, sans-serif';
            ctx.fillText('WhatsApp: (85) 98500-9840', 540, 1650);
            
            downloadStories(canvas);
        };
        
        qrImg.src = qrUrl;
    };
    
    productImg.onerror = function() {
        alert('Erro ao carregar imagem do produto. Tente novamente.');
    };
    
    productImg.src = product.imagem;
}

function downloadStories(canvas) {
    try {
        var link = document.createElement('a');
        link.download = 'krstore-produto-stories.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        Cart.showToast('Imagem gerada! Salva no seu dispositivo.', 'success');
    } catch (e) {
        // Fallback: abrir em nova aba
        var dataUrl = canvas.toDataURL('image/png');
        var newWindow = window.open();
        newWindow.document.write('<img src="' + dataUrl + '" style="max-width:100%;">');
        newWindow.document.write('<p>Clique com botão direito na imagem e salve.</p>');
        Cart.showToast('Imagem gerada! Salve a imagem da nova aba.', 'success');
    }
}

// ==========================================
// FILTROS DE CATEGORIA
// ==========================================
const Filter = {
    init() {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                Filter.apply(filter);
            });
        });
    },
    
    apply(filter) {
        document.querySelectorAll('.products-section[data-category]').forEach(section => {
            if (filter === 'all' || section.dataset.category === filter) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
};

// ==========================================
// BUSCA DE PRODUTOS
// ==========================================
const Search = {
    init() {
        const searchInput = document.querySelector('.search-input');
        if (!searchInput) return;
        
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const term = this.value.trim();
                if (term.length >= 2) {
                    Search.perform(term);
                }
            }, 300);
        });
    },
    
    perform(term) {
        // Implementar busca AJAX se necessário
        window.location.href = `?busca=${encodeURIComponent(term)}`;
    }
};

// ==========================================
// MOBILE MENU
// ==========================================
const MobileMenu = {
    init() {
        const menuBtn = document.querySelector('.mobile-menu-btn');
        const navMenu = document.querySelector('.nav-menu');
        
        menuBtn?.addEventListener('click', () => {
            navMenu?.classList.toggle('active');
            menuBtn.classList.toggle('active');
        });
    }
};

// ==========================================
// LAZY LOADING DE IMAGENS
// ==========================================
const LazyLoad = {
    init() {
        const images = document.querySelectorAll('img[data-src]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => observer.observe(img));
    }
};

// ==========================================
// INICIALIZAÇÃO
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado - Inicializando...');
    Cart.init();
    Carousel.init();
    Filter.init();
    Search.init();
    MobileMenu.init();
    LazyLoad.init();
    initGalleries(); // Inicializar galerias de imagens
    
    // Event listener único para TODOS os botões de abrir modal
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-open-modal');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            
            var variacoesStr = btn.getAttribute('data-variacoes') || '';
            var variacoes = null;
            if (variacoesStr) {
                try {
                    variacoes = JSON.parse(variacoesStr);
                } catch (err) {
                    console.log('Erro ao parsear variações:', err);
                }
            }
            
            var imagensStr = btn.getAttribute('data-imagens') || '';
            var imagens = [];
            if (imagensStr) {
                try {
                    imagens = JSON.parse(imagensStr);
                } catch (err) {
                    console.log('Erro ao parsear imagens:', err);
                }
            }
            
            var product = {
                id: parseInt(btn.getAttribute('data-id')) || 0,
                nome: btn.getAttribute('data-nome') || '',
                preco: parseFloat(btn.getAttribute('data-preco')) || 0,
                imagem: btn.getAttribute('data-imagem') || '',
                tamanhos: btn.getAttribute('data-tamanhos') || '',
                cores: btn.getAttribute('data-cores') || '',
                variacoes: variacoes,
                imagens: imagens
            };
            
            console.log('Produto clicado:', product);
            showProductModal(product);
        }
    });
    
    console.log('Inicialização completa!');
    
    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            Cart.close();
            closeProductModal();
        }
    });
});

// Expor funções para uso global (onclick nos cards)
window.showProductModal = showProductModal;
window.closeProductModal = closeProductModal;
window.addToCart = addToCart;
window.Cart = Cart;
window.generateInstagramStories = generateInstagramStories;
window.selectVariation = selectVariation;
window.galleryNav = galleryNav;
window.galleryGoTo = galleryGoTo;
window.updateVariationDetails = updateVariationDetails;
window.onVariationChange = onVariationChange;
window.modalGalleryNav = modalGalleryNav;
window.modalGalleryGoTo = modalGalleryGoTo;

console.log('KRStore JS carregado com sucesso!');
