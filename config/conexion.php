<?php
$host = 'localhost';
$db = 'clase_backend';
$user = 'root';
$pass = ''; // Contraseña de MySQL: ajustar si corresponde.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['mensaje' => 'Error de conexión con la base de datos.']);
    exit;
}
