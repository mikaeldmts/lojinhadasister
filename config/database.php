<?php
/**
 * Configuração do Banco de Dados - KRStore
 */

// Carregar variáveis de ambiente
function loadEnv($path) {
    if (!file_exists($path)) {
        die('Arquivo .env não encontrado!');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

// Carregar .env
loadEnv(__DIR__ . '/../.env');

// Configurações do banco
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'vendaskr_banco');
define('DB_USER', $_ENV['DB_USER'] ?? 'vendaskr_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Configurações do site
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'KRStore Moda Masculina');
define('SITE_URL', $_ENV['SITE_URL'] ?? 'https://vendaskrstore.shop');
define('WHATSAPP_NUMBER', $_ENV['WHATSAPP_NUMBER'] ?? '5585985009840');
define('INSTAGRAM_USER', $_ENV['INSTAGRAM_USER'] ?? 'krstore2026');

// Credenciais Admin
define('ADMIN_USER', $_ENV['ADMIN_USER'] ?? 'admin');
define('ADMIN_PASS_HASH', $_ENV['ADMIN_PASS_HASH'] ?? '');

// Chave de sessão
define('SESSION_SECRET', $_ENV['SESSION_SECRET'] ?? 'default_secret');

// Conexão com o banco de dados
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Falha na conexão: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            die("Erro ao conectar ao banco de dados.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }
    
    public static function getDB() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    public function error() {
        return $this->conn->error;
    }
    
    // Prevenir clonagem
    private function __clone() {}
    
    // Prevenir deserialização
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Função helper para obter conexão
function getDB() {
    return Database::getInstance()->getConnection();
}
