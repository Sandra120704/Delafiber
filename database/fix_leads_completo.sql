-- =====================================================
-- FIX COMPLETO DEL MÓDULO DE LEADS
-- Fecha: 2025-10-12
-- Propósito: Arreglar TODOS los problemas del módulo
-- =====================================================

USE `delafiber`;

-- =====================================================
-- 1. ACTUALIZAR PERMISOS (CRÍTICO)
-- =====================================================

UPDATE `roles` 
SET `permisos` = '["*"]'
WHERE `idrol` = 1;

UPDATE `roles` 
SET `permisos` = '["leads.*", "seguimientos.*", "tareas.*", "cotizaciones.*", "reportes.*", "zonas.*"]'
WHERE `idrol` = 2;

UPDATE `roles` 
SET `permisos` = '["leads.*", "seguimientos.*", "tareas.*", "cotizaciones.*"]'
WHERE `idrol` = 3;

-- =====================================================
-- 2. NORMALIZAR ESTADOS DE LEADS
-- =====================================================

-- Actualizar estados NULL o vacíos a 'activo'
UPDATE `leads` 
SET `estado` = 'activo' 
WHERE `estado` IS NULL OR `estado` = '' OR `estado` = 'Activo';

-- Normalizar estados convertidos
UPDATE `leads` 
SET `estado` = 'convertido' 
WHERE `estado` = 'Convertido';

-- Normalizar estados descartados
UPDATE `leads` 
SET `estado` = 'descartado' 
WHERE `estado` = 'Descartado' OR `estado` = 'Descartada';

-- =====================================================
-- 3. ASEGURAR INTEGRIDAD DE DATOS
-- =====================================================

-- Asignar etapa por defecto si es NULL
UPDATE `leads` 
SET `idetapa` = 1 
WHERE `idetapa` IS NULL;

-- Asegurar que todos los leads tengan usuario asignado
UPDATE `leads` l
LEFT JOIN `usuarios` u ON l.idusuario = u.idusuario
SET l.idusuario = l.idusuario_registro
WHERE l.idusuario IS NULL AND l.idusuario_registro IS NOT NULL;

-- =====================================================
-- 4. LIMPIAR SESIONES (FORZAR RECARGA)
-- =====================================================

-- Nota: CodeIgniter usa sesiones en archivos, no en BD
-- Eliminar manualmente los archivos en: writable/session/*
-- O ejecutar desde terminal: del /Q writable\session\*

-- =====================================================
-- 5. VERIFICACIÓN
-- =====================================================

-- Ver distribución de estados
SELECT 
    estado,
    COUNT(*) as total,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM leads), 2) as porcentaje
FROM leads
GROUP BY estado
ORDER BY total DESC;

-- Ver leads por etapa
SELECT 
    e.nombre as etapa,
    COUNT(l.idlead) as total_leads
FROM etapas e
LEFT JOIN leads l ON e.idetapa = l.idetapa AND l.estado = 'activo'
GROUP BY e.idetapa, e.nombre
ORDER BY e.orden;

-- Ver permisos actualizados
SELECT 
    idrol,
    nombre,
    nivel,
    permisos
FROM roles
ORDER BY nivel;

-- =====================================================
-- RESUMEN
-- =====================================================
SELECT '✅ FIX COMPLETADO' as mensaje;
SELECT 'Ahora TODOS los usuarios deben:' as instruccion;
SELECT '1. Cerrar sesión' as paso1;
SELECT '2. Volver a iniciar sesión' as paso2;
SELECT '3. Probar crear lead, seguimiento y tareas' as paso3;
