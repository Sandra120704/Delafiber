-- ========================================
-- SCRIPT 5: INSERTAR DATOS INICIALES
-- ========================================
-- Este script inserta los datos básicos del sistema
-- Ejecutar DESPUÉS de 4_crear_vistas.sql
-- ========================================

USE delafiber;

-- ========================================
-- DATOS GEOGRÁFICOS - CHINCHA, ICA
-- ========================================

INSERT INTO departamentos (nombre) VALUES ('Ica');

INSERT INTO provincias (nombre, iddepartamento) VALUES ('Chincha', 1);

INSERT INTO distritos (nombre, idprovincia) VALUES 
('Chincha Alta', 1), 
('Sunampe', 1), 
('Grocio Prado', 1), 
('Pueblo Nuevo', 1),
('Alto Larán', 1),
('Chavín', 1),
('El Carmen', 1),
('San Juan de Yanac', 1),
('San Pedro de Huacarpana', 1),
('Tambo de Mora', 1);

-- ========================================
-- ROLES DEL SISTEMA
-- ========================================

INSERT INTO roles (nombre, descripcion) VALUES 
('admin', 'Administrador con acceso total al sistema'), 
('vendedor', 'Vendedor que gestiona leads y clientes'), 
('supervisor', 'Supervisor que controla reportes y equipos');

-- ========================================
-- PERSONAS DE EJEMPLO
-- ========================================

INSERT INTO personas (nombres, apellidos, dni, correo, telefono, direccion, iddistrito) VALUES
('Juan Carlos', 'Pérez López', '12345678', 'juan.perez@delafiber.com', '999111222', 'Av. Los Incas 123', 1),
('María Elena', 'López García', '87654321', 'maria.lopez@delafiber.com', '999222333', 'Calle Principal 456', 2),
('Carlos Alberto', 'García Torres', '11223344', 'carlos.garcia@delafiber.com', '999333444', 'Jr. Libertad 789', 3),
('Ana Sofía', 'Torres Ruiz', '44332211', 'ana.torres@delafiber.com', '999444555', 'Urb. Las Flores Mz A Lt 10', 4);

-- ========================================
-- USUARIOS DEL SISTEMA
-- ========================================

-- NOTA: Las contraseñas deben hashearse en producción
INSERT INTO usuarios (usuario, clave, idrol, idpersona) VALUES
('admin', '123456', 1, 1),
('vendedor1', '123456', 2, 2),
('vendedor2', '123456', 2, 3),
('supervisor', '123456', 3, 4);

-- ========================================
-- ORÍGENES DE LEADS
-- ========================================

INSERT INTO origenes (nombre, tipo) VALUES 
('Campaña Digital', 'campaña'), 
('Referido de Cliente', 'referido'), 
('Contacto Directo', NULL), 
('Evento o Feria', NULL), 
('Marketing Offline', 'campaña'), 
('Redes Sociales', 'campaña'), 
('Página Web', NULL),
('Volanteo', 'campaña');

-- ========================================
-- MODALIDADES DE COMUNICACIÓN
-- ========================================

INSERT INTO modalidades (nombre) VALUES 
('Llamada telefónica'), 
('WhatsApp'), 
('Correo electrónico'), 
('Reunión presencial'),
('Videollamada'),
('Mensaje de texto');

-- ========================================
-- PIPELINE PRINCIPAL DE VENTAS
-- ========================================

INSERT INTO pipelines (nombre, descripcion) VALUES 
('Pipeline Principal', 'Proceso general de ventas para servicios de fibra óptica');

-- ========================================
-- ETAPAS DEL PROCESO DE VENTA
-- ========================================

INSERT INTO etapas (idpipeline, nombre, orden) VALUES
(1, 'CAPTACION', 1),
(1, 'CONTACTO', 2),
(1, 'INTERES', 3),
(1, 'COTIZACION', 4),
(1, 'NEGOCIACION', 5),
(1, 'CIERRE', 6),
(1, 'VENTA', 7);

