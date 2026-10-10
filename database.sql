-- Ejecuta este archivo dentro de la base de datos vanne_enviosgit si deseas crear la tabla manualmente.
-- La aplicación también crea la tabla automáticamente desde config.php.
CREATE TABLE IF NOT EXISTS envios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    destinatario VARCHAR(150) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
