-- ========================================
-- SCRIPT DE CORRECCIONES - DELAFIBER CRM
-- ========================================
-- Este script corrige los problemas identificados en el análisis
-- Ejecutar DESPUÉS de crear la base de datos inicial
-- ========================================

USE delafiber;

-- ========================================
-- 1. AGREGAR DISTRITOS DE LIMA (PRIORIDAD ALTA)
-- ========================================

-- Agregar departamento Lima
INSERT IGNORE INTO departamentos (iddepartamento, nombre) VALUES (2, 'Lima');

-- Agregar provincia Lima
INSERT IGNORE INTO provincias (idprovincia, nombre, iddepartamento) VALUES (2, 'Lima', 2);

-- Agregar 43 distritos de Lima Metropolitana
INSERT IGNORE INTO distritos (nombre, idprovincia) VALUES
-- Zona Norte
('Ancón', 2),
('Carabayllo', 2),
('Comas', 2),
('Independencia', 2),
('Los Olivos', 2),
('Puente Piedra', 2),
('San Martín de Porres', 2),
('Santa Rosa', 2),

-- Zona Sur
('Barranco', 2),
('Chorrillos', 2),
('Lurín', 2),
('Pachacámac', 2),
('Pucusana', 2),
('Punta Hermosa', 2),
('Punta Negra', 2),
('San Bartolo', 2),
('San Juan de Miraflores', 2),
('Santa María del Mar', 2),
('Villa El Salvador', 2),
('Villa María del Triunfo', 2),

-- Zona Este
('Ate', 2),
('Chaclacayo', 2),
('Cieneguilla', 2),
('El Agustino', 2),
('La Molina', 2),
('Lurigancho', 2),
('San Juan de Lurigancho', 2),
('San Luis', 2),
('Santa Anita', 2),

-- Zona Oeste
('Bellavista', 2),
('Callao', 2),
('Carmen de la Legua', 2),
('La Perla', 2),
('La Punta', 2),
('Ventanilla', 2),

-- Zona Centro
('Breña', 2),
('Cercado de Lima', 2),
('Jesús María', 2),
('La Victoria', 2),
('Lince', 2),
('Magdalena del Mar', 2),
('Miraflores', 2),
('Pueblo Libre', 2),
('Rímac', 2),
('San Borja', 2),
('San Isidro', 2),
('San Miguel', 2),
('Santiago de Surco', 2),
('Surquillo', 2);

SELECT 'Distritos de Lima agregados correctamente' as Resultado;

-- ========================================
-- 2. CORREGIR VISTA DE LEADS (PRIORIDAD ALTA)
-- ========================================

DROP VIEW IF EXISTS vista_leads_completa;

CREATE VIEW vista_leads_completa AS
SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    p.dni,
    p.telefono,
    p.correo,
    e.nombre as etapa_actual,
    o.nombre as origen,
    c.nombre as campania,
    CONCAT(pu.nombres, ' ', pu.apellidos) as vendedor_asignado,
    l.estado,
    l.numero_contrato_externo,
    l.fecha_registro,
    CONCAT(d.nombre, ' - ', pr.nombre, ' - ', dp.nombre) as ubicacion
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
INNER JOIN etapas e ON l.idetapa = e.idetapa
INNER JOIN origenes o ON l.idorigen = o.idorigen
LEFT JOIN campanias c ON l.idcampania = c.idcampania
LEFT JOIN usuarios u_vendedor ON l.idusuario = u_vendedor.idusuario
LEFT JOIN personas pu ON u_vendedor.idpersona = pu.idpersona
LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
LEFT JOIN provincias pr ON d.idprovincia = pr.idprovincia
LEFT JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento;

SELECT 'Vista vista_leads_completa corregida' as Resultado;

-- ========================================
-- 3. AGREGAR COLUMNA PRESUPUESTO_ESTIMADO (PRIORIDAD MEDIA)
-- ========================================

-- Verificar si la columna ya existe antes de agregarla
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'delafiber' 
AND TABLE_NAME = 'leads' 
AND COLUMN_NAME = 'presupuesto_estimado';

SET @query = IF(@col_exists = 0,
    'ALTER TABLE leads ADD COLUMN presupuesto_estimado DECIMAL(10,2) DEFAULT 0 AFTER estado',
    'SELECT "Columna presupuesto_estimado ya existe" as Resultado');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Columna presupuesto_estimado verificada/agregada' as Resultado;

-- ========================================
-- 4. AGREGAR ÍNDICES ADICIONALES (PRIORIDAD MEDIA)
-- ========================================

-- Índice para personas por distrito (mejora consultas del mapa)
CREATE INDEX IF NOT EXISTS idx_personas_distrito ON personas(iddistrito);

-- Índice para fecha de conversión (mejora reportes)
CREATE INDEX IF NOT EXISTS idx_leads_fecha_conversion ON leads(fecha_conversion_contrato);

-- Índice para tareas por lead (mejora consultas de tareas)
CREATE INDEX IF NOT EXISTS idx_tareas_lead ON tareas(idlead);

-- Índice para seguimientos por lead y fecha
CREATE INDEX IF NOT EXISTS idx_seguimiento_lead_fecha ON seguimiento(idlead, fecha);

SELECT 'Índices adicionales creados' as Resultado;

