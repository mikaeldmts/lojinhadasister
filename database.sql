-- =============================================
-- Lojinha da Irmã - Banco de Dados
-- Execute este arquivo no phpMyAdmin
-- =============================================

-- Criar banco de dados (caso não exista)
CREATE DATABASE IF NOT EXISTS `lojinha1_banco` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `lojinha1_banco`;

-- =============================================
-- Tabela de Categorias de Tipo (Camisetas, Calças, etc)
-- =============================================
CREATE TABLE IF NOT EXISTS `categorias_tipo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ordem` INT(11) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de Categorias de Subtipo
-- =============================================
CREATE TABLE IF NOT EXISTS `categorias_subtipo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `cor` VARCHAR(7) DEFAULT '#ffffff',
    `ordem` INT(11) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de Produtos
-- =============================================
CREATE TABLE IF NOT EXISTS `produtos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `preco` DECIMAL(10,2) NOT NULL,
    `preco_promocional` DECIMAL(10,2) DEFAULT NULL,
    `categoria_tipo_id` INT(11) NOT NULL,
    `categoria_subtipo_id` INT(11) DEFAULT NULL,
    `imagem_principal` VARCHAR(255) DEFAULT NULL,
    `imagens_adicionais` TEXT DEFAULT NULL COMMENT 'JSON com URLs das imagens adicionais',
    `tamanhos` VARCHAR(100) DEFAULT 'P,M,G,GG',
    `cores` VARCHAR(255) DEFAULT NULL,
    `variacoes` TEXT DEFAULT NULL COMMENT 'JSON com variações do produto (ex: múltiplas camisas em uma foto)',
    `estoque` INT(11) DEFAULT 0,
    `destaque` TINYINT(1) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `visualizacoes` INT(11) DEFAULT 0,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `categoria_tipo_id` (`categoria_tipo_id`),
    KEY `categoria_subtipo_id` (`categoria_subtipo_id`),
    CONSTRAINT `fk_produto_tipo` FOREIGN KEY (`categoria_tipo_id`) REFERENCES `categorias_tipo` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_produto_subtipo` FOREIGN KEY (`categoria_subtipo_id`) REFERENCES `categorias_subtipo` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar campos se não existirem (para bancos já criados)
-- Execute manualmente: 
-- ALTER TABLE `produtos` ADD COLUMN `variacoes` TEXT DEFAULT NULL AFTER `cores`;
-- ALTER TABLE `produtos` ADD COLUMN `imagens_adicionais` TEXT DEFAULT NULL AFTER `imagem_principal`;
-- Para renomear categorias_estilo para categorias_subtipo:
-- RENAME TABLE `categorias_estilo` TO `categorias_subtipo`;
-- ALTER TABLE `produtos` CHANGE `categoria_estilo_id` `categoria_subtipo_id` INT(11) DEFAULT NULL;

-- =============================================
-- Tabela de Imagens dos Produtos
-- =============================================
CREATE TABLE IF NOT EXISTS `produto_imagens` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `produto_id` INT(11) NOT NULL,
    `imagem` VARCHAR(255) NOT NULL,
    `ordem` INT(11) DEFAULT 0,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `produto_id` (`produto_id`),
    CONSTRAINT `fk_imagem_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de Configurações do Site
-- =============================================
CREATE TABLE IF NOT EXISTS `configuracoes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `chave` VARCHAR(100) NOT NULL,
    `valor` TEXT DEFAULT NULL,
    `tipo` VARCHAR(50) DEFAULT 'text',
    `atualizado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de Banners
-- =============================================
CREATE TABLE IF NOT EXISTS `banners` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(255) DEFAULT NULL,
    `subtitulo` VARCHAR(255) DEFAULT NULL,
    `imagem` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) DEFAULT NULL,
    `ordem` INT(11) DEFAULT 0,
    `ativo` TINYINT(1) DEFAULT 1,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de Logs de Acesso Admin
