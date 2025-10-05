-- ============================================
-- DELAFIBER CRM - INSTALACIÓN COMPLETA
-- ============================================
-- Fecha: 2025-10-05
-- Descripción: Script único y completo para instalar el CRM de Delafiber
--              Incluye: Estructura base + CRM de Campañas con Turf.js
-- ============================================

-- Seleccionar base de datos
USE delafiber;

-- Ubicación geográfica
CREATE TABLE departamentos (
    iddepartamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE provincias (
    idprovincia INT AUTO_INCREMENT PRIMARY KEY,
    iddepartamento INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT fk_provincia_departamento FOREIGN KEY (iddepartamento) 
        REFERENCES departamentos(iddepartamento) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE distritos (
    iddistrito INT AUTO_INCREMENT PRIMARY KEY,
    idprovincia INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idprovincia) 
        REFERENCES provincias(idprovincia) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Personas y Usuarios
CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    dni VARCHAR(8) UNIQUE,
    correo VARCHAR(150),
    telefono VARCHAR(9),
    direccion VARCHAR(255),
    referencias VARCHAR(255),
    iddistrito INT,
    coordenadas VARCHAR(50) DEFAULT NULL COMMENT 'Formato: lat,lng',
    origen ENUM('Manual', 'Importación', 'Web', 'Referido', 'Campaña') DEFAULT 'Manual',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_persona_distrito FOREIGN KEY (iddistrito) 
        REFERENCES distritos(iddistrito) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE roles (
    idrol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    idrol INT NOT NULL,
    idpersona INT,
    correo VARCHAR(150),
    ultimo_login DATETIME NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) 
        REFERENCES roles(idrol) ON DELETE RESTRICT,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) 
        REFERENCES personas(idpersona) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Campañas y Medios
CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo_campana ENUM('Prospección', 'Retención', 'Upselling', 'Recuperación') DEFAULT 'Prospección',
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(9,2) DEFAULT 0,
    objetivo_contactos INT DEFAULT 0,
    canal VARCHAR(50) DEFAULT NULL,
    estado ENUM('Activa','Inactiva') DEFAULT 'Activa',
    activo BOOLEAN DEFAULT TRUE,
    responsable INT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_campania_responsable FOREIGN KEY (responsable) 
        REFERENCES usuarios(idusuario) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE medios (
    idmedio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE difusiones (
    iddifusion INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    idmedio INT NOT NULL,
    presupuesto DECIMAL(9,2) NOT NULL DEFAULT 0,
    leads_generados INT DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_difusion_campania FOREIGN KEY (idcampania) 
        REFERENCES campanias(idcampania) ON DELETE CASCADE,
    CONSTRAINT fk_difusion_medio FOREIGN KEY (idmedio) 
        REFERENCES medios(idmedio) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Leads y Pipeline
CREATE TABLE origenes (
    idorigen INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(20) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE modalidades (
    idmodalidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pipelines (
    idpipeline INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
) ENGINE=InnoDB;

CREATE TABLE etapas (
    idetapa INT AUTO_INCREMENT PRIMARY KEY,
    idpipeline INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    orden INT NOT NULL,
    CONSTRAINT fk_etapa_pipeline FOREIGN KEY (idpipeline) 
        REFERENCES pipelines(idpipeline) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE servicios_catalogo (
    idservicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    velocidad VARCHAR(50),
    precio_referencial DECIMAL(8,2),
    precio_instalacion DECIMAL(8,2) DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE leads (
    idlead INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL,
    idetapa INT NOT NULL DEFAULT 1,
    idusuario INT NOT NULL,
    idorigen INT NOT NULL,
    idcampania INT,
    medio_comunicacion VARCHAR(100),
    idmodalidad INT,
    idusuario_registro INT,
    referido_por INT,
    estado ENUM('Convertido','Descartado') DEFAULT NULL,
    presupuesto_estimado DECIMAL(10,2) DEFAULT 0,
    numero_contrato_externo VARCHAR(50),
    fecha_conversion_contrato DATETIME,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_persona_lead (idpersona),
    
    CONSTRAINT fk_lead_persona FOREIGN KEY (idpersona) 
        REFERENCES personas(idpersona) ON DELETE CASCADE,
    CONSTRAINT fk_lead_etapa FOREIGN KEY (idetapa) 
        REFERENCES etapas(idetapa) ON DELETE RESTRICT,
    CONSTRAINT fk_lead_usuario FOREIGN KEY (idusuario) 
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT,
    CONSTRAINT fk_lead_origen FOREIGN KEY (idorigen) 
        REFERENCES origenes(idorigen) ON DELETE RESTRICT,
    CONSTRAINT fk_lead_campania FOREIGN KEY (idcampania)
        REFERENCES campanias(idcampania) ON DELETE SET NULL,
    CONSTRAINT fk_lead_modalidad FOREIGN KEY (idmodalidad)
        REFERENCES modalidades(idmodalidad) ON DELETE SET NULL,
    CONSTRAINT fk_lead_usuario_registro FOREIGN KEY (idusuario_registro)
        REFERENCES usuarios(idusuario) ON DELETE SET NULL,
    CONSTRAINT fk_lead_referido_por FOREIGN KEY (referido_por)
        REFERENCES personas(idpersona) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE leads_historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    etapa_anterior INT NULL,
    etapa_nueva INT NULL,
    fecha DATETIME NOT NULL,
    datos_adicionales JSON NULL,
    KEY idx_idlead (idlead),
    KEY idx_fecha (fecha)
) ENGINE=InnoDB;

-- Cotizaciones
CREATE TABLE cotizaciones (
    idcotizacion INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idservicio INT NOT NULL,
    precio_cotizado DECIMAL(8,2),
    descuento_aplicado DECIMAL(5,2) DEFAULT 0,
    precio_instalacion DECIMAL(8,2) DEFAULT 0,
    vigencia_dias INT DEFAULT 30,
    estado ENUM('vigente','vencida','aceptada','rechazada') DEFAULT 'vigente',
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_cotizacion_lead FOREIGN KEY (idlead) 
        REFERENCES leads(idlead) ON DELETE CASCADE,
    CONSTRAINT fk_cotizacion_servicio FOREIGN KEY (idservicio) 
        REFERENCES servicios_catalogo(idservicio) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE oportunidades (
    idoportunidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    idlead INT,
    valor_estimado DECIMAL(12,2) DEFAULT 0,
    fecha_cierre DATE,
    estado VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seguimiento y Tareas
CREATE TABLE seguimiento (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    idmodalidad INT NOT NULL,
    nota TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_seguimiento_lead FOREIGN KEY (idlead)  
        REFERENCES leads(idlead) ON DELETE CASCADE,
    CONSTRAINT fk_seguimiento_usuario FOREIGN KEY (idusuario)
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT,
    CONSTRAINT fk_seguimiento_modalidad FOREIGN KEY (idmodalidad) 
        REFERENCES modalidades(idmodalidad) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE tareas (
    idtarea INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idusuario INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    tipo_tarea VARCHAR(50) DEFAULT 'llamada',
    prioridad ENUM('baja','media','alta','urgente') DEFAULT 'media',
    fecha_inicio DATE,
    fecha_fin DATE,
    fecha_vencimiento DATETIME,
    fecha_completado DATETIME,
    estado ENUM('Pendiente','En progreso','Completada') DEFAULT 'Pendiente',
    notas_resultado TEXT,
    CONSTRAINT fk_tarea_lead FOREIGN KEY (idlead) 
        REFERENCES leads(idlead) ON DELETE CASCADE,
    CONSTRAINT fk_tarea_usuario FOREIGN KEY (idusuario) 
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- 3. TABLAS CRM DE CAMPAÑAS CON TURF.JS
-- ============================================

-- Zonas Geográficas de Campaña
CREATE TABLE tb_zonas_campana (
    id_zona INT PRIMARY KEY AUTO_INCREMENT,
    id_campana INT NOT NULL,
    nombre_zona VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    poligono JSON NOT NULL COMMENT 'Array de {lat, lng} que define el área',
    color VARCHAR(7) DEFAULT '#3498db',
    prioridad ENUM('Alta', 'Media', 'Baja') DEFAULT 'Media',
    area_m2 DECIMAL(12,2),
    create_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    inactive_at DATETIME NULL,
    iduser_create INT NOT NULL,
    iduser_update INT NULL,
    CONSTRAINT zona_fk_campana FOREIGN KEY (id_campana) 
        REFERENCES campanias(idcampania) ON DELETE CASCADE,
    CONSTRAINT zona_fk_user_create FOREIGN KEY (iduser_create) 
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT,
    CONSTRAINT zona_fk_user_update FOREIGN KEY (iduser_update) 
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT,
    INDEX idx_campana (id_campana),
    INDEX idx_prioridad (prioridad),
    INDEX idx_activo (inactive_at)
) ENGINE = InnoDB;

-- Agregar relación de personas con zonas
ALTER TABLE personas
ADD COLUMN id_zona INT NULL AFTER coordenadas,
ADD CONSTRAINT persona_fk_zona FOREIGN KEY (id_zona) 
    REFERENCES tb_zonas_campana(id_zona) ON DELETE SET NULL,
ADD INDEX idx_coordenadas (coordenadas),
ADD INDEX idx_zona (id_zona);

-- Interacciones/Seguimiento
CREATE TABLE tb_interacciones (
    id_interaccion INT PRIMARY KEY AUTO_INCREMENT,
    id_prospecto INT NOT NULL,
    id_campana INT NOT NULL,
    tipo_interaccion ENUM('Llamada', 'Visita', 'Email', 'WhatsApp', 'SMS', 'Reunión') NOT NULL,
    fecha_interaccion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resultado ENUM('Contactado', 'No Contesta', 'Interesado', 'No Interesado', 'Agendado', 'Convertido', 'Rechazado') NOT NULL,
    notas TEXT,
    proxima_accion DATE NULL,
    id_usuario INT NOT NULL,
    duracion_minutos INT DEFAULT 0,
    costo DECIMAL(8,2) DEFAULT 0.00,
    create_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT interaccion_fk_prospecto FOREIGN KEY (id_prospecto) 
        REFERENCES personas(idpersona) ON DELETE CASCADE,
    CONSTRAINT interaccion_fk_campana FOREIGN KEY (id_campana) 
        REFERENCES campanias(idcampania) ON DELETE CASCADE,
    CONSTRAINT interaccion_fk_usuario FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(idusuario) ON DELETE RESTRICT,
    INDEX idx_prospecto (id_prospecto),
    INDEX idx_campana (id_campana),
    INDEX idx_fecha (fecha_interaccion),
    INDEX idx_resultado (resultado),
    INDEX idx_usuario (id_usuario)
) ENGINE = InnoDB;

-- Asignación de Zonas a Agentes
CREATE TABLE tb_asignaciones_zona (
    id_asignacion INT PRIMARY KEY AUTO_INCREMENT,
    id_zona INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    fecha_fin DATE NULL,
    meta_contactos INT DEFAULT 0,
    meta_conversiones INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    create_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT asignacion_fk_zona FOREIGN KEY (id_zona) 
        REFERENCES tb_zonas_campana(id_zona) ON DELETE CASCADE,
    CONSTRAINT asignacion_fk_usuario FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(idusuario) ON DELETE CASCADE,
    CONSTRAINT asignacion_uk_zona_usuario UNIQUE (id_zona, id_usuario, fecha_asignacion),
    INDEX idx_usuario (id_usuario),
    INDEX idx_zona (id_zona),
    INDEX idx_activo (activo)
) ENGINE = InnoDB;

-- Métricas por Zona
CREATE TABLE tb_metricas_zona (
    id_metrica INT PRIMARY KEY AUTO_INCREMENT,
    id_zona INT NOT NULL,
    fecha DATE NOT NULL,
    total_prospectos INT DEFAULT 0,
    contactados INT DEFAULT 0,
    interesados INT DEFAULT 0,
    convertidos INT DEFAULT 0,
    rechazados INT DEFAULT 0,
    tasa_conversion DECIMAL(5,2) DEFAULT 0.00,
    tasa_contacto DECIMAL(5,2) DEFAULT 0.00,
    inversion DECIMAL(10,2) DEFAULT 0.00,
    ingreso_estimado DECIMAL(10,2) DEFAULT 0.00,
    roi DECIMAL(8,2) DEFAULT 0.00,
    create_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT metrica_fk_zona FOREIGN KEY (id_zona) 
        REFERENCES tb_zonas_campana(id_zona) ON DELETE CASCADE,
    CONSTRAINT metrica_uk_zona_fecha UNIQUE (id_zona, fecha),
    INDEX idx_fecha (fecha),
    INDEX idx_zona (id_zona)
) ENGINE = InnoDB;

-- Integración CRM-Delatel (Futura)
CREATE TABLE tb_integracion_crm_delatel (
    id_integracion INT PRIMARY KEY AUTO_INCREMENT,
    id_prospecto INT NOT NULL,
    id_contrato INT NULL,
    fecha_conversion DATETIME NULL,
    valor_contrato DECIMAL(10,2) DEFAULT 0.00,
    plan_contratado VARCHAR(100),
    observaciones TEXT,
    create_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT integ_fk_prospecto FOREIGN KEY (id_prospecto) 
        REFERENCES personas(idpersona) ON DELETE CASCADE,
    INDEX idx_prospecto (id_prospecto),
    INDEX idx_contrato (id_contrato),
    INDEX idx_fecha_conversion (fecha_conversion)
) ENGINE = InnoDB;

-- ============================================
-- 4. VISTAS SQL PARA ANÁLISIS
-- ============================================

-- Vista: Resumen de campañas
CREATE OR REPLACE VIEW vw_campanas_resumen AS
SELECT 
    c.idcampania,
    c.nombre,
    c.tipo_campana,
    c.fecha_inicio,
    c.fecha_fin,
    c.presupuesto,
    c.estado,
    COUNT(DISTINCT z.id_zona) as total_zonas,
    COUNT(DISTINCT p.idpersona) as total_prospectos,
    COUNT(DISTINCT i.id_interaccion) as total_interacciones,
    SUM(CASE WHEN i.resultado = 'Convertido' THEN 1 ELSE 0 END) as total_convertidos,
    ROUND(
        (SUM(CASE WHEN i.resultado = 'Convertido' THEN 1 ELSE 0 END) / 
         NULLIF(COUNT(DISTINCT p.idpersona), 0)) * 100, 
        2
    ) as tasa_conversion
FROM campanias c
LEFT JOIN tb_zonas_campana z ON c.idcampania = z.id_campana AND z.inactive_at IS NULL
LEFT JOIN personas p ON p.id_zona = z.id_zona
LEFT JOIN tb_interacciones i ON i.id_prospecto = p.idpersona AND i.id_campana = c.idcampania
GROUP BY c.idcampania;

-- Vista: Zonas con estadísticas
CREATE OR REPLACE VIEW vw_zonas_estadisticas AS
SELECT 
    z.id_zona,
    z.nombre_zona,
    z.id_campana,
    c.nombre as nombre_campana,
    z.prioridad,
    z.area_m2,
    ROUND(z.area_m2 / 1000000, 2) as area_km2,
    COUNT(DISTINCT p.idpersona) as total_prospectos,
    COUNT(DISTINCT a.id_usuario) as agentes_asignados,
    COUNT(DISTINCT i.id_interaccion) as total_interacciones,
    SUM(CASE WHEN i.resultado = 'Convertido' THEN 1 ELSE 0 END) as convertidos,
    ROUND(
        COUNT(DISTINCT p.idpersona) / NULLIF((z.area_m2 / 1000000), 0),
        2
    ) as densidad_prospectos_km2
FROM tb_zonas_campana z
LEFT JOIN campanias c ON z.id_campana = c.idcampania
LEFT JOIN personas p ON p.id_zona = z.id_zona
LEFT JOIN tb_asignaciones_zona a ON a.id_zona = z.id_zona AND a.activo = TRUE
LEFT JOIN tb_interacciones i ON i.id_prospecto = p.idpersona AND i.id_campana = z.id_campana
WHERE z.inactive_at IS NULL
GROUP BY z.id_zona;

-- Vista: Rendimiento de agentes
CREATE OR REPLACE VIEW vw_agentes_rendimiento AS
SELECT 
    u.idusuario,
    CONCAT(pe.nombres, ' ', pe.apellidos) as nombre_agente,
    z.id_zona,
    z.nombre_zona,
    c.nombre as nombre_campana,
    a.meta_contactos,
    a.meta_conversiones,
    COUNT(DISTINCT i.id_interaccion) as interacciones_realizadas,
    SUM(CASE WHEN i.resultado = 'Contactado' THEN 1 ELSE 0 END) as contactos_exitosos,
    SUM(CASE WHEN i.resultado = 'Convertido' THEN 1 ELSE 0 END) as conversiones,
    ROUND(
        (SUM(CASE WHEN i.resultado = 'Convertido' THEN 1 ELSE 0 END) / 
         NULLIF(a.meta_conversiones, 0)) * 100,
        2
    ) as porcentaje_meta
FROM tb_asignaciones_zona a
INNER JOIN usuarios u ON a.id_usuario = u.idusuario
INNER JOIN personas pe ON u.idpersona = pe.idpersona
INNER JOIN tb_zonas_campana z ON a.id_zona = z.id_zona
INNER JOIN campanias c ON z.id_campana = c.idcampania
LEFT JOIN tb_interacciones i ON i.id_usuario = u.idusuario 
    AND i.id_campana = c.idcampania
WHERE a.activo = TRUE
GROUP BY u.idusuario, z.id_zona;

-- Vista: Conversión CRM a Delatel
CREATE OR REPLACE VIEW vw_conversion_crm_delatel AS
SELECT 
    z.nombre_zona,
    c.nombre as nombre_campana,
    COUNT(p.idpersona) as total_prospectos,
    COUNT(i.id_contrato) as total_convertidos,
    SUM(i.valor_contrato) as valor_total_contratos,
    ROUND(
        (COUNT(i.id_contrato) / NULLIF(COUNT(p.idpersona), 0)) * 100,
        2
    ) as tasa_conversion,
    ROUND(
        AVG(i.valor_contrato),
        2
    ) as ticket_promedio
FROM personas p
LEFT JOIN tb_zonas_campana z ON p.id_zona = z.id_zona
LEFT JOIN campanias c ON z.id_campana = c.idcampania
LEFT JOIN tb_integracion_crm_delatel i ON p.idpersona = i.id_prospecto
GROUP BY z.id_zona, c.idcampania;

-- ============================================
-- 5. ÍNDICES ADICIONALES PARA PERFORMANCE
-- ============================================

CREATE INDEX idx_interacciones_prospecto_fecha 
    ON tb_interacciones(id_prospecto, fecha_interaccion);

CREATE INDEX idx_interacciones_campana_resultado 
    ON tb_interacciones(id_campana, resultado);

CREATE INDEX idx_personas_zona_coordenadas 
    ON personas(id_zona, coordenadas);

-- ============================================
-- 6. DATOS INICIALES ESENCIALES
-- ============================================

-- Roles básicos
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Supervisor', 'Gestión de equipos y campañas'),
('Agente', 'Gestión de leads y seguimiento'),
('Vendedor', 'Ventas y cotizaciones');

-- Orígenes de leads
INSERT INTO origenes (nombre, tipo) VALUES
('Web', 'digital'),
('Redes Sociales', 'digital'),
('Referido', 'organico'),
('Llamada Directa', 'directo'),
('WhatsApp', 'digital'),
('Email', 'digital');

-- Modalidades de contacto
INSERT INTO modalidades (nombre) VALUES
('Llamada'),
('WhatsApp'),
('Email'),
('Visita'),
('Reunión Virtual');

-- Pipeline básico
INSERT INTO pipelines (nombre, descripcion) VALUES
('Ventas Fibra Óptica', 'Pipeline principal para ventas de servicios de internet');

-- Etapas del pipeline
INSERT INTO etapas (idpipeline, nombre, orden) VALUES
(1, 'Nuevo Lead', 1),
(1, 'Contactado', 2),
(1, 'Calificado', 3),
(1, 'Propuesta Enviada', 4),
(1, 'Negociación', 5),
(1, 'Cerrado Ganado', 6);

-- Servicios básicos
INSERT INTO servicios_catalogo (nombre, descripcion, velocidad, precio_referencial, precio_instalacion, activo) VALUES
('Fibra 50 Mbps', 'Plan básico de internet', '50 Mbps', 49.90, 50.00, TRUE),
('Fibra 100 Mbps', 'Plan estándar de internet', '100 Mbps', 69.90, 50.00, TRUE),
('Fibra 200 Mbps', 'Plan premium de internet', '200 Mbps', 99.90, 50.00, TRUE),
('Fibra 300 Mbps', 'Plan ultra de internet', '300 Mbps', 129.90, 50.00, TRUE);

-- Medios de difusión
INSERT INTO medios (nombre, descripcion, activo) VALUES
('Facebook Ads', 'Publicidad en Facebook', TRUE),
('Google Ads', 'Publicidad en Google', TRUE),
('Instagram', 'Publicidad en Instagram', TRUE),
('Volantes', 'Material impreso', TRUE),
('Radio', 'Publicidad en radio local', TRUE);
