-- Active: 1758854975906@@127.0.0.1@3306@delafiber
DROP DATABASE IF EXISTS delafiber;
CREATE DATABASE delafiber;
USE delafiber;

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
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) 
        REFERENCES roles(idrol) ON DELETE RESTRICT,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) 
        REFERENCES personas(idpersona) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    presupuesto DECIMAL(9,2) DEFAULT 0,
    estado ENUM('Activa','Inactiva') DEFAULT 'Activa',
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

-- Índices para mejorar rendimiento
CREATE INDEX idx_personas_dni ON personas(dni);
CREATE INDEX idx_personas_correo ON personas(correo);
CREATE INDEX idx_usuarios_usuario ON usuarios(usuario);
CREATE INDEX idx_usuarios_activo ON usuarios(activo);
CREATE INDEX idx_personas_nombre ON personas(nombres, apellidos);
CREATE INDEX idx_personas_telefono ON personas(telefono);

-- Índices para campañas 
CREATE INDEX idx_campanias_estado ON campanias(estado);
CREATE INDEX idx_campanias_fechas ON campanias(fecha_inicio, fecha_fin);
CREATE INDEX idx_campanias_responsable ON campanias(responsable);

-- Índices para leads
CREATE INDEX idx_leads_estado ON leads(estado);
CREATE INDEX idx_leads_fecha ON leads(fecha_registro);
CREATE INDEX idx_leads_usuario ON leads(idusuario);
CREATE INDEX idx_leads_campania ON leads(idcampania);

-- Índices para seguimiento
CREATE INDEX idx_seguimiento_fecha ON seguimiento(fecha);
CREATE INDEX idx_seguimiento_lead ON seguimiento(idlead);

-- Índices para tareas
CREATE INDEX idx_tareas_estado ON tareas(estado);
CREATE INDEX idx_tareas_fecha_vencimiento ON tareas(fecha_vencimiento);
CREATE INDEX idx_tareas_usuario ON tareas(idusuario);

-- Datos iniciales del sistema
INSERT INTO departamentos (nombre) VALUES ('Ica');
INSERT INTO provincias (nombre, iddepartamento) VALUES ('Chincha', 1);
INSERT INTO distritos (nombre, idprovincia) VALUES 
('Chincha Alta', 1), 
('Sunampe', 1), 
('Grocio Prado', 1), 
('Pueblo Nuevo', 1);

-- Roles del sistema
INSERT INTO roles (nombre, descripcion) VALUES 
('admin', 'Administrador con acceso total al sistema'), 
('vendedor', 'Vendedor que gestiona leads y clientes'), 
('supervisor', 'Supervisor que controla reportes y equipos');

-- Personas de ejemplo
INSERT INTO personas (nombres, apellidos, dni, correo, telefono, direccion, iddistrito) VALUES
('Juan Carlos', 'Pérez López', '12345678', 'juan.perez@delafiber.com', '999111222', 'Av. Los Incas 123', 1),
('María Elena', 'López García', '87654321', 'maria.lopez@delafiber.com', '999222333', 'Calle Principal 456', 2),
('Carlos Alberto', 'García Torres', '11223344', 'carlos.garcia@delafiber.com', '999333444', 'Jr. Libertad 789', 3),
('Ana Sofía', 'Torres Ruiz', '44332211', 'ana.torres@delafiber.com', '999444555', 'Urb. Las Flores Mz A Lt 10', 4);

-- Usuarios del sistema
INSERT INTO usuarios (usuario, clave, idrol, idpersona) VALUES
('jperez', '123456', 1, 1),
('mlopez', '123456', 2, 2),
('cgarcia', '123456', 2, 3),
('atorres', '123456', 2, 4);

-- Orígenes de leads
INSERT INTO origenes (nombre, tipo) VALUES 
('Campaña Digital', 'campaña'), 
('Referido de Cliente', 'referido'), 
('Contacto Directo', NULL), 
('Evento o Feria', NULL), 
('Marketing Offline', 'campaña'), 
('Redes Sociales', 'campaña'), 
('Página Web', NULL),
('Volanteo', 'campaña');

