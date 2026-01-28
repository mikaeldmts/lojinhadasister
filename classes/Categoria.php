<?php
/**
 * Classe de Categorias - KRStore
 * Compatível com PHP sem mysqlnd (não usa get_result nem fetch_all)
 */

require_once __DIR__ . '/../config/database.php';

class Categoria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
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
    
    // ==========================================
    // CATEGORIAS DE TIPO
    // ==========================================
    
    /**
     * Buscar todas as categorias de tipo
     */
    public function getAllTipos($apenasAtivos = true) {
        $sql = "SELECT * FROM categorias_tipo";
        if ($apenasAtivos) {
            $sql .= " WHERE ativo = 1";
        }
        $sql .= " ORDER BY ordem ASC, nome ASC";
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar categoria de tipo por ID
     */
    public function getTipoById($id) {
        $sql = "SELECT * FROM categorias_tipo WHERE id = " . (int)$id;
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Buscar categoria de tipo por slug
     */
    public function getTipoBySlug($slug) {
        $slug = $this->db->real_escape_string($slug);
        $sql = "SELECT * FROM categorias_tipo WHERE slug = '$slug'";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Criar categoria de tipo
     */
    public function createTipo($data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $ordem = (int)($data['ordem'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $sql = "INSERT INTO categorias_tipo (nome, slug, descricao, ordem, ativo) 
                VALUES ('$nome', '$slug', '$descricao', $ordem, $ativo)";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Atualizar categoria de tipo
     */
    public function updateTipo($id, $data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $ordem = (int)($data['ordem'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $sql = "UPDATE categorias_tipo SET 
                nome = '$nome',
                slug = '$slug',
                descricao = '$descricao',
                ordem = $ordem,
                ativo = $ativo
                WHERE id = " . (int)$id;
        
        return $this->db->query($sql);
    }
    
    /**
     * Deletar categoria de tipo
     */
    public function deleteTipo($id) {
        $sql = "DELETE FROM categorias_tipo WHERE id = " . (int)$id;
        return $this->db->query($sql);
    }
    
    // ==========================================
    // CATEGORIAS DE ESTILO
    // ==========================================
    
    /**
     * Buscar todas as categorias de estilo
     */
    public function getAllEstilos($apenasAtivos = true) {
        $sql = "SELECT * FROM categorias_estilo";
        if ($apenasAtivos) {
            $sql .= " WHERE ativo = 1";
        }
        $sql .= " ORDER BY ordem ASC, nome ASC";
        
        $result = $this->db->query($sql);
        return $this->fetchAllAssoc($result);
    }
    
    /**
     * Buscar categoria de estilo por ID
     */
    public function getEstiloById($id) {
        $sql = "SELECT * FROM categorias_estilo WHERE id = " . (int)$id;
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Buscar categoria de estilo por slug
     */
    public function getEstiloBySlug($slug) {
        $slug = $this->db->real_escape_string($slug);
        $sql = "SELECT * FROM categorias_estilo WHERE slug = '$slug'";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Criar categoria de estilo
     */
    public function createEstilo($data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $cor = $this->db->real_escape_string($data['cor'] ?? '#ffffff');
        $ordem = (int)($data['ordem'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $sql = "INSERT INTO categorias_estilo (nome, slug, descricao, cor, ordem, ativo) 
                VALUES ('$nome', '$slug', '$descricao', '$cor', $ordem, $ativo)";
        
        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    /**
     * Atualizar categoria de estilo
     */
    public function updateEstilo($id, $data) {
        $nome = $this->db->real_escape_string($data['nome']);
        $slug = $this->db->real_escape_string($data['slug']);
        $descricao = $this->db->real_escape_string($data['descricao'] ?? '');
        $cor = $this->db->real_escape_string($data['cor'] ?? '#ffffff');
        $ordem = (int)($data['ordem'] ?? 0);
        $ativo = (int)($data['ativo'] ?? 1);
        
        $sql = "UPDATE categorias_estilo SET 
                nome = '$nome',
                slug = '$slug',
                descricao = '$descricao',
                cor = '$cor',
                ordem = $ordem,
                ativo = $ativo
                WHERE id = " . (int)$id;
        
        return $this->db->query($sql);
    }
    
    /**
     * Deletar categoria de estilo
     */
    public function deleteEstilo($id) {
        $sql = "DELETE FROM categorias_estilo WHERE id = " . (int)$id;
        return $this->db->query($sql);
    }
    
    /**
     * Contar produtos por categoria de tipo
     */
    public function countProdutosByTipo($tipoId) {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE categoria_tipo_id = " . (int)$tipoId . " AND ativo = 1";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }
    
    /**
     * Contar produtos por categoria de estilo
     */
    public function countProdutosByEstilo($estiloId) {
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE categoria_estilo_id = " . (int)$estiloId . " AND ativo = 1";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }
    
    /**
     * Gerar slug
     */
    public function generateSlug($nome) {
        $slug = preg_replace('~[^\pL\d]+~u', '-', $nome);
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        return strtolower($slug);
    }
}
