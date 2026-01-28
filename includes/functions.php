<?php
/**
 * Funções Helper - KRStore
 */

/**
 * Formatar preço em Real
 */
function formatPrice($price) {
    return 'R$ ' . number_format($price, 2, ',', '.');
}

/**
 * Formatar data em português
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Sanitizar string
 */
function sanitize($string) {
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Gerar cor aleatória baseada em string
 */
function stringToColor($str) {
    $hash = md5($str);
    return '#' . substr($hash, 0, 6);
}

/**
 * Calcular desconto em porcentagem
 */
function calcDiscount($original, $promotional) {
    if (!$promotional || $promotional >= $original) {
        return 0;
    }
    return round((($original - $promotional) / $original) * 100);
}

/**
 * Verificar se é requisição AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Resposta JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Redirect com mensagem
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    header("Location: $url");
    exit;
}

/**
 * Obter mensagem flash
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Validar upload de imagem
 */
function validateImageUpload($file, $maxSize = 5242880) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Erro no upload do arquivo'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['error' => 'Arquivo muito grande (máx: 5MB)'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Tipo de arquivo não permitido'];
    }
    
    return ['success' => true, 'mime' => $mimeType];
}

/**
 * Upload de imagem
 */
function uploadImage($file, $directory = 'uploads/products') {
    $validation = validateImageUpload($file);
    
    if (isset($validation['error'])) {
        return $validation;
    }
    
    $uploadDir = __DIR__ . '/../' . $directory;
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $directory . '/' . $filename
        ];
    }
    
    return ['error' => 'Falha ao salvar arquivo'];
}

/**
 * Gerar paginação
 */
function paginate($total, $perPage, $currentPage, $baseUrl) {
    $totalPages = ceil($total / $perPage);
    $pages = [];
    
    for ($i = 1; $i <= $totalPages; $i++) {
        $pages[] = [
            'number' => $i,
            'url' => $baseUrl . '?page=' . $i,
            'active' => $i === $currentPage
        ];
    }
    
    return [
        'pages' => $pages,
        'total' => $totalPages,
        'current' => $currentPage,
        'hasNext' => $currentPage < $totalPages,
        'hasPrev' => $currentPage > 1,
        'nextUrl' => $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : null,
        'prevUrl' => $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : null
    ];
}
