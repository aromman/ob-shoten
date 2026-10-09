<?php

/*
 * Alta de interesados desde la página "Próximamente".
 *
 * Responde JSON cuando la llamada viene por fetch; si el navegador no tiene
 * JavaScript, redirige de vuelta a la página con ?estado=ok|error.
 */

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

$wantsJson = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

function responder($ok, $mensaje, $status, $wantsJson)
{
    if ($wantsJson) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ok' => $ok, 'mensaje' => $mensaje));
    } else {
        header('Location: ./?estado=' . ($ok ? 'ok' : 'error'), true, 303);
    }
    exit;
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    responder(false, 'Método no permitido.', 405, $wantsJson);
}

// Honeypot: los bots completan este campo oculto. Les respondemos "ok" sin guardar.
if (!empty($_POST['website'])) {
    responder(true, '¡Listo! Te vamos a avisar.', 200, $wantsJson);
}

$email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
$email = strtolower($email);
if ($email === '' || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(false, 'Revisá el email, parece que no es válido.', 422, $wantsJson);
}

$origenesPermitidos = array('proximamente', 'ob');
$origen = isset($_POST['origen']) ? (string) $_POST['origen'] : 'proximamente';
if (!in_array($origen, $origenesPermitidos, true)) {
    $origen = 'proximamente';
}

$configPath = getenv('SHOTEN_CONFIG');
if ($configPath === false || $configPath === '') {
    $configPath = dirname(__DIR__) . '/shoten-config/proximamente.php';
}
if (!is_file($configPath)) {
    error_log('[proximamente] No se encontró la configuración en ' . $configPath);
    responder(false, 'No pudimos registrarte. Probá de nuevo más tarde.', 500, $wantsJson);
}
$config = require $configPath;

$ip = isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : null;
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : null;

try {
    $db = $config['db'];
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=utf8mb4',
        $db['user'],
        $db['pass'],
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false)
    );

    $maxPorHora = isset($config['max_per_ip_hour']) ? (int) $config['max_per_ip_hour'] : 5;
    if ($ip !== null && $maxPorHora > 0) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM suscriptores WHERE ip = ? AND creado_en > (NOW() - INTERVAL 1 HOUR)');
        $stmt->execute(array($ip));
        if ((int) $stmt->fetchColumn() >= $maxPorHora) {
            responder(false, 'Recibimos muchos registros desde tu conexión. Probá más tarde.', 429, $wantsJson);
        }
    }

    // Si el email ya existe no se duplica ni se avisa al usuario (no revelamos quién está anotado).
    $stmt = $pdo->prepare('INSERT IGNORE INTO suscriptores (email, origen, ip, user_agent) VALUES (?, ?, ?, ?)');
    $stmt->execute(array($email, $origen, $ip, $userAgent));
    $esNuevo = $stmt->rowCount() > 0;
} catch (PDOException $e) {
    error_log('[proximamente] Error de base de datos: ' . $e->getMessage());
    responder(false, 'No pudimos registrarte. Probá de nuevo más tarde.', 500, $wantsJson);
}

if ($esNuevo && !empty($config['notify_email'])) {
    $from = !empty($config['notify_from']) ? $config['notify_from'] : 'no-responder@shoten.com.ar';
    @mail(
        $config['notify_email'],
        'Shoten: nuevo interesado',
        "Se anotó un nuevo interesado en la lista de Shoten.\n\nEmail: {$email}\nOrigen: {$origen}\nFecha: " . date('Y-m-d H:i:s') . "\n",
        'From: ' . $from . "\r\nContent-Type: text/plain; charset=utf-8"
    );
}

responder(true, '¡Listo! Te vamos a avisar cuando lancemos.', 200, $wantsJson);
