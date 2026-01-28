# KRStore Moda Masculina

Loja virtual de roupas masculinas desenvolvida em PHP com tema escuro moderno.

## 📁 Estrutura do Projeto

```
krstore/
├── .env                    # Variáveis de ambiente (credenciais)
├── .htaccess              # Configurações de segurança Apache
├── index.php              # Página principal da loja
├── database.sql           # Script SQL para criar o banco de dados
│
├── admin/                 # Painel Administrativo
│   ├── index.php         # Dashboard
│   ├── login.php         # Tela de login
│   ├── logout.php        # Logout
│   ├── produtos.php      # Lista de produtos
│   ├── produto-form.php  # Criar/editar produto
│   ├── produto-delete.php# Excluir produto
│   ├── categorias.php    # Gerenciar categorias
│   ├── logs.php          # Logs de acesso
│   └── includes/         # Header e footer do admin
│
├── assets/
│   ├── css/
│   │   ├── style.css     # Estilos da loja
│   │   └── admin.css     # Estilos do painel admin
│   └── js/
│       └── main.js       # JavaScript (carrinho, carrossel)
│
├── classes/              # Classes PHP
│   ├── Produto.php       # Model de produtos
│   ├── Categoria.php     # Model de categorias
│   └── Auth.php          # Autenticação admin
│
├── config/
│   └── database.php      # Conexão com banco de dados
│
├── includes/
│   ├── header.php        # Header da loja
│   ├── footer.php        # Footer da loja
│   ├── product-card.php  # Componente card de produto
│   └── functions.php     # Funções auxiliares
│
└── uploads/
    └── products/         # Imagens dos produtos (se usar upload local)
```

## 🚀 Instalação

### 1. Upload dos Arquivos

Faça upload de todos os arquivos para a pasta `public_html` (ou similar) na sua hospedagem.

### 2. Criar Banco de Dados

1. Acesse o **phpMyAdmin** da sua hospedagem
2. Crie um banco de dados chamado `vendaskr_banco` (ou o nome configurado)
3. Importe o arquivo `database.sql`
4. O script criará todas as tabelas e dados iniciais

### 3. Configurar o .env

Edite o arquivo `.env` com suas credenciais:

```env
DB_HOST=localhost
DB_NAME=vendaskr_banco
DB_USER=vendaskr_user
DB_PASS=SUA_SENHA_AQUI

SITE_NAME=KRStore Moda Masculina
SITE_URL=https://vendaskrstore.shop

WHATSAPP_NUMBER=5585985009840
INSTAGRAM_USER=krstore2026

ADMIN_USER=admin
ADMIN_PASS_HASH=047eda68a1d5ed8835f8f80b0be399f476b0f6e05e820b94944b97b6799c6b6c147eb9edacdcad1fcfe7926ee43ab4731b5d3afa916c8f66b3f799ddfd9a0aaf
```

### 4. Permissões de Pastas

```bash
chmod 755 -R /path/to/krstore
chmod 777 -R /path/to/krstore/uploads
```

## 🔐 Acesso ao Painel Admin

- **URL:** `https://seusite.com/admin/`
- **Usuário:** `admin`
- **Senha:** A senha que gera o hash SHA-512 configurado

## 📱 Funcionalidades

### Loja (Frontend)
- ✅ Tema escuro moderno e responsivo
- ✅ Catálogo de produtos separado por categorias
- ✅ Carrossel de produtos por categoria
- ✅ Cards de produtos com badges de promoção/destaque
- ✅ Seleção de tamanho e cor
- ✅ Carrinho de compras (localStorage)
- ✅ Finalização de compra via WhatsApp
- ✅ Busca de produtos
- ✅ Filtro por estilos

### Painel Admin (Backend)
- ✅ Login seguro com SHA-512
- ✅ Dashboard com estatísticas
- ✅ CRUD completo de produtos
- ✅ Gerenciamento de categorias (Tipos e Estilos)
- ✅ Logs de atividades
- ✅ Proteção CSRF
- ✅ Sessões seguras

## 🎨 Categorias Padrão

### Tipos de Produto
- Camisetas
- Camisas
- Calças
- Bermudas

### Estilos
- Casual
- Social
- Urbano (Streetwear)
- Esportivo
- Tradicional
- Elegante
- Criativo

## 🛡️ Segurança

- Arquivos sensíveis bloqueados via .htaccess
- Proteção contra SQL Injection (prepared statements)
- Proteção CSRF em formulários
- Senhas hasheadas com SHA-512
- Sessões com timeout de 4 horas
- Headers de segurança configurados

## 📞 Contato

- **Instagram:** @krstore2026
- **WhatsApp:** (85) 98500-9840
- **Site:** vendaskrstore.shop

---

Desenvolvido com ❤️ para KRStore Moda Masculina
