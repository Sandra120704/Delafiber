-- =====================================================
-- SCRIPT DE CORRECCIÓN: Normalizar estados de leads
-- Fecha: 2025-10-13
-- Descripción: Normaliza todos los estados a minúsculas
-- =====================================================

-- 1. Ver estados actuales
SELECT DISTINCT estado, COUNT(*) as total
FROM leads
GROUP BY estado;

-- 2. Normalizar estados a minúsculas
UPDATE leads 
SET estado = LOWER(estado)
WHERE estado IS NOT NULL;

-- 3. Corregir estados NULL a 'activo'
UPDATE leads 
SET estado = 'activo'
WHERE estado IS NULL OR estado = '';

-- 4. Verificar resultados
SELECT DISTINCT estado, COUNT(*) as total
FROM leads
GROUP BY estado;

-- 5. Agregar índice para mejorar rendimiento (opcional)
ALTER TABLE leads ADD INDEX idx_estado (estado);

-- =====================================================
-- NOTA: Ejecuta este script en phpMyAdmin
-- =====================================================
