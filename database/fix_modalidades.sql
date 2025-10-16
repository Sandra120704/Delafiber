-- Active: 1743133057434@@127.0.0.1@3306@delafiber
-- ============================================
-- FIX: Insertar Modalidades de Comunicación
-- ============================================
-- Este script soluciona el error al guardar seguimientos
-- cuando la tabla modalidades está vacía

USE delafiber;

-- Verificar si hay modalidades
SELECT COUNT(*) as total_modalidades FROM modalidades;

-- Si el resultado es 0, ejecutar los siguientes INSERT:

-- Limpiar tabla (opcional, solo si hay datos incorrectos)
-- TRUNCATE TABLE modalidades;

-- Insertar modalidades básicas
INSERT INTO modalidades (idmodalidad, nombre, icono, estado) VALUES
(1, 'Llamada Telefónica', 'phone', 'activo'),
(2, 'WhatsApp', 'whatsapp', 'activo'),
(3, 'Email', 'email', 'activo'),
(4, 'Reunión Presencial', 'users', 'activo'),
(5, 'Videoconferencia', 'video', 'activo'),
(6, 'Sistema', 'settings', 'activo'),
(7, 'Visita Técnica', 'tool', 'activo'),
(8, 'SMS', 'message-square', 'activo')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Verificar que se insertaron correctamente
SELECT * FROM modalidades ORDER BY idmodalidad;

-- Resultado esperado:
-- +-------------+----------------------+----------------+--------+
-- | idmodalidad | nombre               | icono          | estado |
-- +-------------+----------------------+----------------+--------+
-- |           1 | Llamada Telefónica   | phone          | activo |
-- |           2 | WhatsApp             | whatsapp       | activo |
-- |           3 | Email                | email          | activo |
-- |           4 | Reunión Presencial   | users          | activo |
-- |           5 | Videoconferencia     | video          | activo |
-- |           6 | SMS                  | message-square | activo |
-- |           7 | Visita Técnica       | tool           | activo |
-- |           8 | Mensaje de Texto     | message-circle | activo |
-- +-------------+----------------------+----------------+--------+
SHOW TABLES LIKE 'seguimientos';