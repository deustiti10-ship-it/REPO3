<?php
declare(strict_types=1);

/*
 * Configuración de conexión a MySQL.
 * En producción, evita publicar estas credenciales en repositorios públicos.
 */
$DB_HOST = 'mysql-vanne.alwaysdata.net';
$DB_NAME = 'vanne_enviosgit';
$DB_USER = 'vanne';
$DB_PASS = 'clase1234';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Crear la tabla automáticamente si no existe.
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS envios (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            destinatario VARCHAR(150) NOT NULL,
            direccion VARCHAR(255) NOT NULL,
            descripcion TEXT NOT NULL,
            creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    http_response_code(500);
    exit('No fue posible conectar con la base de datos. Verifica las credenciales y que la base de datos exista en AlwaysData.');
}
