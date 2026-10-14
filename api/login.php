<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['mensaje' => 'Método no permitido.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (
    !is_array($input) ||
    !is_string($input['email'] ?? null) ||
    !is_string($input['password'] ?? null)
) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Envíe correo y contraseña en JSON.']);
    exit;
}

$email = trim($input['email']);
$pass = $input['password'];

if ($email === '' || $pass === '') {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Ingrese correo y contraseña.']);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $stmt = $pdo->prepare('SELECT id, password FROM usuarios WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($pass, $usuario['password'])) {
        http_response_code(401);
        echo json_encode(['mensaje' => 'Credenciales incorrectas.']);
        exit;
    }

    session_start();
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];

    echo json_encode(['mensaje' => 'Inicio de sesión exitoso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['mensaje' => 'Error al iniciar sesión.']);
}
