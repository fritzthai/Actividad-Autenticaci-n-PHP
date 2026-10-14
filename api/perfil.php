<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['mensaje' => 'Acceso no autorizado. Inicie sesión.']);
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

try {
    $stmt = $pdo->prepare('SELECT id, nombre, email FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        unset($_SESSION['usuario_id']);
        http_response_code(401);
        echo json_encode(['mensaje' => 'Acceso no autorizado.']);
        exit;
    }

    echo json_encode(['usuario' => $usuario], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['mensaje' => 'Error al consultar el perfil.']);
}