-- Modalidades de comunicación
INSERT INTO modalidades (nombre) VALUES 
('Llamada telefónica'), 
('WhatsApp'), 
('Correo electrónico'), 
('Reunión presencial'),
('Videollamada'),
('Mensaje de texto');

-- Pipeline principal de ventas
INSERT INTO pipelines (nombre, descripcion) VALUES 
('Pipeline Principal', 'Proceso general de ventas para servicios de fibra óptica');

-- Etapas del proceso de venta
INSERT INTO etapas (idpipeline, nombre, orden) VALUES
(1, 'CAPTACION', 1),
(1, 'CONTACTO', 2),
(1, 'INTERES', 3),
(1, 'COTIZACION', 4),
(1, 'NEGOCIACION', 5),
(1, 'CIERRE', 6),
(1, 'VENTA', 7);

-- Medios de publicidad disponibles
INSERT INTO medios (nombre, descripcion) VALUES
('Facebook Ads', 'Publicidad pagada en Facebook e Instagram'),
('Google Ads', 'Publicidad en Google y red de display'),
('WhatsApp Business', 'Marketing directo por WhatsApp'),
('Volanteo', 'Distribución de material impreso'),
('Referidos', 'Programa de referidos de clientes'),
('Página Web', 'Formularios de contacto del sitio web'),
('Radio', 'Publicidad en radios locales'),
('Eventos', 'Participación en ferias y eventos');

-- Catálogo de servicios básico
INSERT INTO servicios_catalogo (nombre, descripcion, velocidad, precio_referencial, precio_instalacion) VALUES
('Fibra Básica', 'Plan básico para uso doméstico', '50 Mbps', 79.90, 99.00),
('Fibra Estándar', 'Plan ideal para familias', '100 Mbps', 99.90, 99.00),
('Fibra Premium', 'Plan de alta velocidad', '200 Mbps', 139.90, 149.00),
('Fibra Ultra', 'Plan para uso intensivo', '300 Mbps', 179.90, 199.00),
('Empresarial Básico', 'Plan para pequeñas empresas', '100 Mbps', 199.90, 299.00),
('Empresarial Premium', 'Plan para medianas empresas', '500 Mbps', 399.90, 499.00);

-- Campañas de ejemplo
INSERT INTO campanias (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, responsable) VALUES 
('Campaña Navidad 2024', 'Promoción especial para época navideña', '2024-12-01', '2024-12-31', 5000.00, 1),
('Lanzamiento Chincha', 'Campaña de lanzamiento en distrito de Chincha', '2024-11-15', '2025-02-15', 8000.00, 2),
('Fibra para Todos', 'Campaña masiva de penetración de mercado', '2025-01-01', '2025-06-30', 15000.00, 1);

-- Difusiones de las campañas
INSERT INTO difusiones (idcampania, idmedio, presupuesto, leads_generados) VALUES 
(1, 1, 2000.00, 45),
(1, 2, 1500.00, 32),
(2, 1, 3000.00, 68),
(2, 4, 1000.00, 25),
(3, 1, 5000.00, 120),
(3, 2, 4000.00, 95),
(3, 6, 2000.00, 55);

-- Vistas para facilitar consultas
CREATE VIEW vista_usuarios_completa AS
SELECT 
    u.idusuario,
    u.usuario,
    CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo,
    p.correo,
    p.telefono,
    r.nombre as rol,
    u.activo,
    u.fecha_creacion
FROM usuarios u
LEFT JOIN personas p ON u.idpersona = p.idpersona
LEFT JOIN roles r ON u.idrol = r.idrol;

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
    CONCAT(u.nombres, ' ', u.apellidos) as vendedor_asignado,
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
LEFT JOIN personas u ON u_vendedor.idpersona = u.idpersona
LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
LEFT JOIN provincias pr ON d.idprovincia = pr.idprovincia
LEFT JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento;
ALTER TABLE usuarios ADD COLUMN ultimo_login DATETIME NULL AFTER correo;

ALTER TABLE usuarios ADD COLUMN correo VARCHAR(150) NULL AFTER idpersona;
ALTER TABLE usuarios ADD COLUMN activo BOOLEAN DEFAULT TRUE AFTER correo;