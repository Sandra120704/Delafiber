

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

-- ========================================
-- TABLAS DE PERSONAS Y USUARIOS
-- ========================================

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
    ultimo_login DATETIME NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (idrol) 
        REFERENCES roles(idrol) ON DELETE RESTRICT,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) 
        REFERENCES personas(idpersona) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ========================================
-- TABLAS DE CAMPAÑAS Y MEDIOS
-- ========================================

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

-- ========================================
-- TABLAS DE LEADS Y PIPELINE
-- ========================================

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

-- ========================================
-- TABLAS DE COTIZACIONES Y OPORTUNIDADES
-- ========================================

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

-- ========================================
-- TABLAS DE SEGUIMIENTO Y TAREAS
-- ========================================

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

SELECT 'Tablas creadas exitosamente' as Resultado;
SELECT 'Ahora ejecutar: 3_crear_indices.sql' as 'Siguiente Paso';