-- ========================================
-- MEDIOS DE PUBLICIDAD DISPONIBLES
-- ========================================

INSERT INTO medios (nombre, descripcion) VALUES
('Facebook Ads', 'Publicidad pagada en Facebook e Instagram'),
('Google Ads', 'Publicidad en Google y red de display'),
('WhatsApp Business', 'Marketing directo por WhatsApp'),
('Volanteo', 'Distribución de material impreso'),
('Referidos', 'Programa de referidos de clientes'),
('Página Web', 'Formularios de contacto del sitio web'),
('Radio', 'Publicidad en radios locales'),
('Eventos', 'Participación en ferias y eventos');

-- ========================================
-- CATÁLOGO DE SERVICIOS BÁSICO
-- ========================================

INSERT INTO servicios_catalogo (nombre, descripcion, velocidad, precio_referencial, precio_instalacion) VALUES
('Fibra Básica', 'Plan básico para uso doméstico', '50 Mbps', 79.90, 99.00),
('Fibra Estándar', 'Plan ideal para familias', '100 Mbps', 99.90, 99.00),
('Fibra Premium', 'Plan de alta velocidad', '200 Mbps', 139.90, 149.00),
('Fibra Ultra', 'Plan para uso intensivo', '300 Mbps', 179.90, 199.00),
('Empresarial Básico', 'Plan para pequeñas empresas', '100 Mbps', 199.90, 299.00),
('Empresarial Premium', 'Plan para medianas empresas', '500 Mbps', 399.90, 499.00);

-- ========================================
-- CAMPAÑAS DE EJEMPLO
-- ========================================

INSERT INTO campanias (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, responsable) VALUES 
('Campaña Navidad 2024', 'Promoción especial para época navideña', '2024-12-01', '2024-12-31', 5000.00, 1),
('Lanzamiento Chincha', 'Campaña de lanzamiento en distrito de Chincha', '2024-11-15', '2025-02-15', 8000.00, 2),
('Fibra para Todos', 'Campaña masiva de penetración de mercado', '2025-01-01', '2025-06-30', 15000.00, 1);

-- ========================================
-- DIFUSIONES DE LAS CAMPAÑAS
-- ========================================

INSERT INTO difusiones (idcampania, idmedio, presupuesto, leads_generados) VALUES 
(1, 1, 2000.00, 45),
(1, 2, 1500.00, 32),
(2, 1, 3000.00, 68),
(2, 4, 1000.00, 25),
(3, 1, 5000.00, 120),
(3, 2, 4000.00, 95),
(3, 6, 2000.00, 55);

-- ========================================
-- RESUMEN DE DATOS INSERTADOS
-- ========================================

SELECT 'Datos iniciales insertados exitosamente' as Resultado;
SELECT '' as '';
SELECT 'RESUMEN:' as '';
SELECT COUNT(*) as 'Departamentos' FROM departamentos;
SELECT COUNT(*) as 'Provincias' FROM provincias;
SELECT COUNT(*) as 'Distritos' FROM distritos;
SELECT COUNT(*) as 'Roles' FROM roles;
SELECT COUNT(*) as 'Usuarios' FROM usuarios;
SELECT COUNT(*) as 'Personas' FROM personas;
SELECT COUNT(*) as 'Orígenes' FROM origenes;
SELECT COUNT(*) as 'Modalidades' FROM modalidades;
SELECT COUNT(*) as 'Etapas' FROM etapas;
SELECT COUNT(*) as 'Medios' FROM medios;
SELECT COUNT(*) as 'Servicios' FROM servicios_catalogo;
SELECT COUNT(*) as 'Campañas' FROM campanias;
SELECT '' as '';
SELECT 'Base de datos lista para usar' as 'ESTADO FINAL';
SELECT 'Usuario: admin / Contraseña: 123456' as 'ACCESO';
