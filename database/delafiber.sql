-- Active: 1743133057434@@127.0.0.1@3306@delafiber

-- Eliminar y crear base de datos
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
-- Usuarios del sistema - CORREGIDA
-- =====================================================
CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre completo del usuario',
  `email` varchar(100) NOT NULL COMMENT 'Email para login',
  `password` varchar(255) NOT NULL COMMENT 'Password hasheado',
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
  KEY `idx_usuario_estado` (`estado`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idrol`) REFERENCES `roles` (`idrol`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Email: admin@delafiber.com
-- Password: password123
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `idrol`, `estado`) VALUES
('Administrador', 'admin@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Activo');

-- =====================================================
-- TABLA 6: personas
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
  KEY `idx_personas_coordenadas` (`coordenadas`),
  KEY `idx_personas_zona` (`id_zona`),
  CONSTRAINT `fk_persona_distrito` FOREIGN KEY (`iddistrito`) REFERENCES `distritos` (`iddistrito`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 7: origenes
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
  `direccion_servicio` varchar(255) DEFAULT NULL COMMENT 'Dirección específica para este servicio',
  `distrito_servicio` int(11) DEFAULT NULL COMMENT 'Distrito donde se instalará el servicio',
  `coordenadas_servicio` varchar(100) DEFAULT NULL COMMENT 'Coordenadas de instalación (lat,lng)',
  `zona_servicio` int(11) DEFAULT NULL COMMENT 'Zona de campaña para este servicio',
  `tipo_solicitud` enum('Casa','Negocio','Oficina','Otro') DEFAULT 'Casa' COMMENT 'Tipo de instalación',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idlead`),
  KEY `fk_lead_persona` (`idpersona`),
  KEY `fk_lead_usuario` (`idusuario`),
  KEY `fk_lead_origen` (`idorigen`),
  KEY `fk_lead_etapa` (`idetapa`),
  KEY `fk_lead_campania` (`idcampania`),
  KEY `fk_lead_distrito_servicio` (`distrito_servicio`),
  KEY `idx_lead_estado` (`estado`),
  KEY `idx_leads_fecha` (`created_at`),
  KEY `idx_leads_coordenadas_servicio` (`coordenadas_servicio`),
  KEY `idx_leads_zona_servicio` (`zona_servicio`),
  CONSTRAINT `fk_lead_persona` FOREIGN KEY (`idpersona`) REFERENCES `personas` (`idpersona`) ON DELETE CASCADE,
  CONSTRAINT `fk_lead_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_origen` FOREIGN KEY (`idorigen`) REFERENCES `origenes` (`idorigen`),
  CONSTRAINT `fk_lead_etapa` FOREIGN KEY (`idetapa`) REFERENCES `etapas` (`idetapa`),
  CONSTRAINT `fk_lead_campania` FOREIGN KEY (`idcampania`) REFERENCES `campanias` (`idcampania`) ON DELETE SET NULL,
  CONSTRAINT `fk_lead_distrito_servicio` FOREIGN KEY (`distrito_servicio`) REFERENCES `distritos` (`iddistrito`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 12: seguimientos
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
  KEY `idx_seguimientos_fecha` (`fecha`),
  CONSTRAINT `fk_seguimiento_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_seguimiento_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`),
  CONSTRAINT `fk_seguimiento_modalidad` FOREIGN KEY (`idmodalidad`) REFERENCES `modalidades` (`idmodalidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 13: tareas
-- =====================================================
CREATE TABLE `tareas` (
  `idtarea` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) DEFAULT NULL,
  `idusuario` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_vencimiento` datetime NOT NULL,
  `recordatorio` datetime DEFAULT NULL COMMENT 'Fecha/hora para notificar',
  `tipo_tarea` enum('Llamada','Visita','Email','Cotización','Seguimiento','Instalación','Otro') DEFAULT 'Seguimiento',
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente',
  `resultado` text DEFAULT NULL COMMENT 'Resultado de la tarea completada',
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
-- =====================================================
CREATE TABLE `servicios` (
  `idservicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `velocidad` varchar(50) DEFAULT NULL COMMENT 'Velocidad del servicio (ej: 100 Mbps)',
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idservicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `servicios` VALUES
(1, 'Internet 50 Mbps', 'Plan de internet fibra óptica 50 Mbps', '50 Mbps', 60.00, 'Internet', 'Activo', NOW()),
(2, 'Internet 100 Mbps', 'Plan de internet fibra óptica 100 Mbps', '100 Mbps', 80.00, 'Internet', 'Activo', NOW()),
(3, 'Internet 200 Mbps', 'Plan de internet fibra óptica 200 Mbps', '200 Mbps', 120.00, 'Internet', 'Activo', NOW()),
(4, 'Cable TV Básico', 'Paquete básico de cable TV', NULL, 30.00, 'Cable TV', 'Activo', NOW()),
(5, 'Cable TV HD', 'Paquete HD de cable TV', 'HD', 40.00, 'Cable TV', 'Activo', NOW()),
(6, 'Netflix Premium', 'Suscripción Netflix Premium', '4K', 20.00, 'Streaming', 'Activo', NOW()),
(7, 'Instalación', 'Costo de instalación', NULL, 50.00, 'Instalación', 'Activo', NOW());

-- =====================================================
-- TABLA 15: cotizaciones
-- =====================================================
CREATE TABLE `cotizaciones` (
  `idcotizacion` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `iddireccion` int(11) DEFAULT NULL COMMENT 'Dirección específica para esta cotización',
  `idusuario` int(11) NOT NULL,
  `numero_cotizacion` varchar(50) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `precio_cotizado` decimal(10,2) DEFAULT 0 COMMENT 'Precio base del servicio',
  `descuento_aplicado` decimal(5,2) DEFAULT 0 COMMENT 'Porcentaje de descuento',
  `precio_instalacion` decimal(10,2) DEFAULT 0 COMMENT 'Costo de instalación',
  `vigencia_dias` int(11) DEFAULT 30 COMMENT 'Días de vigencia de la cotización',
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'Fecha límite de vigencia',
  `condiciones_pago` text DEFAULT NULL COMMENT 'Condiciones de pago acordadas',
  `tiempo_instalacion` varchar(50) DEFAULT '24-48 horas' COMMENT 'Tiempo estimado de instalación',
  `observaciones` text DEFAULT NULL,
  `direccion_instalacion` varchar(255) DEFAULT NULL COMMENT 'Dirección de instalación (copiada del lead)',
  `pdf_generado` varchar(255) DEFAULT NULL COMMENT 'Ruta del PDF generado',
  `enviado_por` enum('Email','WhatsApp','Impreso','Sistema','Otro') DEFAULT NULL,
  `estado` enum('Borrador','Enviada','Aceptada','Rechazada') DEFAULT 'Borrador',
  `motivo_rechazo` text DEFAULT NULL COMMENT 'Razón del rechazo',
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcotizacion`),
  KEY `fk_cotizacion_lead` (`idlead`),
  KEY `fk_cotizacion_usuario` (`idusuario`),
  KEY `fk_cotizacion_direccion` (`iddireccion`),
  CONSTRAINT `fk_cotizacion_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_cotizacion_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`),
  CONSTRAINT `fk_cotizacion_direccion` FOREIGN KEY (`iddireccion`) REFERENCES `direcciones` (`iddireccion`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 16: cotizacion_detalle
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
-- TABLA 17: eventos_calendario
-- =====================================================
CREATE TABLE `eventos_calendario` (
  `idevento` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `idlead` int(11) DEFAULT NULL,
  `idtarea` int(11) DEFAULT NULL,
  `tipo_evento` enum('Tarea','Reunion','Instalacion','Llamada','Visita','Seguimiento','Otro') NOT NULL DEFAULT 'Otro',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `todo_el_dia` tinyint(1) DEFAULT 0,
  `ubicacion` varchar(255) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `recordatorio` int DEFAULT 15 COMMENT 'Minutos antes para recordar',
  `estado` enum('Pendiente','Completado','Cancelado') DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idevento`),
  KEY `fk_evento_usuario` (`idusuario`),
  KEY `fk_evento_lead` (`idlead`),
  KEY `fk_evento_tarea` (`idtarea`),
  KEY `idx_evento_fecha` (`fecha_inicio`),
  CONSTRAINT `fk_evento_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_evento_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_evento_tarea` FOREIGN KEY (`idtarea`) REFERENCES `tareas` (`idtarea`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 18: tb_zonas_campana
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
  `area_m2` decimal(15,2) DEFAULT NULL COMMENT 'Área en metros cuadrados',
  `iduser_create` int(11) DEFAULT NULL COMMENT 'Usuario que creó la zona',
  `iduser_update` int(11) DEFAULT NULL COMMENT 'Usuario que actualizó la zona',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_zona`),
  KEY `fk_zona_campana` (`id_campana`),
  KEY `fk_zona_user_create` (`iduser_create`),
  KEY `fk_zona_user_update` (`iduser_update`),
  CONSTRAINT `fk_zona_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanias` (`idcampania`) ON DELETE CASCADE,
  CONSTRAINT `fk_zona_user_create` FOREIGN KEY (`iduser_create`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL,
  CONSTRAINT `fk_zona_user_update` FOREIGN KEY (`iduser_update`) REFERENCES `usuarios` (`idusuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 19: tb_asignaciones_zona
-- =====================================================
CREATE TABLE `tb_asignaciones_zona` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_zona` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `meta_contactos` int(11) DEFAULT NULL COMMENT 'Meta de contactos a realizar',
  `meta_conversiones` int(11) DEFAULT NULL COMMENT 'Meta de conversiones esperadas',
  `estado` enum('Activa','Finalizada') DEFAULT 'Activa',
  PRIMARY KEY (`id_asignacion`),
  KEY `fk_asignacion_zona` (`id_zona`),
  KEY `fk_asignacion_usuario` (`idusuario`),
  CONSTRAINT `fk_asignacion_zona` FOREIGN KEY (`id_zona`) REFERENCES `tb_zonas_campana` (`id_zona`) ON DELETE CASCADE,
  CONSTRAINT `fk_asignacion_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA 20: auditoria
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
-- TABLA 21: historial_leads
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

CREATE TABLE `direcciones` (
  `iddireccion` int(11) NOT NULL AUTO_INCREMENT,
  `idpersona` int(11) NOT NULL,
  `tipo` enum('Casa','Negocio','Oficina','Otro') DEFAULT 'Casa',
  `direccion` varchar(255) NOT NULL,
  `referencias` text,
  `iddistrito` int(11),
  `coordenadas` varchar(100),
  `id_zona` int(11),
  `es_principal` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iddireccion`),
  KEY `fk_direccion_persona` (`idpersona`),
  CONSTRAINT `fk_direccion_persona` FOREIGN KEY (`idpersona`) 
    REFERENCES `personas` (`idpersona`) ON DELETE CASCADE
);

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
    u.estado,
    r.idrol,
    r.nombre as rol_nombre,
    r.nivel as rol_nivel,
    r.permisos,
    z.nombre_zona as zona_asignada_nombre
FROM usuarios u
LEFT JOIN roles r ON u.idrol = r.idrol
LEFT JOIN tb_zonas_campana z ON u.zona_asignada = z.id_zona
WHERE u.estado = 'Activo';

-- Vista de leads completos (ACTUALIZADA con campos de dirección de servicio)
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
    
    -- Dirección personal
    p.direccion as direccion_personal,
    p.coordenadas as coordenadas_personal,
    d.nombre as distrito_personal,
    
    -- Dirección de servicio (puede ser diferente)
    l.direccion_servicio,
    l.coordenadas_servicio,
    l.tipo_solicitud,
    ds.nombre as distrito_servicio,
    
    -- Dirección efectiva (prioriza la de servicio si existe)
    COALESCE(l.direccion_servicio, p.direccion) as direccion_efectiva,
    COALESCE(l.coordenadas_servicio, p.coordenadas) as coordenadas_efectivas,
    COALESCE(ds.nombre, d.nombre) as distrito_efectivo,
    
    -- Otros campos
    o.nombre as origen,
    e.nombre as etapa,
    e.color as etapa_color,
    u.nombre as vendedor,
    c.nombre as campania,
    
    -- Zona (prioriza la zona del servicio si existe)
    COALESCE(zs.nombre_zona, z.nombre_zona) as zona
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
LEFT JOIN distritos ds ON l.distrito_servicio = ds.iddistrito
INNER JOIN origenes o ON l.idorigen = o.idorigen
INNER JOIN etapas e ON l.idetapa = e.idetapa
LEFT JOIN usuarios u ON l.idusuario = u.idusuario
LEFT JOIN campanias c ON l.idcampania = c.idcampania
LEFT JOIN tb_zonas_campana z ON p.id_zona = z.id_zona
LEFT JOIN tb_zonas_campana zs ON l.zona_servicio = zs.id_zona;

CREATE OR REPLACE VIEW `v_leads_con_ubicacion` AS
SELECT 
    l.idlead,
    l.idpersona,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    p.telefono,
    p.correo,
    p.direccion as direccion_personal,
    dp.nombre as distrito_personal,    
    COALESCE(l.direccion_servicio, p.direccion) as direccion_instalacion,
    COALESCE(ds.nombre, dp.nombre) as distrito_instalacion,
    l.tipo_solicitud,
    COALESCE(l.coordenadas_servicio, p.coordenadas) as coordenadas,
    COALESCE(l.zona_servicio, p.id_zona) as id_zona,
    z.nombre_zona,
    e.nombre as etapa,
    l.estado,
    l.created_at as fecha_solicitud
    
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
LEFT JOIN distritos dp ON p.iddistrito = dp.iddistrito
LEFT JOIN distritos ds ON l.distrito_servicio = ds.iddistrito
LEFT JOIN tb_zonas_campana z ON COALESCE(l.zona_servicio, p.id_zona) = z.id_zona
INNER JOIN etapas e ON l.idetapa = e.idetapa
WHERE l.estado = 'Activo';

DELIMITER $$

CREATE PROCEDURE `sp_crear_lead_con_direccion`(
    IN p_idpersona INT,
    IN p_idusuario INT,
    IN p_idorigen INT,
    IN p_direccion_servicio VARCHAR(255),
    IN p_distrito_servicio INT,
    IN p_tipo_solicitud VARCHAR(20),
    OUT p_idlead INT
)
BEGIN
    DECLARE v_coordenadas VARCHAR(100);
    DECLARE v_zona INT;
    
    -- Insertar el lead
    INSERT INTO leads (
        idpersona, 
        idusuario, 
        idorigen, 
        idetapa,
        direccion_servicio,
        distrito_servicio,
        tipo_solicitud,
        estado
    ) VALUES (
        p_idpersona,
        p_idusuario,
        p_idorigen,
        1, -- CAPTACIÓN
        p_direccion_servicio,
        p_distrito_servicio,
        p_tipo_solicitud,
        'Activo'
    );
    
    SET p_idlead = LAST_INSERT_ID();
    
    -- Aquí se podría agregar lógica para geocodificar
    -- y asignar zona automáticamente
    
END$$

DELIMITER ;

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Ejemplo: Juan Pérez solicita servicio en dos ubicaciones diferentes

-- Insertar persona
INSERT INTO personas (nombres, apellidos, telefono, correo, direccion, iddistrito)
VALUES ('Juan', 'Pérez García', '987654321', 'juan.perez@email.com', 'Av. Benavides 123', 1);

SET @idpersona = LAST_INSERT_ID();

-- Lead 1: Servicio para su casa
INSERT INTO leads (
    idpersona, idusuario, idorigen, idetapa,
    direccion_servicio, distrito_servicio, tipo_solicitud
) VALUES (
    @idpersona, 1, 1, 1,
    'Av. Benavides 123, Grocio Prado', 1, 'Casa'
);

-- Lead 2: Servicio para su negocio (DIFERENTE UBICACIÓN)
INSERT INTO leads (
    idpersona, idusuario, idorigen, idetapa,
    direccion_servicio, distrito_servicio, tipo_solicitud
) VALUES (
    @idpersona, 1, 1, 1,
    'Jr. Comercio 456, Chincha Alta', 2, 'Negocio'
);

-- =====================================================
-- CONSULTA PARA VERIFICAR
-- =====================================================

SELECT 
    CONCAT(p.nombres, ' ', p.apellidos) as cliente,
    l.idlead,
    l.tipo_solicitud,
    l.direccion_servicio as direccion_instalacion,
    d.nombre as distrito,
    e.nombre as etapa
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
LEFT JOIN distritos d ON l.distrito_servicio = d.iddistrito
INNER JOIN etapas e ON l.idetapa = e.idetapa
WHERE p.idpersona = @idpersona;

-- =====================================================
-- NOTAS IMPORTANTES:
-- ===================

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- DATOS DE PRUEBA
-- =====================================================

-- Insertar departamentos, provincias y distritos de Ica
INSERT INTO `departamentos` (`iddepartamento`, `nombre`, `codigo`) VALUES
(1, 'Ica', '11');

INSERT INTO `provincias` (`idprovincia`, `iddepartamento`, `nombre`, `codigo`) VALUES
(1, 1, 'Chincha', '1101'),
(2, 1, 'Ica', '1102'),
(3, 1, 'Pisco', '1103');

INSERT INTO `distritos` (`iddistrito`, `idprovincia`, `nombre`, `codigo`) VALUES
-- Chincha
(1, 1, 'Chincha Alta', '110101'),
(2, 1, 'Chincha Baja', '110102'),
(3, 1, 'El Carmen', '110103'),
(4, 1, 'Grocio Prado', '110104'),
(5, 1, 'Pueblo Nuevo', '110105'),
(6, 1, 'San Pedro de Huacarpana', '110106'),
(7, 1, 'Sunampe', '110107'),
(8, 1, 'Tambo de Mora', '110108'),
-- Ica
(9, 2, 'Ica', '110201'),
(10, 2, 'La Tinguiña', '110202'),
(11, 2, 'Los Aquijes', '110203'),
(12, 2, 'Parcona', '110204'),
(13, 2, 'Pueblo Nuevo', '110205'),
-- Pisco
(14, 3, 'Pisco', '110301'),
(15, 3, 'San Andrés', '110302'),
(16, 3, 'Paracas', '110303');

-- Insertar usuarios de prueba
-- Password para todos: password123
INSERT INTO `usuarios` (`idusuario`, `nombre`, `email`, `password`, `idrol`, `turno`, `telefono`, `estado`) VALUES
(2, 'Carlos Mendoza', 'carlos@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'completo', '987654321', 'Activo'),
(3, 'María García', 'maria@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'mañana', '987654322', 'Activo'),
(4, 'Juan Pérez', 'juan@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'tarde', '987654323', 'Activo'),
(5, 'Ana Torres', 'ana@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'completo', '987654324', 'Activo');

-- Insertar campañas de prueba
INSERT INTO `campanias` (`idcampania`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `presupuesto`, `estado`) VALUES
(1, 'Campaña Verano 2025', 'Promoción de internet para temporada de verano', '2025-10-01', '2025-12-31', 15000.00, 'Activa'),
(2, 'Campaña Fiestas Patrias', 'Ofertas especiales por fiestas patrias', '2025-10-08', '2025-11-30', 10000.00, 'Activa'),
(3, 'Campaña Navidad 2025', 'Promociones navideñas', '2025-12-01', '2025-12-31', 20000.00, 'Activa');

-- Insertar zonas de campaña (ejemplo con polígonos en Chincha)
INSERT INTO `tb_zonas_campana` (`id_zona`, `id_campana`, `nombre_zona`, `descripcion`, `poligono`, `color`, `prioridad`, `estado`) VALUES
(1, 1, 'Zona Centro Chincha', 'Centro de Chincha Alta', '[{"lat":-13.4099,"lng":-76.1317},{"lat":-13.4099,"lng":-76.1217},{"lat":-13.4199,"lng":-76.1217},{"lat":-13.4199,"lng":-76.1317}]', '#3498db', 'Alta', 'Activa'),
(2, 1, 'Zona Pueblo Nuevo', 'Distrito de Pueblo Nuevo', '[{"lat":-13.4299,"lng":-76.1417},{"lat":-13.4299,"lng":-76.1317},{"lat":-13.4399,"lng":-76.1317},{"lat":-13.4399,"lng":-76.1417}]', '#27ae60', 'Media', 'Activa'),
(3, 2, 'Zona Sunampe', 'Distrito de Sunampe', '[{"lat":-13.4199,"lng":-76.1517},{"lat":-13.4199,"lng":-76.1417},{"lat":-13.4299,"lng":-76.1417},{"lat":-13.4299,"lng":-76.1517}]', '#e74c3c', 'Baja', 'Activa');

-- Asignar zonas a usuarios
INSERT INTO `tb_asignaciones_zona` (`id_zona`, `idusuario`, `meta_contactos`, `estado`) VALUES
(1, 3, 50, 'Activa'),
(2, 4, 40, 'Activa'),
(3, 5, 30, 'Activa');

-- Insertar personas de prueba
INSERT INTO `personas` (`idpersona`, `dni`, `nombres`, `apellidos`, `telefono`, `correo`, `direccion`, `referencias`, `iddistrito`, `coordenadas`, `id_zona`) VALUES
(1, '12345678', 'Roberto', 'Sánchez López', '987123456', 'roberto.sanchez@gmail.com', 'Av. Benavides 123', 'Cerca al parque principal', 1, '-13.4099,-76.1317', 1),
(2, '23456789', 'Lucía', 'Ramírez Flores', '987123457', 'lucia.ramirez@gmail.com', 'Jr. Lima 456', 'Frente a la iglesia', 1, '-13.4109,-76.1327', 1),
(3, '34567890', 'Pedro', 'Gonzales Vega', '987123458', 'pedro.gonzales@hotmail.com', 'Calle Los Pinos 789', 'Casa de dos pisos', 5, '-13.4299,-76.1417', 2),
(4, '45678901', 'Carmen', 'Díaz Morales', '987123459', 'carmen.diaz@yahoo.com', 'Av. Grau 321', 'Al lado del mercado', 7, '-13.4199,-76.1517', 3),
(5, '56789012', 'Miguel', 'Torres Ruiz', '987123460', NULL, 'Jr. Bolognesi 654', NULL, 1, '-13.4119,-76.1337', 1),
(6, '67890123', 'Rosa', 'Mendoza Castro', '987123461', 'rosa.mendoza@gmail.com', 'Calle San Martín 987', 'Esquina con Jr. Ayacucho', 2, NULL, NULL),
(7, '78901234', 'Jorge', 'Vargas Pinto', '987123462', 'jorge.vargas@outlook.com', 'Av. Progreso 147', 'Cerca al colegio', 5, '-13.4309,-76.1427', 2),
(8, '89012345', 'Elena', 'Quispe Rojas', '987123463', NULL, 'Jr. Tacna 258', NULL, 7, '-13.4209,-76.1527', 3),
(9, '90123456', 'Fernando', 'Huamán Silva', '987123464', 'fernando.huaman@gmail.com', 'Calle Comercio 369', 'Casa amarilla', 1, '-13.4129,-76.1347', 1),
(10, '01234567', 'Patricia', 'Rojas Fernández', '987123465', 'patricia.rojas@hotmail.com', 'Av. Industrial 741', 'Al frente de la fábrica', 5, '-13.4319,-76.1437', 2);

-- Insertar leads de prueba
INSERT INTO `leads` (`idlead`, `idpersona`, `idusuario`, `idorigen`, `idetapa`, `idcampania`, `nota_inicial`, `estado`, `created_at`) VALUES
(1, 1, 3, 1, 2, 1, 'Cliente interesado en plan de 100 Mbps', 'Activo', '2025-10-01 10:30:00'),
(2, 2, 3, 2, 3, 1, 'Solicitó cotización por WhatsApp', 'Activo', '2025-10-02 14:20:00'),
(3, 3, 4, 3, 1, 1, 'Referido por cliente actual', 'Activo', '2025-10-03 09:15:00'),
(4, 4, 5, 1, 4, 2, 'En negociación de precio', 'Activo', '2025-10-04 11:45:00'),
(5, 5, 3, 4, 2, 1, 'Vio publicidad en la calle', 'Activo', '2025-10-05 16:00:00'),
(6, 6, 4, 5, 1, 1, 'Llenó formulario web', 'Activo', '2025-10-06 08:30:00'),
(7, 7, 5, 2, 3, 2, 'Interesado en combo internet + cable', 'Activo', '2025-10-07 13:10:00'),
(8, 8, 3, 6, 5, 1, 'Venta cerrada - Plan 50 Mbps', 'Convertido', '2025-10-08 10:00:00'),
(9, 9, 4, 1, 2, 1, 'Preguntó por cobertura en su zona', 'Activo', '2025-10-08 15:30:00'),
(10, 10, 5, 3, 6, 2, 'No le interesó el servicio', 'Descartado', '2025-10-08 12:00:00');

-- Insertar seguimientos
INSERT INTO `seguimientos` (`idlead`, `idusuario`, `idmodalidad`, `nota`, `fecha`) VALUES
(1, 3, 1, 'Primera llamada - Cliente muy interesado', '2025-10-01 10:35:00'),
(1, 3, 2, 'Envié información por WhatsApp', '2025-10-01 11:00:00'),
(2, 3, 2, 'Cliente solicitó cotización formal', '2025-10-02 14:25:00'),
(3, 4, 1, 'Llamada de seguimiento - Aún evaluando', '2025-10-03 10:00:00'),
(4, 5, 4, 'Visita domiciliaria realizada', '2025-10-04 15:00:00'),
(5, 3, 1, 'Cliente preguntó por promociones', '2025-10-05 16:15:00'),
(8, 3, 1, 'Confirmación de instalación', '2025-10-08 10:30:00'),
(9, 4, 2, 'Envié mapa de cobertura', '2025-10-08 16:00:00');

-- Insertar tareas
INSERT INTO `tareas` (`idlead`, `idusuario`, `titulo`, `descripcion`, `fecha_vencimiento`, `prioridad`, `estado`) VALUES
(1, 3, 'Enviar cotización formal', 'Preparar cotización detallada para plan 100 Mbps', '2025-10-10 17:00:00', 'alta', 'pendiente'),
(2, 3, 'Llamar para confirmar interés', 'Hacer seguimiento de cotización enviada', '2025-10-11 10:00:00', 'media', 'pendiente'),
(3, 4, 'Agendar visita técnica', 'Coordinar visita para verificar factibilidad', '2025-10-12 14:00:00', 'alta', 'pendiente'),
(4, 5, 'Negociar descuento', 'Cliente solicita descuento especial', '2025-10-13 11:00:00', 'urgente', 'pendiente'),
(5, 3, 'Enviar información de planes', 'Compartir catálogo completo de servicios', '2025-10-05 09:00:00', 'baja', 'completada'),
(7, 5, 'Preparar combo personalizado', 'Armar paquete internet + cable TV', '2025-10-14 16:00:00', 'media', 'pendiente'),
(9, 4, 'Verificar cobertura en zona', 'Consultar con técnicos disponibilidad', '2025-10-15 10:00:00', 'alta', 'pendiente');

-- Insertar cotizaciones
INSERT INTO `cotizaciones` (`idcotizacion`, `idlead`, `idusuario`, `numero_cotizacion`, `subtotal`, `igv`, `total`, `precio_cotizado`, `descuento_aplicado`, `precio_instalacion`, `vigencia_dias`, `observaciones`, `estado`, `fecha_envio`) VALUES
(1, 2, 3, 'COT-2025-0001', 80.00, 14.40, 94.40, 80.00, 0, 50.00, 30, 'Plan Internet 100 Mbps', 'Enviada', '2025-10-02 15:00:00'),
(2, 7, 5, 'COT-2025-0002', 120.00, 21.60, 141.60, 120.00, 0, 50.00, 30, 'Combo: Internet 100 Mbps + Cable TV HD', 'Enviada', '2025-10-07 14:00:00'),
(3, 8, 3, 'COT-2025-0003', 60.00, 10.80, 70.80, 60.00, 0, 50.00, 30, 'Plan Internet 50 Mbps', 'Aceptada', '2025-10-08 09:00:00'),
(4, 4, 5, 'COT-2025-0004', 120.00, 21.60, 141.60, 120.00, 10, 50.00, 30, 'Plan Internet 200 Mbps con 10% descuento', 'Borrador', NULL);

-- Insertar detalles de cotizaciones
INSERT INTO `cotizacion_detalle` (`idcotizacion`, `idservicio`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
-- Cotización 1
(1, 2, 1, 80.00, 80.00),
-- Cotización 2
(2, 2, 1, 80.00, 80.00),
(2, 5, 1, 40.00, 40.00),
-- Cotización 3
(3, 1, 1, 60.00, 60.00),
-- Cotización 4
(4, 3, 1, 120.00, 108.00);

-- Insertar historial de leads
INSERT INTO `historial_leads` (`idlead`, `idusuario`, `etapa_anterior`, `etapa_nueva`, `motivo`, `fecha`) VALUES
(1, 3, 1, 2, 'Cliente mostró interés después de la llamada', '2025-10-01 11:00:00'),
(2, 3, 2, 3, 'Se envió cotización formal', '2025-10-02 15:00:00'),
(4, 5, 3, 4, 'Cliente solicitó negociar precio', '2025-10-04 12:00:00'),
(8, 3, 4, 5, 'Cliente aceptó cotización y firmó contrato', '2025-10-08 10:00:00'),
(10, 5, 2, 6, 'Cliente no tiene interés en el servicio', '2025-10-08 12:00:00');

-- Insertar auditoría de ejemplo
INSERT INTO `auditoria` (`idusuario`, `accion`, `tabla_afectada`, `registro_id`, `datos_nuevos`, `ip_address`) VALUES
(1, 'LOGIN', NULL, NULL, '{"usuario":"admin@delafiber.com"}', '127.0.0.1'),
(3, 'CREATE_LEAD', 'leads', 1, '{"idpersona":1,"idetapa":1}', '192.168.1.100'),
(3, 'UPDATE_LEAD', 'leads', 1, '{"idetapa":2}', '192.168.1.100'),
(3, 'CREATE_COTIZACION', 'cotizaciones', 1, '{"idlead":2,"total":94.40}', '192.168.1.100');

-- =====================================================
-- RESUMEN
-- =====================================================
SELECT '========================================' as '';
SELECT '✅ BASE DE DATOS DELAFIBER CREADA' as '';
SELECT '========================================' as '';
SELECT 'Tablas creadas: 20' as info;
SELECT 'Vistas creadas: 2' as info;
SELECT 'Roles insertados: 3' as info;
SELECT 'Usuarios insertados: 5' as info;
SELECT 'Orígenes insertados: 6' as info;
SELECT 'Etapas insertadas: 6' as info;
SELECT 'Modalidades insertadas: 6' as info;
SELECT 'Servicios insertados: 7' as info;
SELECT 'Campañas insertadas: 3' as info;
SELECT 'Zonas insertadas: 3' as info;
SELECT 'Personas insertadas: 10' as info;
SELECT 'Leads insertados: 10' as info;
SELECT 'Seguimientos insertados: 8' as info;
SELECT 'Tareas insertadas: 7' as info;
SELECT 'Cotizaciones insertadas: 3' as info;
SELECT '' as '';
SELECT '👥 USUARIOS DE PRUEBA:' as '';
SELECT '========================================' as '';
SELECT '📧 admin@delafiber.com | 🔑 password123 | 👤 Administrador' as '';
SELECT '📧 carlos@delafiber.com | 🔑 password123 | 👤 Supervisor' as '';
SELECT '📧 maria@delafiber.com | 🔑 password123 | 👤 Vendedor' as '';
SELECT '📧 juan@delafiber.com | 🔑 password123 | 👤 Vendedor' as '';
SELECT '📧 ana@delafiber.com | 🔑 password123 | 👤 Vendedor' as '';
SELECT '' as '';
SELECT '✅ Base de datos lista con datos de prueba' as '';
SELECT '========================================' as '';
