-- Script SQL para crear la tabla de formularios
-- Ejecutar este script en tu base de datos MySQL

CREATE DATABASE IF NOT EXISTS formularios_db;
USE formularios_db;

CREATE TABLE IF NOT EXISTS formularios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    mes VARCHAR(20) NOT NULL,
    cantidad INT NOT NULL,
    fecha_hora DATETIME NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para mejorar el rendimiento
CREATE INDEX idx_fecha_hora ON formularios(fecha_hora);
CREATE INDEX idx_email ON formularios(email);

-- Ejemplo de consulta para ver los datos
-- SELECT * FROM formularios ORDER BY fecha_hora DESC;
