<?php
/**
 * Classe de Autenticação - KRStore Admin
 * Compatível com PHP sem mysqlnd (não usa get_result nem fetch_all)
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    
    /**
     * Verificar login
     */
    public static function login($username, $password) {
        // Verificar credenciais do admin
        if ($username === ADMIN_USER && hash('sha512', $password) === ADMIN_PASS_HASH) {
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            // Regenerar ID da sessão
            session_regenerate_id(true);
            
            // Registrar log
            self::logAction('login', 'Login realizado com sucesso');
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Verificar se está logado
     */
    public static function isLogged() {
        if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
            return false;
        }
        
        // Verificar timeout (4 horas)
        if (time() - $_SESSION['admin_login_time'] > 14400) {
            self::logout();
            return false;
        }
        
        // Atualizar tempo da sessão
        $_SESSION['admin_login_time'] = time();
        
        return true;
    }
    
    /**
     * Exigir autenticação
     */
    public static function requireAuth() {
        if (!self::isLogged()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Fazer logout
     */
    public static function logout() {
        self::logAction('logout', 'Logout realizado');
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Gerar token CSRF
     */
    public static function getCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validar token CSRF
     */
    public static function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Registrar log de ação
     */
    public static function logAction($action, $description = '') {
        $db = Database::getInstance();
        
        $action = $db->real_escape_string($action);
        $description = $db->real_escape_string($description);
        $ip = $db->real_escape_string($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        $sql = "INSERT INTO logs (acao, descricao, ip) VALUES ('$action', '$description', '$ip')";
        $db->query($sql);
    }
    
    /**
     * Buscar logs
     */
    public static function getLogs($limit = 50) {
        $db = Database::getInstance();
        
        $sql = "SELECT * FROM logs ORDER BY criado_em DESC LIMIT " . (int)$limit;
        $result = $db->query($sql);
        
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}
