

USE delafiber;

-- Insertar personas de prueba con direcciones (ignora duplicados de DNI)
INSERT IGNORE INTO personas (nombres, apellidos, dni, telefono, correo, direccion, iddistrito) VALUES
('Juan Carlos', 'Pérez García', '12345678', '987654321', 'juan.perez@email.com', 'Av. Larco 1234', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Miraflores' LIMIT 1)),
('María Elena', 'Rodríguez López', '23456789', '987654322', 'maria.rodriguez@email.com', 'Calle Los Pinos 567', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Isidro' LIMIT 1)),
('Carlos Alberto', 'Sánchez Torres', '34567890', '987654323', 'carlos.sanchez@email.com', 'Jr. Las Flores 890', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Surco' LIMIT 1)),
('Ana Patricia', 'Mendoza Silva', '45678901', '987654324', 'ana.mendoza@email.com', 'Av. La Molina 2345', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'La Molina' LIMIT 1)),
('Roberto Luis', 'Castro Vargas', '56789012', '987654325', 'roberto.castro@email.com', 'Calle San Borja 456', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Borja' LIMIT 1)),
('Laura Isabel', 'Flores Quispe', '67890123', '987654326', 'laura.flores@email.com', 'Av. Grau 789', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Barranco' LIMIT 1)),
('Diego Martín', 'Ramírez Cruz', '78901234', '987654327', 'diego.ramirez@email.com', 'Jr. Lima 1122', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Chorrillos' LIMIT 1)),
('Sofía Andrea', 'Gutiérrez Rojas', '89012345', '987654328', 'sofia.gutierrez@email.com', 'Av. La Marina 3344', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Miguel' LIMIT 1));

-- Insertar leads asociados a estas personas
INSERT INTO leads (idpersona, idetapa, idusuario, idorigen, idcampania, estado, fecha_registro) VALUES
-- Leads activos
((SELECT idpersona FROM personas WHERE dni = '12345678'), 1, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '23456789'), 2, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '34567890'), 3, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '45678901'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '56789012'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '67890123'), 2, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, 'Descartado', NOW()),
((SELECT idpersona FROM personas WHERE dni = '78901234'), 1, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '89012345'), 2, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    NULL, NULL, NOW());
INSERT IGNORE INTO campanias (nombre, descripcion, fecha_inicio, fecha_fin, presupuesto, estado, responsable) VALUES
('Campaña Verano 2025', 'Promoción de fibra óptica para temporada de verano', '2025-01-01', '2025-03-31', 5000.00, 'Activa', 
    (SELECT idusuario FROM usuarios LIMIT 1)),
('Campaña Black Friday', 'Descuentos especiales por Black Friday', '2024-11-20', '2024-11-30', 3000.00, 'Activa', 
    (SELECT idusuario FROM usuarios LIMIT 1)),
('Campaña Navidad', 'Promoción navideña con instalación gratis', '2024-12-01', '2024-12-31', 4000.00, 'Activa', 
    (SELECT idusuario FROM usuarios LIMIT 1));

-- Asociar algunos leads a campañas
UPDATE leads SET idcampania = (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Verano 2025' LIMIT 1)
WHERE idpersona IN (
    SELECT idpersona FROM personas WHERE dni IN ('12345678', '23456789', '45678901')
);

UPDATE leads SET idcampania = (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Black Friday' LIMIT 1)
WHERE idpersona IN (
    SELECT idpersona FROM personas WHERE dni IN ('34567890', '56789012')
);

UPDATE leads SET idcampania = (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Navidad' LIMIT 1)
WHERE idpersona IN (
    SELECT idpersona FROM personas WHERE dni IN ('67890123', '78901234')
);

-- ========================================
-- MÁS PERSONAS Y LEADS PARA ESTADÍSTICAS
-- ========================================

-- Agregar más personas en diferentes distritos
INSERT IGNORE INTO personas (nombres, apellidos, dni, telefono, correo, direccion, iddistrito) VALUES
-- Más en Miraflores
('Pedro José', 'Vega Morales', '11111111', '987111111', 'pedro.vega@email.com', 'Av. Benavides 555', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Miraflores' LIMIT 1)),
('Carmen Rosa', 'Díaz Paredes', '22222222', '987222222', 'carmen.diaz@email.com', 'Calle Schell 777', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Miraflores' LIMIT 1)),
('Miguel Ángel', 'Torres Ruiz', '33333333', '987333333', 'miguel.torres@email.com', 'Av. Javier Prado 999', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Isidro' LIMIT 1)),
('Patricia Elena', 'Campos Soto', '44444444', '987444444', 'patricia.campos@email.com', 'Calle Las Begonias 1111', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Isidro' LIMIT 1)),
('Fernando Luis', 'Ríos Chávez', '55555555', '987555555', 'fernando.rios@email.com', 'Av. Primavera 2222', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Surco' LIMIT 1)),
('Gabriela María', 'Núñez Ortiz', '66666666', '987666666', 'gabriela.nunez@email.com', 'Calle Los Sauces 3333', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'Surco' LIMIT 1)),
('Ricardo José', 'Herrera Luna', '77777777', '987777777', 'ricardo.herrera@email.com', 'Av. La Fontana 4444', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'La Molina' LIMIT 1)),
('Valeria Andrea', 'Ponce Reyes', '88888888', '987888888', 'valeria.ponce@email.com', 'Calle Las Viñas 5555', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'La Molina' LIMIT 1)),
('Andrés Felipe', 'Salazar Medina', '99999999', '987999999', 'andres.salazar@email.com', 'Av. San Borja Norte 6666', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Borja' LIMIT 1)),
('Daniela Sofía', 'Carrillo Vega', '10101010', '987101010', 'daniela.carrillo@email.com', 'Calle San Borja Sur 7777', 
    (SELECT iddistrito FROM distritos WHERE nombre = 'San Borja' LIMIT 1));

-- Insertar leads para estas nuevas personas
INSERT INTO leads (idpersona, idetapa, idusuario, idorigen, idcampania, estado, fecha_registro) VALUES
-- 🎯 LEADS ACTIVOS (azul)
((SELECT idpersona FROM personas WHERE dni = '11111111'), 1, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Verano 2025' LIMIT 1), NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '22222222'), 2, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Verano 2025' LIMIT 1), NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '33333333'), 1, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Black Friday' LIMIT 1), NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '55555555'), 3, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Navidad' LIMIT 1), NULL, NOW()),
((SELECT idpersona FROM personas WHERE dni = '44444444'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Black Friday' LIMIT 1), 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '66666666'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Verano 2025' LIMIT 1), 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '77777777'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Navidad' LIMIT 1), 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '99999999'), 4, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Black Friday' LIMIT 1), 'Convertido', NOW()),
((SELECT idpersona FROM personas WHERE dni = '88888888'), 2, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Verano 2025' LIMIT 1), 'Descartado', NOW()),
((SELECT idpersona FROM personas WHERE dni = '10101010'), 1, 
    (SELECT idusuario FROM usuarios LIMIT 1), 
    (SELECT idorigen FROM origenes LIMIT 1), 
    (SELECT idcampania FROM campanias WHERE nombre = 'Campaña Navidad' LIMIT 1), 'Descartado', NOW());

-- ========================================
-- RESUMEN DE DATOS INSERTADOS
-- ========================================

SELECT 'DATOS DE PRUEBA INSERTADOS CORRECTAMENTE!' as 'RESULTADO';
SELECT '' as '';
SELECT 'RESUMEN DE DATOS:' as '';
SELECT COUNT(*) as 'Total Personas' FROM personas;
SELECT COUNT(*) as 'Total Leads' FROM leads;
SELECT COUNT(*) as 'Total Campañas' FROM campanias WHERE estado = 'Activa';
SELECT '' as '';
SELECT 'DISTRIBUCIÓN DE LEADS:' as '';
SELECT 
    CASE 
        WHEN estado IS NULL THEN 'Leads Activos'
        WHEN estado = 'Convertido' THEN 'Clientes'
        WHEN estado = 'Descartado' THEN 'Descartados'
    END as 'Tipo',
    COUNT(*) as 'Cantidad'
FROM leads
GROUP BY estado;
SELECT '' as '';
SELECT 'LEADS POR DISTRITO:' as '';
SELECT 
    d.nombre as 'Distrito',
    COUNT(l.idlead) as 'Total Leads',
    SUM(CASE WHEN l.estado = 'Convertido' THEN 1 ELSE 0 END) as 'Convertidos',
    SUM(CASE WHEN l.estado IS NULL THEN 1 ELSE 0 END) as 'Activos',
    SUM(CASE WHEN l.estado = 'Descartado' THEN 1 ELSE 0 END) as 'Descartados'
FROM distritos d
LEFT JOIN personas p ON p.iddistrito = d.iddistrito
LEFT JOIN leads l ON l.idpersona = p.idpersona
WHERE l.idlead IS NOT NULL
GROUP BY d.iddistrito
ORDER BY COUNT(l.idlead) DESC;
SELECT '' as '';
SELECT 'Ahora recarga el mapa en: http://delafiber.test/mapa' as 'SIGUIENTE PASO';

SELECT * FROM usuarios;