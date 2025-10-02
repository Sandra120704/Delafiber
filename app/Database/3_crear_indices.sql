-- ========================================
-- SCRIPT 3: CREAR ÍNDICES
-- ========================================
-- Este script crea todos los índices para optimizar el rendimiento
-- Ejecutar DESPUÉS de 2_crear_tablas.sql
-- ========================================

USE delafiber;

-- ========================================
-- ÍNDICES PARA PERSONAS
-- ========================================

CREATE INDEX idx_personas_dni ON personas(dni);
CREATE INDEX idx_personas_correo ON personas(correo);
CREATE INDEX idx_personas_nombre ON personas(nombres, apellidos);
CREATE INDEX idx_personas_telefono ON personas(telefono);
CREATE INDEX idx_personas_distrito ON personas(iddistrito);

-- ========================================
-- ÍNDICES PARA USUARIOS
-- ========================================

CREATE INDEX idx_usuarios_usuario ON usuarios(usuario);
CREATE INDEX idx_usuarios_activo ON usuarios(activo);

-- ========================================
-- ÍNDICES PARA CAMPAÑAS
-- ========================================

CREATE INDEX idx_campanias_estado ON campanias(estado);
CREATE INDEX idx_campanias_fechas ON campanias(fecha_inicio, fecha_fin);
CREATE INDEX idx_campanias_responsable ON campanias(responsable);

-- ========================================
-- ÍNDICES PARA LEADS
-- ========================================

CREATE INDEX idx_leads_estado ON leads(estado);
CREATE INDEX idx_leads_fecha ON leads(fecha_registro);
CREATE INDEX idx_leads_usuario ON leads(idusuario);
CREATE INDEX idx_leads_campania ON leads(idcampania);
CREATE INDEX idx_leads_fecha_conversion ON leads(fecha_conversion_contrato);

-- ========================================
-- ÍNDICES PARA SEGUIMIENTO
-- ========================================

CREATE INDEX idx_seguimiento_fecha ON seguimiento(fecha);
CREATE INDEX idx_seguimiento_lead ON seguimiento(idlead);
CREATE INDEX idx_seguimiento_lead_fecha ON seguimiento(idlead, fecha);

-- ========================================
-- ÍNDICES PARA TAREAS
-- ========================================

CREATE INDEX idx_tareas_estado ON tareas(estado);
CREATE INDEX idx_tareas_fecha_vencimiento ON tareas(fecha_vencimiento);
CREATE INDEX idx_tareas_usuario ON tareas(idusuario);
CREATE INDEX idx_tareas_lead ON tareas(idlead);

SELECT 'Índices creados exitosamente' as Resultado;
SELECT COUNT(*) as 'Total Índices' FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'delafiber';
SELECT 'Ahora ejecutar: 4_crear_vistas.sql' as 'Siguiente Paso';
