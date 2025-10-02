-- ========================================
-- SCRIPT DE OPTIMIZACIÓN DE BASE DE DATOS
-- Delafiber CRM - Versión 1.1
-- Fecha: 2025-10-01
-- ========================================

USE delafiber;

-- ========================================
-- 1. ELIMINAR TABLA OPORTUNIDADES (Duplica funcionalidad de leads)
-- ========================================

SELECT 'Verificando datos en oportunidades...' as Paso;
SELECT COUNT(*) as total_oportunidades FROM oportunidades;

-- Si no hay datos importantes, eliminar la tabla
-- DESCOMENTAR LA SIGUIENTE LÍNEA SOLO SI ESTÁS SEGURO:
-- DROP TABLE IF EXISTS oportunidades;

SELECT 'Tabla oportunidades marcada para eliminación (comentada por seguridad)' as Resultado;

-- ========================================
-- 2. ELIMINAR CAMPO CORREO DUPLICADO DE USUARIOS
-- ========================================

SELECT 'Eliminando campo correo de usuarios (ya existe en personas)...' as Paso;

-- Verificar si hay correos en usuarios que no estén en personas
SELECT 
    u.idusuario,
    u.correo as correo_usuario,
    p.correo as correo_persona
FROM usuarios u
LEFT JOIN personas p ON u.idpersona = p.idpersona
WHERE u.correo IS NOT NULL 
  AND (p.correo IS NULL OR p.correo != u.correo)
LIMIT 10;

-- Migrar correos de usuarios a personas si es necesario
UPDATE personas p
INNER JOIN usuarios u ON p.idpersona = u.idpersona
SET p.correo = u.correo
WHERE u.correo IS NOT NULL 
  AND (p.correo IS NULL OR p.correo = '');

-- Eliminar columna correo de usuarios
ALTER TABLE usuarios DROP COLUMN IF EXISTS correo;

SELECT 'Campo correo eliminado de usuarios' as Resultado;

-- ========================================
-- 3. HACER DNI NOT NULL Y AGREGAR VALIDACIONES
-- ========================================

SELECT 'Configurando DNI como campo obligatorio...' as Paso;

-- Primero, asignar DNI temporal a registros sin DNI
UPDATE personas 
SET dni = CONCAT('TEMP', LPAD(idpersona, 8, '0'))
WHERE dni IS NULL OR dni = '';

-- Ahora hacer DNI NOT NULL
ALTER TABLE personas 
MODIFY COLUMN dni VARCHAR(8) NOT NULL;

-- Agregar índice único (si no existe)
ALTER TABLE personas 
DROP INDEX IF EXISTS idx_dni;

ALTER TABLE personas 
ADD UNIQUE INDEX idx_dni (dni);

SELECT 'DNI configurado como NOT NULL con índice único' as Resultado;

-- ========================================
-- 4. AGREGAR CHECK CONSTRAINTS PARA VALIDACIONES
-- ========================================

SELECT 'Agregando validaciones a nivel de base de datos...' as Paso;

-- 4.1 Validación de teléfono (solo números, 9 dígitos)
ALTER TABLE personas 
DROP CONSTRAINT IF EXISTS chk_telefono_valido;

ALTER TABLE personas 
ADD CONSTRAINT chk_telefono_valido 
CHECK (telefono REGEXP '^[0-9]{9}$');

-- 4.2 Validación de DNI (8 dígitos numéricos)
ALTER TABLE personas 
DROP CONSTRAINT IF EXISTS chk_dni_valido;

ALTER TABLE personas 
ADD CONSTRAINT chk_dni_valido 
CHECK (dni REGEXP '^[0-9]{8}$' OR dni LIKE 'TEMP%');

-- 4.3 Validación de correo electrónico
ALTER TABLE personas 
DROP CONSTRAINT IF EXISTS chk_correo_valido;

