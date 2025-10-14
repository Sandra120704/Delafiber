-- =====================================================
-- MIGRACIÓN: Agregar campo idusuario_registro a tabla leads
-- Fecha: 2025-10-12
-- Descripción: Separar usuario que registra vs usuario asignado
-- =====================================================

USE `delafiber`;

-- Paso 1: Agregar el nuevo campo
ALTER TABLE `leads` 
ADD COLUMN `idusuario_registro` int(11) DEFAULT NULL COMMENT 'Usuario que REGISTRÓ el lead (no cambia)' 
AFTER `idusuario`;

-- Paso 2: Agregar índice
ALTER TABLE `leads` 
ADD KEY `fk_lead_usuario_registro` (`idusuario_registro`);

-- Paso 3: Agregar foreign key
ALTER TABLE `leads` 
ADD CONSTRAINT `fk_lead_usuario_registro` 
FOREIGN KEY (`idusuario_registro`) 
REFERENCES `usuarios` (`idusuario`) 
ON DELETE SET NULL;

-- Paso 4: Migrar datos existentes (copiar idusuario a idusuario_registro)
UPDATE `leads` 
SET `idusuario_registro` = `idusuario` 
WHERE `idusuario_registro` IS NULL;

-- Paso 5: Actualizar comentarios de campos para claridad
ALTER TABLE `leads` 
MODIFY COLUMN `idusuario` int(11) DEFAULT NULL COMMENT 'Usuario ASIGNADO para seguimiento (puede cambiar)';

-- Verificación
SELECT 
    'Migración completada' as status,
    COUNT(*) as total_leads,
    COUNT(idusuario) as con_asignado,
    COUNT(idusuario_registro) as con_registro
FROM `leads`;

SELECT 'Campo idusuario_registro agregado exitosamente' as mensaje;
