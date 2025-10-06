CREATE TABLE seguimientos LIKE seguimiento;
INSERT INTO seguimientos SELECT * FROM seguimiento;

-- Vista de compatibilidad para servicios
CREATE OR REPLACE VIEW servicios AS
SELECT 
    idservicio,
    nombre,
    descripcion,
    velocidad,
    precio_referencial,
    precio_instalacion,
    activo,
    created_at
FROM servicios_catalogo;
