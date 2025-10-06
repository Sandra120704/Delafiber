-- =====================================================
-- DELAFIBER CRM - DATOS DE PRUEBA
-- Fecha: 2025-10-05
-- Versión: 1.0
-- Descripción: Datos de prueba completos
-- =====================================================

USE `delafiber`;

-- =====================================================
-- DEPARTAMENTOS DEL PERÚ
-- =====================================================
INSERT INTO `departamentos` (`iddepartamento`, `nombre`, `codigo`) VALUES
(1, 'Amazonas', '01'),
(2, 'Áncash', '02'),
(3, 'Apurímac', '03'),
(4, 'Arequipa', '04'),
(5, 'Ayacucho', '05'),
(6, 'Cajamarca', '06'),
(7, 'Callao', '07'),
(8, 'Cusco', '08'),
(9, 'Huancavelica', '09'),
(10, 'Huánuco', '10'),
(11, 'Ica', '11'),
(12, 'Junín', '12'),
(13, 'La Libertad', '13'),
(14, 'Lambayeque', '14'),
(15, 'Lima', '15'),
(16, 'Loreto', '16'),
(17, 'Madre de Dios', '17'),
(18, 'Moquegua', '18'),
(19, 'Pasco', '19'),
(20, 'Piura', '20'),
(21, 'Puno', '21'),
(22, 'San Martín', '22'),
(23, 'Tacna', '23'),
(24, 'Tumbes', '24'),
(25, 'Ucayali', '25');

-- =====================================================
-- PROVINCIAS DE ICA (Departamento 11)
-- =====================================================
INSERT INTO `provincias` (`idprovincia`, `iddepartamento`, `nombre`, `codigo`) VALUES
(1, 11, 'Ica', '1101'),
(2, 11, 'Chincha', '1102'),
(3, 11, 'Nazca', '1103'),
(4, 11, 'Palpa', '1104'),
(5, 11, 'Pisco', '1105');

-- =====================================================
-- DISTRITOS DE CHINCHA (Provincia 2)
-- =====================================================
INSERT INTO `distritos` (`iddistrito`, `idprovincia`, `nombre`, `codigo`) VALUES
(1, 2, 'Chincha Alta', '110201'),
(2, 2, 'Alto Larán', '110202'),
(3, 2, 'Chavín', '110203'),
(4, 2, 'Chincha Baja', '110204'),
(5, 2, 'El Carmen', '110205'),
(6, 2, 'Grocio Prado', '110206'),
(7, 2, 'Pueblo Nuevo', '110207'),
(8, 2, 'San Juan de Yanac', '110208'),
(9, 2, 'San Pedro de Huacarpana', '110209'),
(10, 2, 'Sunampe', '110210'),
(11, 2, 'Tambo de Mora', '110211');

-- =====================================================
-- DISTRITOS DE ICA (Provincia 1)
-- =====================================================
INSERT INTO `distritos` (`iddistrito`, `idprovincia`, `nombre`, `codigo`) VALUES
(12, 1, 'Ica', '110101'),
(13, 1, 'La Tinguiña', '110102'),
(14, 1, 'Los Aquijes', '110103'),
(15, 1, 'Ocucaje', '110104'),
(16, 1, 'Pachacutec', '110105'),
(17, 1, 'Parcona', '110106'),
(18, 1, 'Pueblo Nuevo', '110107'),
(19, 1, 'Salas', '110108'),
(20, 1, 'San José de Los Molinos', '110109'),
(21, 1, 'San Juan Bautista', '110110'),
(22, 1, 'Santiago', '110111'),
(23, 1, 'Subtanjalla', '110112'),
(24, 1, 'Tate', '110113'),
(25, 1, 'Yauca del Rosario', '110114');