-- =============================================
CREATE TABLE IF NOT EXISTS `admin_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `acao` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Inserir Categorias de Tipo Padrão
-- =============================================
INSERT INTO `categorias_tipo` (`nome`, `slug`, `descricao`, `ordem`, `ativo`) VALUES
('Roupas', 'roupas', 'Roupas femininas e masculinas', 1, 1),
('Acessórios', 'acessorios', 'Acessórios diversos', 2, 1),
('Calçados', 'calcados', 'Calçados femininos e masculinos', 3, 1),
('Decoração', 'decoracao', 'Itens de decoração', 4, 1),
('Utilidades', 'utilidades', 'Produtos úteis para o dia a dia', 5, 1);

-- =============================================
-- Inserir Categorias de Subtipo Padrão
-- =============================================
INSERT INTO `categorias_subtipo` (`nome`, `slug`, `descricao`, `cor`, `ordem`, `ativo`) VALUES
('Feminino', 'feminino', 'Produtos femininos', '#ff69b4', 1, 1),
('Masculino', 'masculino', 'Produtos masculinos', '#4a90d9', 2, 1),
('Infantil', 'infantil', 'Produtos infantis', '#ffa500', 3, 1),
('Unissex', 'unissex', 'Produtos para todos', '#9370db', 4, 1),
('Casa', 'casa', 'Itens para casa', '#20b2aa', 5, 1),
('Presente', 'presente', 'Ideias de presente', '#ff1493', 6, 1),
('Promoção', 'promocao', 'Produtos em promoção', '#ff4500', 7, 1);

-- =============================================
-- Inserir Configurações Padrão
-- =============================================
INSERT INTO `configuracoes` (`chave`, `valor`, `tipo`) VALUES
('site_nome', 'Lojinha da Irmã', 'text'),
('site_descricao', 'Loja de variedades com produtos exclusivos', 'textarea'),
('whatsapp', '5585880556', 'text'),
('instagram', 'lojinhadairmad', 'text'),
('email', 'contato@lojinhadairma.shop', 'email'),
('endereco', '', 'textarea'),
('frete_gratis_minimo', '299.00', 'number'),
('desconto_pix', '10', 'number');

-- =============================================
-- Inserir Produtos de Exemplo
-- =============================================
INSERT INTO `produtos` (`nome`, `slug`, `descricao`, `preco`, `preco_promocional`, `categoria_tipo_id`, `categoria_subtipo_id`, `imagem_principal`, `tamanhos`, `cores`, `estoque`, `destaque`, `ativo`) VALUES
('Camiseta Básica Preta', 'camiseta-basica-preta', 'Camiseta básica 100% algodão, confortável e versátil para o dia a dia.', 79.90, 59.90, 1, 1, 'https://via.placeholder.com/400x500/1a1a1a/ffffff?text=Camiseta+Preta', 'P,M,G,GG,XG', 'Preto,Branco,Cinza', 50, 1, 1),
('Camiseta Streetwear Urban', 'camiseta-streetwear-urban', 'Camiseta oversized com estampa exclusiva estilo urbano.', 129.90, NULL, 1, 3, 'https://via.placeholder.com/400x500/2c2c2c/ffffff?text=Camiseta+Urban', 'M,G,GG', 'Preto,Grafite', 30, 1, 1),
('Camiseta Esportiva Dry-Fit', 'camiseta-esportiva-dry-fit', 'Camiseta tecnológica com secagem rápida para treinos.', 99.90, 79.90, 1, 4, 'https://via.placeholder.com/400x500/27ae60/ffffff?text=Camiseta+Sport', 'P,M,G,GG', 'Verde,Azul,Preto', 40, 0, 1),
('Camisa Social Slim Fit', 'camisa-social-slim-fit', 'Camisa social de algodão egípcio, corte slim fit elegante.', 189.90, 159.90, 2, 2, 'https://via.placeholder.com/400x500/2c3e50/ffffff?text=Camisa+Social', 'P,M,G,GG', 'Branco,Azul Claro,Rosa', 25, 1, 1),
('Camisa Casual Linho', 'camisa-casual-linho', 'Camisa de linho natural, perfeita para o verão.', 159.90, NULL, 2, 1, 'https://via.placeholder.com/400x500/d4c4a8/333333?text=Camisa+Linho', 'M,G,GG', 'Bege,Branco,Azul', 20, 0, 1),
('Camisa Elegante Premium', 'camisa-elegante-premium', 'Camisa de alta costura com acabamento premium.', 249.90, 199.90, 2, 6, 'https://via.placeholder.com/400x500/1a1a2e/f39c12?text=Camisa+Premium', 'P,M,G,GG', 'Preto,Vinho,Azul Marinho', 15, 1, 1),
('Calça Jeans Slim', 'calca-jeans-slim', 'Calça jeans com elastano, corte slim moderno.', 199.90, 169.90, 3, 1, 'https://via.placeholder.com/400x500/3d5a80/ffffff?text=Calca+Jeans', 'P,M,G,GG', 'Azul Escuro,Azul Claro,Preto', 35, 1, 1),
('Calça Social Alfaiataria', 'calca-social-alfaiataria', 'Calça de alfaiataria com caimento perfeito.', 259.90, NULL, 3, 2, 'https://via.placeholder.com/400x500/2c3e50/ffffff?text=Calca+Social', 'P,M,G,GG', 'Preto,Cinza,Marinho', 20, 0, 1),
('Calça Jogger Streetwear', 'calca-jogger-streetwear', 'Calça jogger estilo urbano com bolsos cargo.', 179.90, 149.90, 3, 3, 'https://via.placeholder.com/400x500/1a1a1a/e74c3c?text=Calca+Jogger', 'M,G,GG', 'Preto,Verde Militar,Bege', 28, 1, 1),
('Bermuda Sarja Casual', 'bermuda-sarja-casual', 'Bermuda de sarja confortável para o dia a dia.', 119.90, 99.90, 4, 1, 'https://via.placeholder.com/400x500/c4a35a/ffffff?text=Bermuda+Sarja', 'P,M,G,GG', 'Bege,Azul,Verde', 40, 0, 1),
('Bermuda Jeans Destroyed', 'bermuda-jeans-destroyed', 'Bermuda jeans com detalhes destroyed modernos.', 149.90, NULL, 4, 3, 'https://via.placeholder.com/400x500/5c7aea/ffffff?text=Bermuda+Jeans', 'P,M,G,GG', 'Azul Claro,Azul Escuro', 30, 1, 1),
('Bermuda Esportiva Performance', 'bermuda-esportiva-performance', 'Bermuda esportiva com tecnologia de alta performance.', 129.90, 109.90, 4, 4, 'https://via.placeholder.com/400x500/27ae60/ffffff?text=Bermuda+Sport', 'P,M,G,GG,XG', 'Preto,Cinza,Azul', 45, 0, 1);

-- =============================================
-- Fim do Script
-- =============================================
