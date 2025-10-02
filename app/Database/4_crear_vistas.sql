-- ========================================
-- SCRIPT 4: CREAR VISTAS
-- ========================================
-- Este script crea todas las vistas del sistema
-- Ejecutar DESPUÉS de 3_crear_indices.sql
-- ========================================

USE delafiber;

-- ========================================
-- VISTA: USUARIOS COMPLETA
-- ========================================

CREATE VIEW vista_usuarios_completa AS
SELECT 
    u.idusuario,
    u.usuario,
    CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
    p.correo,
    p.telefono,
    r.nombre as rol,
    u.activo,
    u.fecha_creacion,
    u.ultimo_login
FROM usuarios u
LEFT JOIN personas p ON u.idpersona = p.idpersona
LEFT JOIN roles r ON u.idrol = r.idrol;

-- ========================================
-- VISTA: CAMPAÑAS DASHBOARD
-- ========================================

CREATE VIEW vista_campanas_dashboard AS
SELECT 
    c.idcampania,
    c.nombre,
    c.descripcion,
    c.estado,
    c.fecha_inicio,
    c.fecha_fin,
    c.presupuesto,
    CONCAT(p.nombres, ' ', p.apellidos) as responsable_nombre,
    COALESCE(SUM(d.presupuesto), 0) as inversion_total,
    COALESCE(SUM(d.leads_generados), 0) as leads_total,
    COUNT(DISTINCT d.idmedio) as medios_count,
    CASE 
        WHEN COALESCE(SUM(d.presupuesto), 0) > 0 
        THEN ROUND((COALESCE(SUM(d.leads_generados), 0) / SUM(d.presupuesto)) * 100, 2)
        ELSE 0 
    END as costo_por_lead
FROM campanias c
LEFT JOIN usuarios u ON u.idusuario = c.responsable
LEFT JOIN personas p ON p.idpersona = u.idpersona
LEFT JOIN difusiones d ON d.idcampania = c.idcampania
GROUP BY c.idcampania;

-- ========================================
-- VISTA: LEADS COMPLETA
-- ========================================

CREATE VIEW vista_leads_completa AS
SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    p.dni,
    p.telefono,
    p.correo,
    p.direccion,
    e.nombre as etapa_actual,
    o.nombre as origen,
    c.nombre as campania,
    CONCAT(pu.nombres, ' ', pu.apellidos) as vendedor_asignado,
    l.estado,
    l.presupuesto_estimado,
    l.numero_contrato_externo,
    l.fecha_registro,
    l.fecha_conversion_contrato,
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

SELECT 'Vistas creadas exitosamente' as Resultado;
SELECT COUNT(*) as 'Total Vistas' FROM INFORMATION_SCHEMA.VIEWS 
    WHERE TABLE_SCHEMA = 'delafiber';
SELECT 'Ahora ejecutar: 5_insertar_datos_iniciales.sql' as 'Siguiente Paso';
