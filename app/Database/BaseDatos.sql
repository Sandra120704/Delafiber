-- Crear la base de datos
CREATE DATABASE delafiber;
USE delafiber;

-- Crear tabla de departamentos
CREATE TABLE departamentos (
    iddepartamento INT AUTO_INCREMENT PRIMARY KEY,
    departamento VARCHAR(50) NOT NULL
);


CREATE TABLE provincias (
    idprovincia INT AUTO_INCREMENT PRIMARY KEY,
    provincia VARCHAR(50) NOT NULL,
    iddepartamento INT NOT NULL,
    CONSTRAINT fk_provincia_departamento FOREIGN KEY (iddepartamento) REFERENCES departamentos(iddepartamento)
);

CREATE TABLE distritos (
    iddistrito INT AUTO_INCREMENT PRIMARY KEY,
    distrito VARCHAR(50) NOT NULL,
    idprovincia INT NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idprovincia) REFERENCES provincias(idprovincia)
);


CREATE TABLE personas (
    idpersona INT AUTO_INCREMENT PRIMARY KEY,
    apellidos VARCHAR(100) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    telprimario VARCHAR(20) NOT NULL,
    telalternativo VARCHAR(20),
    email VARCHAR(150),
    direccion TEXT,
    referencia TEXT,
    iddistrito INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_persona_distrito FOREIGN KEY (iddistrito) REFERENCES distritos(iddistrito)
);

DROP TABLE usuarios; (
    idusuario INT AUTO_INCREMENT PRIMARY KEY,
    idpersona INT NOT NULL UNIQUE,
    nombreusuario VARCHAR(50) UNIQUE NOT NULL,
    claveacceso VARCHAR(100) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_usuario_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona)
);


CREATE TABLE campanias (
    idcampania INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fechainicio DATE NOT NULL,
    fechafin DATE NOT NULL,
    inversion DECIMAL(9,2),
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME
);

CREATE TABLE medios (
    idmedio INT AUTO_INCREMENT PRIMARY KEY,
    tipo_medio ENUM('REDES SOCIALES','PRESENCIAL') NOT NULL,
    medio VARCHAR(100) NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME
);

CREATE TABLE difusiones (
    iddifusion INT AUTO_INCREMENT PRIMARY KEY,
    idcampania INT NOT NULL,
    idmedio INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_difusion_campania FOREIGN KEY (idcampania) REFERENCES campanias(idcampania),
    CONSTRAINT fk_difusion_medio FOREIGN KEY (idmedio) REFERENCES medios(idmedio)
);


CREATE TABLE etapas (
    idetapa INT AUTO_INCREMENT PRIMARY KEY,
    nombreetapa VARCHAR(100) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME
);


CREATE TABLE leads (
    idlead INT AUTO_INCREMENT PRIMARY KEY,
    iddifusion INT NOT NULL,
    idpersona INT NOT NULL,
    idusuarioregistro INT NOT NULL,
    idusuarioresponsable INT NOT NULL,
    fechasignacion DATE NOT NULL,
    estado ENUM('nuevo', 'contactado', 'interesado', 'no interesado', 'perdido') DEFAULT 'nuevo',
    fecharegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_leads_difusion FOREIGN KEY (iddifusion) REFERENCES difusiones(iddifusion),
    CONSTRAINT fk_leads_persona FOREIGN KEY (idpersona) REFERENCES personas(idpersona),
    CONSTRAINT fk_leads_usuario_registro FOREIGN KEY (idusuarioregistro) REFERENCES usuarios(idusuario),
    CONSTRAINT fk_leads_usuario_responsable FOREIGN KEY (idusuarioresponsable) REFERENCES usuarios(idusuario)
);


CREATE TABLE seguimientos (
    idseguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idlead INT NOT NULL,
    idetapa INT NOT NULL,
    modalidadcontacto VARCHAR(100),
    fecha DATE NOT NULL,
    hora TIME,
    comentarios TEXT,
    creado DATETIME NOT NULL DEFAULT NOW(),
    modificado DATETIME,
    CONSTRAINT fk_seguimiento_lead FOREIGN KEY (idlead) REFERENCES leads(idlead),
    CONSTRAINT fk_seguimiento_etapa FOREIGN KEY (idetapa) REFERENCES etapas(idetapa)
);

SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
    p.email,
    p.telprimario,
    l.fechasignacion,
    l.estado,
    u1.nombreusuario AS registrado_por,
    u2.nombreusuario AS responsable,
    c.nombre AS campania,
    m.medio
FROM leads l
JOIN personas p ON l.idpersona = p.idpersona
JOIN usuarios u1 ON l.idusuarioregistro = u1.idusuario
JOIN usuarios u2 ON l.idusuarioresponsable = u2.idusuario
JOIN difusiones d ON l.iddifusion = d.iddifusion
JOIN campanias c ON d.idcampania = c.idcampania
JOIN medios m ON d.idmedio = m.idmedio;

SELECT 
    idlead,
	 estado,
	 fechasignacion
FROM leads
WHERE estado = 'interesado';

SELECT 
    u.nombreusuario,
    COUNT(l.idlead) AS total_leads
FROM leads l
JOIN usuarios u ON l.idusuarioresponsable = u.idusuario
GROUP BY u.nombreusuario;

SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre_lead,
    e.nombreetapa,
    s.fecha,
    s.modalidadcontacto
FROM leads l
JOIN personas p ON l.idpersona = p.idpersona
JOIN seguimientos s ON s.idlead = l.idlead
JOIN etapas e ON s.idetapa = e.idetapa
WHERE s.idseguimiento IN (
    SELECT MAX(idseguimiento)
    FROM seguimientos
    GROUP BY idlead
);

SELECT 
    estado,
    COUNT(*) AS total
FROM leads
GROUP BY estado;

SELECT 
    p.idpersona,
    p.nombres,
    p.apellidos,
    d.distrito,
    pr.provincia,
    dp.departamento
FROM personas p
JOIN distritos d ON p.iddistrito = d.iddistrito
JOIN provincias pr ON d.idprovincia = pr.idprovincia
JOIN departamentos dp ON pr.iddepartamento = dp.iddepartamento;


-- Asume que ya tienes distrito/provincia/departamento
INSERT INTO departamentos (departamento) VALUES ('Lima');

INSERT INTO provincias (provincia, iddepartamento)
VALUES ('Lima', 1);

INSERT INTO distritos (distrito, idprovincia)
VALUES ('Miraflores', 1);

INSERT INTO personas (apellidos, nombres, telprimario, iddistrito, creado)
VALUES ('Pérez', 'Juan', '987654321', 1, NOW());

INSERT INTO usuarios (idpersona, nombreusuario, claveacceso, estado, creado)
VALUES (2, 'juanp', '123456', 1, NOW());

SELECT * FROM personas WHERE idpersona = 1;

SELECT * FROM personas;


INSERT INTO campanias (nombre, fechainicio, fechafin, creado)
VALUES ('Campaña Agosto', '2025-08-01', '2025-08-31', NOW());

INSERT INTO medios (tipo_medio, medio, creado)
VALUES ('REDES SOCIALES', 'Facebook', NOW());

INSERT INTO difusiones (idcampania, idmedio, creado)
VALUES (1, 1, NOW());



SELECT * FROM usuarios;