-- =====================================================
-- USUARIOS DE PRUEBA
-- Password para todos: "password"
-- =====================================================
INSERT INTO `usuarios` (`idusuario`, `nombre`, `email`, `password`, `idrol`, `turno`, `telefono`, `estado`) VALUES
(1, 'Admin Sistema', 'admin@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'completo', '987654321', 'Activo'),
(2, 'Supervisor Ventas', 'supervisor@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'completo', '987654322', 'Activo'),
(3, 'Juan Pérez', 'vendedor1@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'mañana', '987654323', 'Activo'),
(4, 'María García', 'vendedor2@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'tarde', '987654324', 'Activo'),
(5, 'Carlos Ruiz', 'vendedor3@delafiber.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'completo', '987654325', 'Activo');

-- =====================================================
-- CAMPAÑA DE PRUEBA
-- =====================================================
INSERT INTO `campanias` (`idcampania`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `presupuesto`, `estado`) VALUES
(1, 'Fibra Óptica Q4 2025', 'Campaña de expansión de fibra óptica en Chincha', '2025-10-01', '2025-12-31', 50000.00, 'Activa');

-- =====================================================
-- ZONAS DE CAMPAÑA (Chincha)
-- =====================================================
INSERT INTO `tb_zonas_campana` (`id_zona`, `id_campana`, `nombre_zona`, `descripcion`, `poligono`, `color`, `prioridad`, `estado`) VALUES
(1, 1, 'Zona Norte - Grocio Prado', 'Cobertura en Grocio Prado', 
'[{"lat":-13.4093,"lng":-76.1318},{"lat":-13.4093,"lng":-76.1218},{"lat":-13.3993,"lng":-76.1218},{"lat":-13.3993,"lng":-76.1318}]', 
'#e74c3c', 'Alta', 'Activa'),
(2, 1, 'Zona Centro - Chincha Alta', 'Cobertura en Chincha Alta', 
'[{"lat":-13.4193,"lng":-76.1318},{"lat":-13.4193,"lng":-76.1218},{"lat":-13.4293,"lng":-76.1218},{"lat":-13.4293,"lng":-76.1318}]', 
'#3498db', 'Media', 'Activa'),
(3, 1, 'Zona Sur - Sunampe', 'Cobertura en Sunampe', 
'[{"lat":-13.4393,"lng":-76.1318},{"lat":-13.4393,"lng":-76.1218},{"lat":-13.4493,"lng":-76.1218},{"lat":-13.4493,"lng":-76.1318}]', 
'#f39c12', 'Baja', 'Activa');

-- =====================================================
-- ASIGNACIÓN DE VENDEDORES A ZONAS
-- =====================================================
INSERT INTO `tb_asignaciones_zona` (`id_zona`, `idusuario`, `meta_contactos`, `estado`) VALUES
(1, 3, 50, 'Activa'),
(2, 4, 50, 'Activa'),
(3, 5, 30, 'Activa');

-- =====================================================
-- PERSONAS DE PRUEBA
-- =====================================================
INSERT INTO `personas` (`idpersona`, `dni`, `nombres`, `apellidos`, `telefono`, `correo`, `direccion`, `iddistrito`, `coordenadas`, `id_zona`) VALUES
(1, '12345678', 'María', 'López García', '987654001', 'maria.lopez@email.com', 'Av. Benavides 123', 1, '-13.4093,-76.1318', 2),
(2, '23456789', 'José', 'Pérez Sánchez', '987654002', 'jose.perez@email.com', 'Jr. Lima 456', 1, '-13.4193,-76.1268', 2),
(3, '34567890', 'Ana', 'García Torres', '987654003', 'ana.garcia@email.com', 'Av. Grau 789', 6, '-13.3993,-76.1268', 1),
(4, '45678901', 'Carlos', 'Ruiz Mendoza', '987654004', 'carlos.ruiz@email.com', 'Jr. Bolognesi 321', 10, '-13.4393,-76.1268', 3),
(5, '56789012', 'Laura', 'Martínez Flores', '987654005', 'laura.martinez@email.com', 'Av. San Martín 654', 1, '-13.4243,-76.1268', 2),
(6, '67890123', 'Pedro', 'Sánchez Rojas', '987654006', 'pedro.sanchez@email.com', 'Jr. Ayacucho 987', 6, '-13.4043,-76.1268', 1),
(7, '78901234', 'Rosa', 'Torres Vega', '987654007', 'rosa.torres@email.com', 'Av. Progreso 147', 10, '-13.4443,-76.1268', 3),
(8, '89012345', 'Miguel', 'Flores Castro', '987654008', 'miguel.flores@email.com', 'Jr. Comercio 258', 1, '-13.4143,-76.1268', 2),
(9, '90123456', 'Elena', 'Castro Díaz', '987654009', 'elena.castro@email.com', 'Av. Industrial 369', 6, '-13.4093,-76.1218', 1),
(10, '01234567', 'Roberto', 'Díaz Morales', '987654010', 'roberto.diaz@email.com', 'Jr. Unión 741', 10, '-13.4393,-76.1218', 3);

-- =====================================================
-- LEADS DE PRUEBA
-- =====================================================
INSERT INTO `leads` (`idlead`, `idpersona`, `idusuario`, `idorigen`, `idetapa`, `idcampania`, `nota_inicial`, `estado`) VALUES
(1, 1, 3, 1, 2, 1, 'Interesada en plan 100 Mbps, preguntó por precio', 'Activo'),
(2, 2, 3, 2, 3, 1, 'Solicitó cotización para plan 50 Mbps + Cable TV', 'Activo'),
(3, 3, 3, 3, 2, 1, 'Referido por cliente actual, interesado en internet', 'Activo'),
(4, 4, 4, 1, 4, 1, 'En negociación, quiere descuento', 'Activo'),
(5, 5, 4, 2, 2, 1, 'Consultó por WhatsApp, interesada en plan 200 Mbps', 'Activo'),
(6, 6, 3, 4, 1, 1, 'Vio publicidad en la calle, primer contacto', 'Activo'),
(7, 7, 5, 5, 3, 1, 'Llenó formulario web, solicitó cotización', 'Activo'),
(8, 8, 4, 1, 5, 1, 'Cliente cerrado, firmó contrato', 'Convertido'),
(9, 9, 3, 2, 2, 1, 'Interesada en combo internet + cable', 'Activo'),
(10, 10, 5, 3, 1, 1, 'Referido, primer contacto pendiente', 'Activo');

-- =====================================================
-- SEGUIMIENTOS DE PRUEBA
-- =====================================================
INSERT INTO `seguimientos` (`idlead`, `idusuario`, `idmodalidad`, `nota`, `fecha`) VALUES
(1, 3, 2, 'Primer contacto por WhatsApp, muy interesada', '2025-10-01 10:30:00'),
(1, 3, 1, 'Llamada de seguimiento, confirmó interés en plan 100 Mbps', '2025-10-03 14:00:00'),
(2, 3, 2, 'Envié cotización por WhatsApp', '2025-10-02 11:00:00'),
(3, 3, 1, 'Llamada inicial, cliente muy receptivo', '2025-10-01 15:00:00'),
(4, 4, 4, 'Visita presencial, cliente solicitó descuento', '2025-10-03 16:00:00'),
(5, 4, 2, 'Consulta por WhatsApp sobre planes', '2025-10-02 09:00:00'),
(8, 4, 4, 'Visita para firma de contrato', '2025-10-04 10:00:00');

-- =====================================================
-- TAREAS DE PRUEBA
-- =====================================================
INSERT INTO `tareas` (`idlead`, `idusuario`, `titulo`, `descripcion`, `fecha_vencimiento`, `prioridad`, `estado`, `visible_para_equipo`, `turno_asignado`) VALUES
(1, 3, 'Llamar a María López', 'Seguimiento de cotización plan 100 Mbps', '2025-10-06 10:00:00', 'alta', 'pendiente', 1, 'mañana'),
(2, 3, 'Enviar cotización a José Pérez', 'Cotización plan 50 Mbps + Cable TV', '2025-10-06 11:00:00', 'media', 'pendiente', 1, 'mañana'),
(4, 4, 'Negociar descuento con Carlos', 'Cliente solicita 10% descuento', '2025-10-06 15:00:00', 'alta', 'pendiente', 1, 'tarde'),
(5, 4, 'Seguimiento Laura Martínez', 'Confirmar interés en plan 200 Mbps', '2025-10-06 16:00:00', 'media', 'pendiente', 1, 'tarde'),
(7, 5, 'Enviar cotización Rosa Torres', 'Cotización solicitada vía web', '2025-10-06 14:00:00', 'media', 'pendiente', 1, 'ambos'),
(9, 3, 'Visitar a Elena Castro', 'Visita para presentar combo internet + cable', '2025-10-07 10:00:00', 'media', 'pendiente', 1, 'mañana');

-- =====================================================
-- COTIZACIONES DE PRUEBA
-- =====================================================
INSERT INTO `cotizaciones` (`idcotizacion`, `idlead`, `idusuario`, `numero_cotizacion`, `subtotal`, `igv`, `total`, `estado`, `fecha_envio`) VALUES
(1, 2, 3, 'COT-2025-001', 90.00, 16.20, 106.20, 'Enviada', '2025-10-02 11:30:00'),
(2, 7, 5, 'COT-2025-002', 120.00, 21.60, 141.60, 'Enviada', '2025-10-03 14:00:00'),
(3, 8, 4, 'COT-2025-003', 130.00, 23.40, 153.40, 'Aceptada', '2025-10-04 09:00:00');

-- =====================================================
-- DETALLE DE COTIZACIONES
-- =====================================================
INSERT INTO `cotizacion_detalle` (`idcotizacion`, `idservicio`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 60.00, 60.00),
(1, 4, 1, 30.00, 30.00),
(2, 2, 1, 80.00, 80.00),
(2, 5, 1, 40.00, 40.00),
(3, 2, 1, 80.00, 80.00),
(3, 5, 1, 40.00, 40.00),
(3, 7, 1, 50.00, 50.00);

-- =====================================================
-- HISTORIAL DE LEADS
-- =====================================================
INSERT INTO `historial_leads` (`idlead`, `idusuario`, `etapa_anterior`, `etapa_nueva`, `motivo`, `fecha`) VALUES
(1, 3, 1, 2, 'Cliente mostró interés en el servicio', '2025-10-01 10:30:00'),
(2, 3, 1, 2, 'Solicitó información de precios', '2025-10-02 09:00:00'),
(2, 3, 2, 3, 'Se envió cotización', '2025-10-02 11:00:00'),
(4, 4, 1, 2, 'Cliente interesado', '2025-10-02 14:00:00'),
(4, 4, 2, 3, 'Cotización enviada', '2025-10-03 10:00:00'),
(4, 4, 3, 4, 'Cliente solicitó descuento', '2025-10-03 16:00:00'),
(8, 4, 1, 2, 'Cliente interesado', '2025-10-03 09:00:00'),
(8, 4, 2, 3, 'Cotización enviada', '2025-10-03 14:00:00'),
(8, 4, 3, 4, 'Negociación exitosa', '2025-10-04 09:00:00'),
(8, 4, 4, 5, 'Contrato firmado', '2025-10-04 10:00:00');

-- =====================================================
-- RESUMEN
-- =====================================================
SELECT '========================================' as '';
SELECT '✅ DATOS DE PRUEBA INSERTADOS' as '';
SELECT '========================================' as '';
SELECT CONCAT('Departamentos: ', COUNT(*), ' registros') as info FROM departamentos;
SELECT CONCAT('Provincias: ', COUNT(*), ' registros') as info FROM provincias;
SELECT CONCAT('Distritos: ', COUNT(*), ' registros') as info FROM distritos;
SELECT CONCAT('Usuarios: ', COUNT(*), ' registros') as info FROM usuarios;
SELECT CONCAT('Campañas: ', COUNT(*), ' registros') as info FROM campanias;
SELECT CONCAT('Zonas: ', COUNT(*), ' registros') as info FROM tb_zonas_campana;
SELECT CONCAT('Personas: ', COUNT(*), ' registros') as info FROM personas;
SELECT CONCAT('Leads: ', COUNT(*), ' registros') as info FROM leads;
SELECT CONCAT('Seguimientos: ', COUNT(*), ' registros') as info FROM seguimientos;
SELECT CONCAT('Tareas: ', COUNT(*), ' registros') as info FROM tareas;
SELECT CONCAT('Cotizaciones: ', COUNT(*), ' registros') as info FROM cotizaciones;
SELECT '' as '';
SELECT 'Sistema listo para usar' as '';
SELECT '========================================' as '';
SELECT '' as '';
SELECT 'USUARIOS DE PRUEBA:' as '';
SELECT 'Email: admin@delafiber.com | Password: password | Rol: Administrador' as '';
SELECT 'Email: supervisor@delafiber.com | Password: password | Rol: Supervisor' as '';
SELECT 'Email: vendedor1@delafiber.com | Password: password | Rol: Vendedor (Turno Mañana)' as '';
SELECT 'Email: vendedor2@delafiber.com | Password: password | Rol: Vendedor (Turno Tarde)' as '';
SELECT 'Email: vendedor3@delafiber.com | Password: password | Rol: Vendedor (Turno Completo)' as '';
SELECT '========================================' as '';
