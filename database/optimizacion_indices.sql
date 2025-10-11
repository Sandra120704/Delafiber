-- Active: 1755358617783@@127.0.0.1@3306@delafiber
-- =====================================================
-- SCRIPT DE OPTIMIZACIÓN: ÍNDICES PARA DELAFIBER CRM
-- Fecha: 2025-10-11
-- Descripción: Agrega índices para mejorar el rendimiento
-- =====================================================

USE `delafiber`;

-- =====================================================
-- TABLA: leads
-- Consultas frecuentes: filtro por etapa, estado, usuario
-- =====================================================

-- Índice para filtrar por etapa (usado en pipeline y reportes)
ALTER TABLE `leads` 
ADD INDEX `idx_leads_idetapa` (`idetapa`);

-- Índice para filtrar por estado (leads activos vs descartados)
ALTER TABLE `leads` 
ADD INDEX `idx_leads_estado` (`estado`);

-- Índice compuesto para consultas de usuario + estado
ALTER TABLE `leads` 
ADD INDEX `idx_leads_usuario_estado` (`idusuario`, `estado`);

-- Índice para búsquedas por campaña
ALTER TABLE `leads` 
ADD INDEX `idx_leads_idcampania` (`idcampania`);

-- Índice para ordenar por fecha de creación
ALTER TABLE `leads` 
ADD INDEX `idx_leads_created_at` (`created_at`);

-- =====================================================
-- TABLA: tareas
-- Consultas frecuentes: filtro por usuario, estado, fecha
-- =====================================================

-- Índice para filtrar por fecha de vencimiento (tareas del día, vencidas)
ALTER TABLE `tareas` 
ADD INDEX `idx_tareas_fecha_vencimiento` (`fecha_vencimiento`);

-- Índice compuesto para consultas de usuario + estado
ALTER TABLE `tareas` 
ADD INDEX `idx_tareas_usuario_estado` (`idusuario`, `estado`);

-- Índice compuesto para tareas pendientes por fecha
ALTER TABLE `tareas` 
ADD INDEX `idx_tareas_estado_fecha` (`estado`, `fecha_vencimiento`);

-- Índice para relacionar tareas con leads
ALTER TABLE `tareas` 
ADD INDEX `idx_tareas_idlead` (`idlead`);

-- =====================================================
-- TABLA: seguimientos
-- Consultas frecuentes: historial por lead
-- =====================================================

-- Índice para obtener seguimientos de un lead
-- NOTA: Ya existe idx_seguimientos_fecha en la tabla, lo omitimos
-- ALTER TABLE `seguimientos` 
-- ADD INDEX `idx_seguimientos_idlead` (`idlead`); -- Ya existe como fk_seguimiento_lead

-- Índice compuesto para lead + fecha (historial ordenado)
ALTER TABLE `seguimientos` 
ADD INDEX `idx_seguimientos_lead_fecha` (`idlead`, `fecha`);

-- =====================================================
-- TABLA: cotizaciones
-- Consultas frecuentes: por lead, por estado
-- =====================================================

-- Índice para obtener cotizaciones de un lead
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_cotizaciones_idlead` (`idlead`);

-- Índice para filtrar por estado
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_cotizaciones_estado` (`estado`);

-- Índice para ordenar por fecha
ALTER TABLE `cotizaciones` 
ADD INDEX `idx_cotizaciones_fecha` (`fecha_cotizacion`);

-- =====================================================
-- TABLA: personas
-- Consultas frecuentes: búsqueda por DNI, teléfono
-- =====================================================

-- Índice para búsqueda rápida por DNI (ya existe UNIQUE, pero agregamos explícito)
-- ALTER TABLE `personas` ADD INDEX `idx_personas_dni` (`dni`); -- Ya existe como UNIQUE

-- Índice para búsqueda por teléfono
ALTER TABLE `personas` 
ADD INDEX `idx_personas_telefono` (`telefono`);

-- Índice para búsqueda por nombres (FULLTEXT para búsquedas más eficientes)
ALTER TABLE `personas` 
ADD FULLTEXT INDEX `idx_personas_nombres_fulltext` (`nombres`, `apellidos`);

-- =====================================================
-- TABLA: tb_zonas_campana (nombre real en tu BD)
-- Consultas frecuentes: por campaña, por estado
-- =====================================================

-- Índice para obtener zonas de una campaña
ALTER TABLE `tb_zonas_campana` 
ADD INDEX `idx_zonas_id_campana` (`id_campana`);

-- Índice para filtrar por estado
ALTER TABLE `tb_zonas_campana` 
ADD INDEX `idx_zonas_estado` (`estado`);

-- =====================================================
-- TABLA: tb_asignaciones_zona (nombre real en tu BD)
-- Consultas frecuentes: por usuario, por zona
-- =====================================================

-- Índice para obtener zonas asignadas a un usuario
ALTER TABLE `tb_asignaciones_zona` 
ADD INDEX `idx_asignaciones_idusuario` (`idusuario`);

-- Índice para obtener usuarios asignados a una zona
ALTER TABLE `tb_asignaciones_zona` 
ADD INDEX `idx_asignaciones_id_zona` (`id_zona`);

-- Índice compuesto para verificar asignaciones únicas
ALTER TABLE `tb_asignaciones_zona` 
ADD UNIQUE INDEX `idx_asignaciones_zona_usuario` (`id_zona`, `idusuario`);

-- =====================================================
-- VERIFICACIÓN DE ÍNDICES CREADOS
-- =====================================================

-- Para verificar que los índices se crearon correctamente, ejecuta:
-- SHOW INDEX FROM leads;
-- SHOW INDEX FROM tareas;
-- SHOW INDEX FROM seguimientos;
-- SHOW INDEX FROM cotizaciones;
-- SHOW INDEX FROM personas;
-- SHOW INDEX FROM tb_zonas_campana;
-- SHOW INDEX FROM tb_asignaciones_zona;

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================
-- 1. Estos índices mejorarán significativamente el rendimiento de consultas SELECT
-- 2. Pueden ralentizar ligeramente las operaciones INSERT/UPDATE/DELETE
-- 3. En producción, ejecutar este script en horarios de bajo tráfico
-- 4. Monitorear el tamaño de la base de datos después de agregar índices
-- 5. Usar EXPLAIN antes de consultas complejas para verificar uso de índices

SELECT 'Índices agregados exitosamente. Ejecuta SHOW INDEX FROM <tabla> para verificar.' AS mensaje;
