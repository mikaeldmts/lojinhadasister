<?php
/**
 * Classe de Produtos - KRStore
 * Compatível com PHP sem mysqlnd (não usa get_result nem fetch_all)
 */

require_once __DIR__ . '/../config/database.php';

class Produto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Buscar todos os produtos ativos
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1
                ORDER BY p.destaque DESC, p.criado_em DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar todos os produtos (incluindo inativos) - Para Admin
     */
    public function getAllAdmin($limit = null, $offset = 0) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                ORDER BY p.criado_em DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar produtos por categoria de tipo
     */
    public function getByTipo($tipoId, $limit = null) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1 AND p.categoria_tipo_id = " . (int)$tipoId . "
                ORDER BY p.destaque DESC, p.criado_em DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar produtos por categoria de estilo
     */
    public function getByEstilo($estiloId, $limit = null) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1 AND p.categoria_estilo_id = " . (int)$estiloId . "
                ORDER BY p.destaque DESC, p.criado_em DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar produto por ID
     */
    public function getById($id) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.id = " . (int)$id;
        
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Buscar produto por slug
     */
    public function getBySlug($slug) {
        $slug = $this->db->real_escape_string($slug);
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.slug = '$slug' AND p.ativo = 1";
        
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Buscar produtos em destaque
     */
    public function getDestaques($limit = 8) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1 AND p.destaque = 1
                ORDER BY p.criado_em DESC
                LIMIT " . (int)$limit;
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar produtos com promoção
     */
    public function getPromocoes($limit = 8) {
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1 AND p.preco_promocional IS NOT NULL
                ORDER BY p.criado_em DESC
                LIMIT " . (int)$limit;
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Pesquisar produtos
     */
    public function search($termo, $limit = 20) {
        $termo = $this->db->real_escape_string($termo);
        $sql = "SELECT p.id, p.nome, p.slug, p.descricao, p.preco, p.preco_promocional,
                p.categoria_tipo_id, p.categoria_estilo_id, p.imagem_principal, p.imagens_adicionais,
                p.tamanhos, p.cores, p.variacoes, p.estoque, p.destaque, p.ativo, p.visualizacoes,
                p.criado_em, p.atualizado_em,
                ct.nome as tipo_nome, ct.slug as tipo_slug, 
                ce.nome as estilo_nome, ce.slug as estilo_slug
                FROM produtos p
                LEFT JOIN categorias_tipo ct ON p.categoria_tipo_id = ct.id
                LEFT JOIN categorias_estilo ce ON p.categoria_estilo_id = ce.id
                WHERE p.ativo = 1 AND (p.nome LIKE '%$termo%' OR p.descricao LIKE '%$termo%')
                ORDER BY p.destaque DESC, p.criado_em DESC
                LIMIT " . (int)$limit;
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Incrementar visualizações
     */
    public function incrementViews($id) {
        $sql = "UPDATE produtos SET visualizacoes = visualizacoes + 1 WHERE id = " . (int)$id;
        return $this->db->query($sql);
    }
    
    /**
     * Criar novo produto
     */
    public function create($data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $preco = (float)$data['preco'];
        $preco_promo = !empty($data['preco_promocional']) ? (float)$data['preco_promocional'] : null;
        $tipo_id = (int)$data['categoria_tipo_id'];
        $estilo_id = !empty($data['categoria_estilo_id']) ? (int)$data['categoria_estilo_id'] : null;
        $imagem = $this->db->real_escape_string($data['imagem_principal']);
        $imagens_adicionais = !empty($data['imagens_adicionais']) ? $this->db->real_escape_string($data['imagens_adicionais']) : null;
        $tamanhos = $this->db->real_escape_string($data['tamanhos'] ?? '');
        $cores = $this->db->real_escape_string($data['cores'] ?? '');
        $variacoes = !empty($data['variacoes']) ? $this->db->real_escape_string($data['variacoes']) : null;
        $estoque = (int)($data['estoque'] ?? 0);
        $destaque = (int)($data['destaque'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $preco_promo_sql = $preco_promo === null ? 'NULL' : $preco_promo;
        $estilo_id_sql = $estilo_id === null ? 'NULL' : $estilo_id;
        $variacoes_sql = $variacoes === null ? 'NULL' : "'$variacoes'";
        $imagens_adicionais_sql = $imagens_adicionais === null ? 'NULL' : "'$imagens_adicionais'";
        
        $sql = "INSERT INTO produtos (nome, slug, descricao, preco, preco_promocional, categoria_tipo_id, 
                categoria_estilo_id, imagem_principal, imagens_adicionais, tamanhos, cores, variacoes, estoque, destaque, ativo)
                VALUES ('$nome', '$slug', '$descricao', $preco, $preco_promo_sql, $tipo_id, 
                $estilo_id_sql, '$imagem', $imagens_adicionais_sql, '$tamanhos', '$cores', $variacoes_sql, $estoque, $destaque, $ativo)";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Atualizar produto
     */
    public function update($id, $data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $preco = (float)$data['preco'];
        $preco_promo = !empty($data['preco_promocional']) ? (float)$data['preco_promocional'] : null;
        $tipo_id = (int)$data['categoria_tipo_id'];
        $estilo_id = !empty($data['categoria_estilo_id']) ? (int)$data['categoria_estilo_id'] : null;
        $imagem = $this->db->real_escape_string($data['imagem_principal']);
        $imagens_adicionais = !empty($data['imagens_adicionais']) ? $this->db->real_escape_string($data['imagens_adicionais']) : null;
        $tamanhos = $this->db->real_escape_string($data['tamanhos'] ?? '');
        $cores = $this->db->real_escape_string($data['cores'] ?? '');
        $variacoes = !empty($data['variacoes']) ? $this->db->real_escape_string($data['variacoes']) : null;
        $estoque = (int)($data['estoque'] ?? 0);
        $destaque = (int)($data['destaque'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $preco_promo_sql = $preco_promo === null ? 'NULL' : $preco_promo;
        $estilo_id_sql = $estilo_id === null ? 'NULL' : $estilo_id;
        $variacoes_sql = $variacoes === null ? 'NULL' : "'$variacoes'";
        $imagens_adicionais_sql = $imagens_adicionais === null ? 'NULL' : "'$imagens_adicionais'";
        
        $sql = "UPDATE produtos SET 
                nome = '$nome',
                slug = '$slug',
                descricao = '$descricao',
                preco = $preco,
                preco_promocional = $preco_promo_sql,
                categoria_tipo_id = $tipo_id,
                categoria_estilo_id = $estilo_id_sql,
                imagem_principal = '$imagem',
                imagens_adicionais = $imagens_adicionais_sql,
                tamanhos = '$tamanhos',
                cores = '$cores',
                variacoes = $variacoes_sql,
                estoque = $estoque,
                destaque = $destaque,
                ativo = $ativo
                WHERE id = " . (int)$id;
        
        return $this->db->query($sql);
    }
    
    /**
     * Deletar produto
     */
    public function delete($id) {
        $sql = "DELETE FROM produtos WHERE id = " . (int)$id;
        return $this->db->query($sql);
    }
    
    /**
     * Contar produtos
     */
    public function count($apenasAtivos = false) {
        $sql = "SELECT COUNT(*) as total FROM produtos";
        if ($apenasAtivos) {
            $sql .= " WHERE ativo = 1";
        }
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }
    
    /**
     * Gerar slug único
     */
    public function generateSlug($nome, $excludeId = null) {
        $slug = $this->slugify($nome);
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Converter texto em slug
     */
    private function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text);
    }
    
    /**
     * Verificar se slug já existe
     */
    private function slugExists($slug, $excludeId = null) {
        $slug = $this->db->real_escape_string($slug);
        $sql = "SELECT id FROM produtos WHERE slug = '$slug'";
        if ($excludeId) {
            $sql .= " AND id != " . (int)$excludeId;
        }
        $result = $this->db->query($sql);
        return $result && $result->num_rows > 0;
    }
    
    /**
     * Helper para converter mysqli_result em array associativo
     */
    private function fetchAllAssoc($result) {
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}
