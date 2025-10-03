-- ========================================
-- Active: 1757456393932@@127.0.0.1@3306@delafiberlafiberlafiberbliotecalafiberlafiberlafiberlafiberlafiber

-- Eliminar base de datos completa
DROP DATABASE IF EXISTS delafiber;

-- Crear base de datos limpia
CREATE DATABASE delafiber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE delafiber;

SELECT 'Base de datos eliminada y recreada exitosamente' as Resultado;
SELECT 'Ahora ejecutar: 2_crear_tablas.sql' as 'Siguiente Paso';
