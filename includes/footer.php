    <!-- Cart Sidebar -->
    <div class="cart-overlay"></div>
    <aside class="cart-sidebar">
        <div class="cart-header">
            <h3 class="cart-title">🛒 Seu Carrinho</h3>
            <button class="cart-close">✕</button>
        </div>
        
        <div class="cart-items" id="cart-items">
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <p>Seu carrinho está vazio</p>
                <p style="font-size: 0.85rem; margin-top: 0.5rem;">Adicione produtos para continuar</p>
            </div>
        </div>
        
        <div class="cart-footer">
            <!-- Dados do Cliente -->
            <div class="cart-customer-info">
                <input type="text" id="customer-name" class="cart-input" placeholder="Seu nome *" required>
                <input type="tel" id="customer-phone" class="cart-input" placeholder="Telefone/WhatsApp *" required>
                <input type="text" id="customer-cep" class="cart-input" placeholder="CEP (opcional)">
                <input type="text" id="customer-address" class="cart-input" placeholder="Endereço completo (opcional)">
            </div>
            
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <span id="cart-subtotal">R$ 0,00</span>
                </div>
                <div class="cart-summary-row">
                    <span>Frete</span>
                    <span>A combinar</span>
                </div>
                <div class="cart-summary-row total">
                    <span>Total</span>
                    <span class="value" id="cart-total">R$ 0,00</span>
                </div>
            </div>
            
            <button class="checkout-btn" id="checkout-btn">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Finalizar pelo WhatsApp
            </button>
        </div>
    </aside>
    
    <!-- Product Modal -->
    <div class="modal" id="product-modal">
        <div class="modal-overlay" onclick="closeProductModal()"></div>
        <div class="modal-content">
            <div class="modal-header-buttons">
                <button class="modal-close" onclick="closeProductModal()">✕</button>
                <button class="modal-share-btn" id="share-stories-btn" title="Criar imagem para Stories">
                    📷
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-product-image">
                    <!-- Container da galeria do modal -->
                    <div id="modal-gallery" class="modal-gallery">
                        <img id="modal-product-image" src="" alt="" class="modal-gallery-img active">
                    </div>
                    <!-- Navegação da galeria -->
                    <button type="button" class="modal-gallery-nav modal-gallery-prev" id="modal-gallery-prev" onclick="modalGalleryNav(-1)">‹</button>
                    <button type="button" class="modal-gallery-nav modal-gallery-next" id="modal-gallery-next" onclick="modalGalleryNav(1)">›</button>
                    <!-- Indicadores -->
                    <div class="modal-gallery-dots" id="modal-gallery-dots"></div>
                </div>
                <div class="modal-product-info">
                    <h3 id="modal-product-name"></h3>
                    <p class="modal-price" id="modal-product-price"></p>
                    <div id="modal-tamanhos"></div>
                    <div id="modal-cores"></div>
                    <button class="btn btn-primary btn-lg btn-block" id="modal-add-btn">
                        🛒 Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Canvas oculto para gerar stories -->
    <canvas id="stories-canvas" style="display: none;"></canvas>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand -->
                <div class="footer-brand">
                    <a href="catalog.php" class="logo">
                        <img src="https://i.ibb.co/whsVT0pp/unnamed-1.jpg" alt="KRStore Logo">
                        <span class="logo-text">KR<span>Store</span></span>
                    </a>
                    <p>Moda masculina com estilo e qualidade. Encontre peças que combinam com você.</p>
                    <div class="social-links">
                        <a href="https://instagram.com/<?php echo INSTAGRAM_USER; ?>" target="_blank" class="social-link" title="Instagram">
                            📷
                        </a>
                        <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="social-link" title="WhatsApp">
                            💬
                        </a>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="footer-section">
                    <h4>Categorias</h4>
                    <ul>
                        <li><a href="catalog.php#camisetas">Camisetas</a></li>
                        <li><a href="catalog.php#camisas">Camisas</a></li>
                        <li><a href="catalog.php#calcas">Calças</a></li>
                        <li><a href="catalog.php#bermudas">Bermudas</a></li>
                    </ul>
                </div>
                
                <!-- Styles -->
                <div class="footer-section">
                    <h4>Estilos</h4>
                    <ul>
                        <li><a href="catalog.php?estilo=casual">Casual</a></li>
                        <li><a href="catalog.php?estilo=social">Social</a></li>
                        <li><a href="catalog.php?estilo=urbano">Urbano</a></li>
                        <li><a href="catalog.php?estilo=esportivo">Esportivo</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="footer-section">
                    <h4>Atendimento</h4>
                    <ul>
                        <li><a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank">WhatsApp</a></li>
                        <li><a href="https://instagram.com/<?php echo INSTAGRAM_USER; ?>" target="_blank">Instagram</a></li>
                        <li><a href="mailto:contato@vendaskrstore.shop">E-mail</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>