ALTER TABLE personas 
ADD CONSTRAINT chk_correo_valido 
CHECK (correo IS NULL OR correo REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$');

-- 4.4 Validación de fechas en tareas (fecha_fin >= fecha_inicio)
ALTER TABLE tareas 
DROP CONSTRAINT IF EXISTS chk_fechas_tareas;

ALTER TABLE tareas 
ADD CONSTRAINT chk_fechas_tareas 
CHECK (fecha_fin IS NULL OR fecha_fin >= fecha_inicio);

-- 4.5 Validación de valor estimado en cotizaciones (debe ser positivo)
ALTER TABLE cotizaciones 
DROP CONSTRAINT IF EXISTS chk_precio_positivo;

ALTER TABLE cotizaciones 
ADD CONSTRAINT chk_precio_positivo 
CHECK (precio_cotizado >= 0);

-- 4.6 Validación de descuento (entre 0 y 100)
ALTER TABLE cotizaciones 
DROP CONSTRAINT IF EXISTS chk_descuento_valido;

ALTER TABLE cotizaciones 
ADD CONSTRAINT chk_descuento_valido 
CHECK (descuento_aplicado >= 0 AND descuento_aplicado <= 100);

-- 4.7 Validación de vigencia de cotización (debe ser positiva)
ALTER TABLE cotizaciones 
DROP CONSTRAINT IF EXISTS chk_vigencia_positiva;

ALTER TABLE cotizaciones 
ADD CONSTRAINT chk_vigencia_positiva 
CHECK (vigencia_dias > 0);

SELECT 'Validaciones agregadas exitosamente' as Resultado;

-- ========================================
-- 5. OPTIMIZAR ÍNDICES
-- ========================================

SELECT 'Optimizando índices...' as Paso;

-- Índices para mejorar rendimiento de consultas frecuentes

-- Leads: búsqueda por usuario y estado
ALTER TABLE leads 
DROP INDEX IF EXISTS idx_leads_usuario_estado;

ALTER TABLE leads 
ADD INDEX idx_leads_usuario_estado (idusuario, estado);

-- Tareas: búsqueda por usuario y estado
ALTER TABLE tareas 
DROP INDEX IF EXISTS idx_tareas_usuario_estado;

ALTER TABLE tareas 
ADD INDEX idx_tareas_usuario_estado (idusuario, estado);

-- Personas: búsqueda por teléfono
ALTER TABLE personas 
DROP INDEX IF EXISTS idx_personas_telefono;

ALTER TABLE personas 
ADD INDEX idx_personas_telefono (telefono);

-- Historial: búsqueda por lead y fecha
ALTER TABLE leads_historial 
DROP INDEX IF EXISTS idx_historial_lead_fecha;

ALTER TABLE leads_historial 
ADD INDEX idx_historial_lead_fecha (idlead, fecha DESC);

SELECT 'Índices optimizados' as Resultado;

-- ========================================
-- 6. AGREGAR CAMPOS FALTANTES ÚTILES
-- ========================================

SELECT 'Agregando campos útiles...' as Paso;

-- Agregar campo de notas internas en personas (si no existe)
SET @col_exists_notas = 0;
SELECT COUNT(*) INTO @col_exists_notas
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'delafiber' 
AND TABLE_NAME = 'personas' 
AND COLUMN_NAME = 'notas_internas';

SET @query_notas = IF(@col_exists_notas = 0,
    'ALTER TABLE personas ADD COLUMN notas_internas TEXT NULL COMMENT "Notas internas del vendedor sobre la persona"',
    'SELECT "Columna notas_internas ya existe" as Resultado');

PREPARE stmt_notas FROM @query_notas;
EXECUTE stmt_notas;
DEALLOCATE PREPARE stmt_notas;

-- Agregar campo de última interacción en leads
SET @col_exists_interaccion = 0;
SELECT COUNT(*) INTO @col_exists_interaccion
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'delafiber' 
AND TABLE_NAME = 'leads' 
AND COLUMN_NAME = 'ultima_interaccion';

SET @query_interaccion = IF(@col_exists_interaccion = 0,
    'ALTER TABLE leads ADD COLUMN ultima_interaccion DATETIME NULL COMMENT "Fecha de la última interacción con el lead"',
    'SELECT "Columna ultima_interaccion ya existe" as Resultado');

PREPARE stmt_interaccion FROM @query_interaccion;
EXECUTE stmt_interaccion;
DEALLOCATE PREPARE stmt_interaccion;

-- Agregar trigger para actualizar última interacción
DROP TRIGGER IF EXISTS trg_actualizar_ultima_interaccion;

DELIMITER $$
CREATE TRIGGER trg_actualizar_ultima_interaccion
AFTER INSERT ON leads_historial
FOR EACH ROW
BEGIN
    UPDATE leads 
    SET ultima_interaccion = NEW.fecha 
    WHERE idlead = NEW.idlead;
END$$
DELIMITER ;

SELECT 'Campos y triggers agregados' as Resultado;

-- ========================================
-- 7. LIMPIEZA DE DATOS
-- ========================================

SELECT 'Limpiando datos inconsistentes...' as Paso;

-- Eliminar espacios en blanco de DNI y teléfonos
UPDATE personas 
SET dni = TRIM(dni),
    telefono = TRIM(telefono)
WHERE dni LIKE ' %' OR dni LIKE '% ' 
   OR telefono LIKE ' %' OR telefono LIKE '% ';

-- Normalizar correos a minúsculas
UPDATE personas 
SET correo = LOWER(TRIM(correo))
WHERE correo IS NOT NULL;

-- Eliminar leads huérfanos (sin persona asociada)
DELETE FROM leads 
WHERE idpersona NOT IN (SELECT idpersona FROM personas);

SELECT 'Datos limpiados' as Resultado;

-- ========================================
-- 8. RESUMEN DE CAMBIOS
-- ========================================

SELECT '========================================' as '';
SELECT 'RESUMEN DE OPTIMIZACIONES APLICADAS' as '';
SELECT '========================================' as '';

SELECT 'TABLA oportunidades' as Tabla, 'Marcada para eliminación' as Estado
UNION ALL
SELECT 'CAMPO usuarios.correo', 'Eliminado (migrado a personas)'
UNION ALL
SELECT 'CAMPO personas.dni', 'Ahora es NOT NULL con validación'
UNION ALL
SELECT 'CHECK CONSTRAINTS', '7 validaciones agregadas'
UNION ALL
SELECT 'ÍNDICES', '4 índices optimizados'
UNION ALL
SELECT 'CAMPOS NUEVOS', 'notas_internas, ultima_interaccion'
UNION ALL
SELECT 'TRIGGER', 'Actualización automática de última interacción';

-- ========================================
-- 9. VERIFICACIÓN FINAL
-- ========================================

SELECT 'Verificando estructura final...' as Paso;

-- Contar registros en tablas principales
SELECT 'personas' as Tabla, COUNT(*) as Total FROM personas
UNION ALL
SELECT 'leads', COUNT(*) FROM leads
UNION ALL
SELECT 'tareas', COUNT(*) FROM tareas
UNION ALL
SELECT 'cotizaciones', COUNT(*) FROM cotizaciones;

-- Verificar constraints
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'delafiber'
  AND CONSTRAINT_TYPE = 'CHECK'
ORDER BY TABLE_NAME;

SELECT '========================================' as '';
SELECT ' OPTIMIZACIÓN COMPLETADA EXITOSAMENTE' as '';
SELECT '========================================' as '';
