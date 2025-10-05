-- Crear usuario de prueba para Delafiber CRM
USE delafiber;

-- Insertar usuario admin (contraseña: 123456)
INSERT INTO usuarios (usuario, clave, idrol, activo) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Si prefieres contraseña sin hashear (solo para desarrollo):
-- INSERT INTO usuarios (usuario, clave, idrol, activo) 
-- VALUES ('admin', '123456', 1, 1);
