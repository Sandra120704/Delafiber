-- =====================================================
-- DELAFIBER CRM - BASE DE DATOS COMPLETA
-- Fecha: 2025-10-05
-- Versión: 1.0
-- Autor: Sistema CRM Delafiber
-- =====================================================

-- Crear base de datos
DROP DATABASE IF EXISTS `delafiber`;
CREATE DATABASE `delafiber` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `delafiber`;

-- Configuración
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =====================================================
-- TABLA 1: roles
-- Gestión de roles y permisos del sistema
-- =====================================================
CREATE TABLE `roles` (
  `idrol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `permisos` json DEFAULT NULL,
  `nivel` int(11) NOT NULL COMMENT '1=Admin, 2=Supervisor, 3=Vendedor',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idrol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` VALUES
(1, 'Administrador', 'Acceso total al sistema', '[\"*\"]', 1, NOW(), NOW()),
(2, 'Supervisor', 'Gestiona equipo de ventas', '[\"leads.view_all\", \"tareas.view_all\", \"reportes.*\", \"zonas.*\"]', 2, NOW(), NOW()),
(3, 'Vendedor', 'Gestiona sus propios leads', '[\"leads.view_own\", \"leads.create\", \"tareas.view_own\", \"cotizaciones.*\"]', 3, NOW(), NOW());

-- =====================================================
-- TABLA 2: departamentos
-- Departamentos del Perú
-- =====================================================
CREATE TABLE `departamentos` (
  `iddepartamento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`iddepartamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 3: provincias
-- Provincias del Perú
-- =====================================================
CREATE TABLE `provincias` (
  `idprovincia` int(11) NOT NULL AUTO_INCREMENT,
  `iddepartamento` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`idprovincia`),
  KEY `fk_provincia_departamento` (`iddepartamento`),
  CONSTRAINT `fk_provincia_departamento` FOREIGN KEY (`iddepartamento`) REFERENCES `departamentos` (`iddepartamento`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 4: distritos
-- Distritos del Perú
-- =====================================================
CREATE TABLE `distritos` (
  `iddistrito` int(11) NOT NULL AUTO_INCREMENT,
  `idprovincia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`iddistrito`),
  KEY `fk_distrito_provincia` (`idprovincia`),
  CONSTRAINT `fk_distrito_provincia` FOREIGN KEY (`idprovincia`) REFERENCES `provincias` (`idprovincia`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 5: usuarios
-- Usuarios del sistema
-- =====================================================
CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `idrol` int(11) DEFAULT 3,
  `turno` enum('mañana','tarde','completo') DEFAULT 'completo',
  `zona_asignada` int(11) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_usuario_rol` (`idrol`),
  KEY `idx_usuario_turno` (`turno`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idrol`) REFERENCES `roles` (`idrol`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 6: personas
-- Contactos/Clientes potenciales
-- =====================================================
CREATE TABLE `personas` (
  `idpersona` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(8) DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `referencias` text DEFAULT NULL,
  `iddistrito` int(11) DEFAULT NULL,
  `coordenadas` varchar(100) DEFAULT NULL COMMENT 'lat,lng',
  `id_zona` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idpersona`),
  UNIQUE KEY `dni` (`dni`),
  KEY `fk_persona_distrito` (`iddistrito`),
  KEY `idx_persona_telefono` (`telefono`),
  CONSTRAINT `fk_persona_distrito` FOREIGN KEY (`iddistrito`) REFERENCES `distritos` (`iddistrito`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 7: origenes
-- Origen de los leads (Facebook, WhatsApp, etc.)
-- =====================================================
CREATE TABLE `origenes` (
  `idorigen` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`idorigen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `origenes` VALUES
(1, 'Facebook', 'Leads provenientes de Facebook', '#1877f2', 'Activo'),
(2, 'WhatsApp', 'Consultas por WhatsApp', '#25d366', 'Activo'),
(3, 'Referido', 'Recomendación de clientes', '#f39c12', 'Activo'),
(4, 'Publicidad', 'Publicidad en calle/volantes', '#e74c3c', 'Activo'),
(5, 'Página Web', 'Formulario de contacto web', '#3498db', 'Activo'),
(6, 'Llamada Directa', 'Cliente llamó directamente', '#9b59b6', 'Activo');

-- =====================================================
-- TABLA 8: etapas
-- Etapas del pipeline de ventas
-- =====================================================
CREATE TABLE `etapas` (
  `idetapa` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`idetapa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `etapas` VALUES
(1, 'CAPTACIÓN', 'Primer contacto con el prospecto', 1, '#95a5a6', 'Activo'),
(2, 'INTERÉS', 'Prospecto muestra interés', 2, '#3498db', 'Activo'),
(3, 'COTIZACIÓN', 'Se envió cotización', 3, '#f39c12', 'Activo'),
(4, 'NEGOCIACIÓN', 'En proceso de negociación', 4, '#e67e22', 'Activo'),
(5, 'CIERRE', 'Venta cerrada exitosamente', 5, '#27ae60', 'Activo'),
(6, 'DESCARTADO', 'Lead descartado', 6, '#e74c3c', 'Activo');

-- =====================================================
-- TABLA 9: modalidades
-- Modalidades de contacto/seguimiento
-- =====================================================
CREATE TABLE `modalidades` (
  `idmodalidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  PRIMARY KEY (`idmodalidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `modalidades` VALUES
(1, 'Llamada Telefónica', 'phone', 'Activo'),
(2, 'WhatsApp', 'whatsapp', 'Activo'),
(3, 'Email', 'email', 'Activo'),
(4, 'Visita Presencial', 'home', 'Activo'),
(5, 'Mensaje de Texto', 'message', 'Activo'),
(6, 'Facebook Messenger', 'facebook', 'Activo');

-- =====================================================
-- TABLA 10: campanias
-- Campañas de marketing/ventas
-- =====================================================
CREATE TABLE `campanias` (
  `idcampania` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `presupuesto` decimal(10,2) DEFAULT NULL,
  `estado` enum('Activa','Inactiva','Finalizada') DEFAULT 'Activa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcampania`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 11: leads
-- Leads del sistema
-- =====================================================
CREATE TABLE `leads` (
  `idlead` int(11) NOT NULL AUTO_INCREMENT,
  `idpersona` int(11) NOT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `idorigen` int(11) NOT NULL,
  `idetapa` int(11) DEFAULT 1,
  `idcampania` int(11) DEFAULT NULL,
  `nota_inicial` text DEFAULT NULL,
  `estado` enum('Activo','Convertido','Descartado') DEFAULT 'Activo',
  `fecha_conversion` datetime DEFAULT NULL,
  `motivo_descarte` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idlead`),
  KEY `fk_lead_persona` (`idpersona`),
  KEY `fk_lead_usuario` (`idusuario`),
  KEY `fk_lead_origen` (`idorigen`),
  KEY `fk_lead_etapa` (`idetapa`),
  KEY `fk_lead_campania` (`idcampania`),
  KEY `idx_lead_estado` (`estado`),
  CONSTRAINT `fk_lead_persona` FOREIGN KEY (`idpersona`) REFERENCES `personas` (`idpersona`) ON DELETE CASCADE,
  CONSTRAINT `fk_lead_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_origen` FOREIGN KEY (`idorigen`) REFERENCES `origenes` (`idorigen`),
  CONSTRAINT `fk_lead_etapa` FOREIGN KEY (`idetapa`) REFERENCES `etapas` (`idetapa`),
  CONSTRAINT `fk_lead_campania` FOREIGN KEY (`idcampania`) REFERENCES `campanias` (`idcampania`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 12: seguimientos
-- Historial de seguimientos de leads
-- =====================================================
CREATE TABLE `seguimientos` (
  `idseguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `idmodalidad` int(11) NOT NULL,
  `nota` text NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idseguimiento`),
  KEY `fk_seguimiento_lead` (`idlead`),
  KEY `fk_seguimiento_usuario` (`idusuario`),
  KEY `fk_seguimiento_modalidad` (`idmodalidad`),
  CONSTRAINT `fk_seguimiento_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_seguimiento_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`),
  CONSTRAINT `fk_seguimiento_modalidad` FOREIGN KEY (`idmodalidad`) REFERENCES `modalidades` (`idmodalidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 13: tareas
-- Tareas y recordatorios
-- =====================================================
CREATE TABLE `tareas` (
  `idtarea` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_vencimiento` datetime NOT NULL,
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente',
  `visible_para_equipo` tinyint(1) DEFAULT 1,
  `turno_asignado` enum('mañana','tarde','ambos') DEFAULT 'ambos',
  `fecha_completada` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtarea`),
  KEY `fk_tarea_lead` (`idlead`),
  KEY `fk_tarea_usuario` (`idusuario`),
  KEY `idx_tarea_estado` (`estado`),
  KEY `idx_tarea_fecha` (`fecha_vencimiento`),
  CONSTRAINT `fk_tarea_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_tarea_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 14: servicios
-- Catálogo de servicios de Delafiber
-- =====================================================
CREATE TABLE `servicios` (
  `idservicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `servicios` VALUES
(1, 'Internet 50 Mbps', 'Plan de internet fibra óptica 50 Mbps', 60.00, 'Internet', 'Activo', NOW()),
(2, 'Internet 100 Mbps', 'Plan de internet fibra óptica 100 Mbps', 80.00, 'Internet', 'Activo', NOW()),
(3, 'Internet 200 Mbps', 'Plan de internet fibra óptica 200 Mbps', 120.00, 'Internet', 'Activo', NOW()),
(4, 'Cable TV Básico', 'Paquete básico de cable TV', 30.00, 'Cable TV', 'Activo', NOW()),
(5, 'Cable TV HD', 'Paquete HD de cable TV', 40.00, 'Cable TV', 'Activo', NOW()),
(6, 'Netflix Premium', 'Suscripción Netflix Premium', 20.00, 'Streaming', 'Activo', NOW()),
(7, 'Instalación', 'Costo de instalación', 50.00, 'Instalación', 'Activo', NOW());

-- =====================================================
-- TABLA 15: cotizaciones
-- Cotizaciones generadas
-- =====================================================
CREATE TABLE `cotizaciones` (
  `idcotizacion` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `numero_cotizacion` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Borrador','Enviada','Aceptada','Rechazada') DEFAULT 'Borrador',
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcotizacion`),
  KEY `fk_cotizacion_lead` (`idlead`),
  KEY `fk_cotizacion_usuario` (`idusuario`),
  CONSTRAINT `fk_cotizacion_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_cotizacion_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 16: cotizacion_detalle
-- Detalle de servicios en cotizaciones
-- =====================================================
CREATE TABLE `cotizacion_detalle` (
  `iddetalle` int(11) NOT NULL AUTO_INCREMENT,
  `idcotizacion` int(11) NOT NULL,
  `idservicio` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`iddetalle`),
  KEY `fk_detalle_cotizacion` (`idcotizacion`),
  KEY `fk_detalle_servicio` (`idservicio`),
  CONSTRAINT `fk_detalle_cotizacion` FOREIGN KEY (`idcotizacion`) REFERENCES `cotizaciones` (`idcotizacion`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_servicio` FOREIGN KEY (`idservicio`) REFERENCES `servicios` (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 17: tb_zonas_campana
-- Zonas geográficas de campañas
-- =====================================================
CREATE TABLE `tb_zonas_campana` (
  `id_zona` int(11) NOT NULL AUTO_INCREMENT,
  `id_campana` int(11) NOT NULL,
  `nombre_zona` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `poligono` json NOT NULL COMMENT 'Coordenadas del polígono',
  `color` varchar(7) DEFAULT '#3498db',
  `prioridad` enum('Alta','Media','Baja') DEFAULT 'Media',
  `estado` enum('Activa','Inactiva') DEFAULT 'Activa',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_zona`),
  KEY `fk_zona_campana` (`id_campana`),
  CONSTRAINT `fk_zona_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanias` (`idcampania`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 18: tb_asignaciones_zona
-- Asignación de agentes a zonas
-- =====================================================
CREATE TABLE `tb_asignaciones_zona` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_zona` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `meta_contactos` int(11) DEFAULT NULL,
  `estado` enum('Activa','Finalizada') DEFAULT 'Activa',
  PRIMARY KEY (`id_asignacion`),
  KEY `fk_asignacion_zona` (`id_zona`),
  KEY `fk_asignacion_usuario` (`idusuario`),
  CONSTRAINT `fk_asignacion_zona` FOREIGN KEY (`id_zona`) REFERENCES `tb_zonas_campana` (`id_zona`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 19: auditoria
-- Registro de acciones del sistema
-- =====================================================
CREATE TABLE `auditoria` (
  `idauditoria` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `datos_anteriores` json DEFAULT NULL,
  `datos_nuevos` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idauditoria`),
  KEY `fk_auditoria_usuario` (`idusuario`),
  KEY `idx_auditoria_fecha` (`created_at`),
  CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 20: historial_leads
-- Historial de cambios de etapa de leads
-- =====================================================
CREATE TABLE `historial_leads` (
  `idhistorial` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `etapa_anterior` int(11) DEFAULT NULL,
  `etapa_nueva` int(11) NOT NULL,
  `motivo` text DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idhistorial`),
  KEY `fk_historial_lead` (`idlead`),
  KEY `fk_historial_usuario` (`idusuario`),
  CONSTRAINT `fk_historial_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VISTAS
-- =====================================================

-- Vista de usuarios con permisos
CREATE OR REPLACE VIEW `v_usuarios_permisos` AS
SELECT 
    u.idusuario,
    u.nombre,
    u.email,
    u.turno,
    r.idrol,
    r.nombre as rol_nombre,
    r.nivel as rol_nivel,
    r.permisos,
    z.nombre_zona as zona_asignada_nombre
FROM usuarios u
LEFT JOIN roles r ON u.idrol = r.idrol
LEFT JOIN tb_zonas_campana z ON u.zona_asignada = z.id_zona
WHERE u.estado = 'Activo';

-- Vista de leads completos
CREATE OR REPLACE VIEW `v_leads_completos` AS
SELECT 
    l.idlead,
    l.estado as lead_estado,
    l.created_at as fecha_registro,
    p.idpersona,
    p.dni,
    CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
    p.telefono,
    p.correo,
    p.direccion,
    p.coordenadas,
    d.nombre as distrito,
    o.nombre as origen,
    e.nombre as etapa,
    e.color as etapa_color,
    u.nombre as vendedor,
    c.nombre as campania,
    z.nombre_zona as zona
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
INNER JOIN origenes o ON l.idorigen = o.idorigen
INNER JOIN etapas e ON l.idetapa = e.idetapa
LEFT JOIN usuarios u ON l.idusuario = u.idusuario
LEFT JOIN campanias c ON l.idcampania = c.idcampania
LEFT JOIN tb_zonas_campana z ON p.id_zona = z.id_zona;

-- =====================================================
-- ÍNDICES ADICIONALES PARA PERFORMANCE
-- =====================================================
CREATE INDEX idx_personas_coordenadas ON personas(coordenadas);
CREATE INDEX idx_personas_zona ON personas(id_zona);
CREATE INDEX idx_leads_fecha ON leads(created_at);
CREATE INDEX idx_seguimientos_fecha ON seguimientos(fecha);

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- RESUMEN
-- =====================================================
SELECT '========================================' as '';
SELECT '✅ BASE DE DATOS DELAFIBER CREADA' as '';
SELECT '========================================' as '';
SELECT 'Tablas creadas: 20' as info;
SELECT 'Vistas creadas: 2' as info;
SELECT 'Roles insertados: 3' as info;
SELECT 'Orígenes insertados: 6' as info;
SELECT 'Etapas insertadas: 6' as info;
SELECT 'Modalidades insertadas: 6' as info;
SELECT 'Servicios insertados: 7' as info;
SELECT '' as '';
SELECT '📊 Próximo paso: Ejecutar datos_prueba.sql' as '';
SELECT '========================================' as '';
