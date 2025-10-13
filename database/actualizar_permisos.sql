-- =====================================================
-- ACTUALIZAR PERMISOS DE ROLES
-- Fecha: 2025-10-12
-- Propósito: Permitir que todos los usuarios puedan:
--   - Ver el pipeline completo
--   - Registrar leads
--   - Agregar seguimientos
--   - Crear tareas
--   - Generar cotizaciones
-- =====================================================

USE `delafiber`;

-- Actualizar permisos del Supervisor
UPDATE `roles` 
SET `permisos` = '["leads.*", "seguimientos.*", "tareas.*", "cotizaciones.*", "reportes.*", "zonas.*"]'
WHERE `idrol` = 2;

-- Actualizar permisos del Vendedor
UPDATE `roles` 
SET `permisos` = '["leads.*", "seguimientos.*", "tareas.*", "cotizaciones.*"]'
WHERE `idrol` = 3;

-- Verificar cambios
SELECT 
    idrol,
    nombre,
    nivel,
    permisos
FROM roles
ORDER BY nivel;
