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
    !is_string($input['nombre'] ?? null) ||
    !is_string($input['email'] ?? null) ||
    !is_string($input['password'] ?? null)
) {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Envíe nombre, correo y contraseña en JSON.']);
    exit;
}

$nombre = trim($input['nombre']);
$email = trim($input['email']);
$pass = $input['password'];

if ($nombre === '' || $email === '' || $pass === '') {
    http_response_code(400);
    echo json_encode(['mensaje' => 'Todos los campos son obligatorios.']);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';
$hash = password_hash($pass, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)'
    );
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => $hash]);
    http_response_code(201);
    echo json_encode(['mensaje' => 'Usuario registrado. Ya puede iniciar sesión.']);
} catch (PDOException $e) {
    http_response_code($e->getCode() === '23000' ? 409 : 500);
    echo json_encode(['mensaje' => $e->getCode() === '23000'
        ? 'El correo ya está registrado.' : 'Error al registrar el usuario.']);
}