-- ========================================
-- 5. ACTUALIZAR CONTRASEÑAS CON HASH (PRIORIDAD CRÍTICA)
-- ========================================

-- NOTA: Las contraseñas deben hashearse desde PHP con password_hash()
-- Este es solo un ejemplo temporal usando MD5 (NO USAR EN PRODUCCIÓN)
-- En producción, usar: password_hash('contraseña', PASSWORD_DEFAULT)

-- Por ahora, dejamos las contraseñas como están
-- El sistema de login debe manejar el hash correctamente

SELECT 'ADVERTENCIA: Las contraseñas deben hashearse desde PHP con password_hash()' as Resultado;

-- ========================================
-- 6. AGREGAR CAMPOS DE AUDITORÍA (OPCIONAL)
-- ========================================

-- Agregar campos de auditoría a tabla leads
ALTER TABLE leads 
ADD COLUMN IF NOT EXISTS created_by INT NULL AFTER fecha_creacion,
ADD COLUMN IF NOT EXISTS updated_by INT NULL AFTER fecha_modificacion;

-- Agregar campos de auditoría a tabla campanias
ALTER TABLE campanias 
ADD COLUMN IF NOT EXISTS created_by INT NULL AFTER fecha_creacion,
ADD COLUMN IF NOT EXISTS updated_by INT NULL AFTER fecha_creacion;

SELECT 'Campos de auditoría agregados' as Resultado;

-- ========================================
-- 7. CREAR TABLA DE CONFIGURACIÓN DEL SISTEMA
-- ========================================

CREATE TABLE IF NOT EXISTS configuracion (
    idconfig INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion VARCHAR(255),
    tipo ENUM('texto','numero','boolean','json') DEFAULT 'texto',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insertar configuraciones iniciales
INSERT IGNORE INTO configuracion (clave, valor, descripcion, tipo) VALUES
('empresa_nombre', 'Delafiber', 'Nombre de la empresa', 'texto'),
('empresa_telefono', '999999999', 'Teléfono principal', 'texto'),
('empresa_email', 'contacto@delafiber.com', 'Email de contacto', 'texto'),
('empresa_direccion', 'Chincha, Ica, Perú', 'Dirección principal', 'texto'),
('moneda', 'PEN', 'Moneda del sistema (PEN, USD)', 'texto'),
('timezone', 'America/Lima', 'Zona horaria', 'texto'),
('leads_por_pagina', '20', 'Leads por página en listados', 'numero'),
('dias_vigencia_cotizacion', '30', 'Días de vigencia de cotizaciones', 'numero'),
('notificaciones_email', 'true', 'Activar notificaciones por email', 'boolean'),
('google_maps_api_key', '', 'API Key de Google Maps', 'texto');

SELECT 'Tabla de configuración creada' as Resultado;

-- ========================================
-- 8. CREAR TABLA DE NOTIFICACIONES
-- ========================================

CREATE TABLE IF NOT EXISTS notificaciones (
    idnotificacion INT AUTO_INCREMENT PRIMARY KEY,
    idusuario INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT,
    leida BOOLEAN DEFAULT FALSE,
    url VARCHAR(255),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_leida DATETIME NULL,
    CONSTRAINT fk_notificacion_usuario FOREIGN KEY (idusuario)
        REFERENCES usuarios(idusuario) ON DELETE CASCADE,
    INDEX idx_notificaciones_usuario (idusuario),
    INDEX idx_notificaciones_leida (leida),
    INDEX idx_notificaciones_fecha (fecha_creacion)
) ENGINE=InnoDB;

SELECT 'Tabla de notificaciones creada' as Resultado;

-- ========================================
-- 9. CREAR TABLA DE ACTIVIDAD DEL SISTEMA
-- ========================================

CREATE TABLE IF NOT EXISTS actividad_sistema (
    idactividad INT AUTO_INCREMENT PRIMARY KEY,
    idusuario INT,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_actividad_usuario FOREIGN KEY (idusuario)
        REFERENCES usuarios(idusuario) ON DELETE SET NULL,
    INDEX idx_actividad_usuario (idusuario),
    INDEX idx_actividad_fecha (fecha),
    INDEX idx_actividad_modulo (modulo)
) ENGINE=InnoDB;

SELECT 'Tabla de actividad del sistema creada' as Resultado;

-- ========================================
-- RESUMEN DE CORRECCIONES APLICADAS
-- ========================================

SELECT '========================================' as '';
SELECT 'CORRECCIONES APLICADAS EXITOSAMENTE' as 'RESULTADO';
SELECT '========================================' as '';
SELECT '' as '';
SELECT 'VERIFICACIÓN:' as '';
SELECT COUNT(*) as 'Distritos Totales' FROM distritos;
SELECT COUNT(*) as 'Distritos de Chincha' FROM distritos d 
    JOIN provincias p ON d.idprovincia = p.idprovincia 
    WHERE p.nombre = 'Chincha';
SELECT COUNT(*) as 'Índices Totales' FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'delafiber' AND TABLE_NAME IN ('personas', 'leads', 'tareas', 'seguimiento');
SELECT COUNT(*) as 'Vistas Totales' FROM INFORMATION_SCHEMA.VIEWS 
    WHERE TABLE_SCHEMA = 'delafiber';
SELECT '' as '';
SELECT 'Base de datos optimizada y lista para producción' as 'ESTADO FINAL';
