-- =====================================================
-- SISTEMA DE COLABORACIÓN ENTRE VENDEDORES
-- Fecha: 2025-10-05
-- =====================================================

USE `delafiber`;

-- =====================================================
-- 1. TABLA: comentarios_lead
-- Para que los vendedores dejen notas visibles al equipo
-- =====================================================
CREATE TABLE IF NOT EXISTS `comentarios_lead` (
  `idcomentario` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `tipo` enum('Nota','Importante','Pregunta','Respuesta') DEFAULT 'Nota',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcomentario`),
  KEY `idx_lead` (`idlead`),
  KEY `idx_usuario` (`idusuario`),
  CONSTRAINT `fk_comentario_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_comentario_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. TABLA: notificaciones
-- Sistema de notificaciones para vendedores
-- =====================================================
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `idnotificacion` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL COMMENT 'Usuario que recibe la notificación',
  `tipo` enum('Tarea','Lead','Comentario','Sistema') DEFAULT 'Sistema',
  `titulo` varchar(200) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL COMMENT 'URL a donde debe ir al hacer click',
  `leida` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idnotificacion`),
  KEY `idx_usuario` (`idusuario`),
  KEY `idx_leida` (`leida`),
  CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA: transferencias_lead
-- Historial de transferencias entre vendedores
-- =====================================================
CREATE TABLE IF NOT EXISTS `transferencias_lead` (
  `idtransferencia` int(11) NOT NULL AUTO_INCREMENT,
  `idlead` int(11) NOT NULL,
  `usuario_origen` int(11) NOT NULL COMMENT 'Vendedor que transfiere',
  `usuario_destino` int(11) NOT NULL COMMENT 'Vendedor que recibe',
  `motivo` text DEFAULT NULL,
  `nota` text DEFAULT NULL COMMENT 'Contexto para el nuevo vendedor',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idtransferencia`),
  KEY `idx_lead` (`idlead`),
  KEY `idx_origen` (`usuario_origen`),
  KEY `idx_destino` (`usuario_destino`),
  CONSTRAINT `fk_trans_lead` FOREIGN KEY (`idlead`) REFERENCES `leads` (`idlead`) ON DELETE CASCADE,
  CONSTRAINT `fk_trans_origen` FOREIGN KEY (`usuario_origen`) REFERENCES `usuarios` (`idusuario`),
  CONSTRAINT `fk_trans_destino` FOREIGN KEY (`usuario_destino`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. INSERTAR DATOS DE EJEMPLO
-- =====================================================

-- Comentarios de ejemplo
INSERT INTO `comentarios_lead` (`idlead`, `idusuario`, `comentario`, `tipo`) VALUES
(1, 1, 'Cliente muy interesado en el plan de 100 Mbps. Solicita instalación urgente.', 'Importante'),
(1, 2, '¿Ya se le envió la cotización?', 'Pregunta'),
(1, 1, 'Sí, cotización enviada el 01/10. Pendiente de respuesta.', 'Respuesta');

-- Notificaciones de ejemplo
INSERT INTO `notificaciones` (`idusuario`, `tipo`, `titulo`, `mensaje`, `url`, `leida`) VALUES
(3, 'Tarea', 'Nueva tarea asignada', 'Tienes una nueva tarea: Llamar a Juan Pérez', '/tareas', 0),
(3, 'Lead', 'Lead reasignado', 'Se te ha asignado el lead: María García', '/leads/view/2', 0),
(4, 'Comentario', 'Nuevo comentario', 'Juan dejó un comentario en el lead de Carlos Ruiz', '/leads/view/3', 0);

-- =====================================================
-- 5. VISTAS ÚTILES
-- =====================================================

-- Vista: Comentarios con información del usuario
CREATE OR REPLACE VIEW `vista_comentarios_lead` AS
SELECT 
    c.idcomentario,
    c.idlead,
    c.comentario,
    c.tipo,
    c.created_at,
    u.nombre as usuario_nombre,
    u.avatar as usuario_avatar,
    CONCAT(p.nombres, ' ', p.apellidos) as lead_nombre
FROM comentarios_lead c
JOIN usuarios u ON c.idusuario = u.idusuario
JOIN leads l ON c.idlead = l.idlead
JOIN personas p ON l.idpersona = p.idpersona
ORDER BY c.created_at DESC;

-- Vista: Notificaciones con detalles
CREATE OR REPLACE VIEW `vista_notificaciones` AS
SELECT 
    n.idnotificacion,
    n.idusuario,
    n.tipo,
    n.titulo,
    n.mensaje,
    n.url,
    n.leida,
    n.created_at,
    u.nombre as usuario_nombre,
    TIMESTAMPDIFF(MINUTE, n.created_at, NOW()) as minutos_transcurridos
FROM notificaciones n
JOIN usuarios u ON n.idusuario = u.idusuario
ORDER BY n.created_at DESC;

-- =====================================================
-- 6. PROCEDIMIENTOS ALMACENADOS
-- =====================================================

-- Procedimiento: Transferir lead entre vendedores
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS `sp_transferir_lead`(
    IN p_idlead INT,
    IN p_usuario_origen INT,
    IN p_usuario_destino INT,
    IN p_motivo TEXT,
    IN p_nota TEXT
)
BEGIN
    -- Actualizar el lead
    UPDATE leads 
    SET idusuario = p_usuario_destino 
    WHERE idlead = p_idlead;
    
    -- Registrar la transferencia
    INSERT INTO transferencias_lead (idlead, usuario_origen, usuario_destino, motivo, nota)
    VALUES (p_idlead, p_usuario_origen, p_usuario_destino, p_motivo, p_nota);
    
    -- Crear notificación para el nuevo vendedor
    INSERT INTO notificaciones (idusuario, tipo, titulo, mensaje, url)
    SELECT 
        p_usuario_destino,
        'Lead',
        'Lead transferido a ti',
        CONCAT('Se te ha asignado el lead: ', p.nombres, ' ', p.apellidos, '. Nota: ', COALESCE(p_nota, 'Sin nota')),
        CONCAT('/leads/view/', p_idlead)
    FROM leads l
    JOIN personas p ON l.idpersona = p.idpersona
    WHERE l.idlead = p_idlead;
    
    -- Crear comentario automático
    INSERT INTO comentarios_lead (idlead, idusuario, comentario, tipo)
    SELECT 
        p_idlead,
        p_usuario_origen,
        CONCAT('Lead transferido a ', u.nombre, '. Motivo: ', COALESCE(p_motivo, 'No especificado')),
        'Importante'
    FROM usuarios u
    WHERE u.idusuario = p_usuario_destino;
END$$
DELIMITER ;

-- Procedimiento: Marcar notificaciones como leídas
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS `sp_marcar_notificaciones_leidas`(
    IN p_idusuario INT
)
BEGIN
    UPDATE notificaciones 
    SET leida = 1 
    WHERE idusuario = p_idusuario AND leida = 0;
END$$
DELIMITER ;

-- =====================================================
-- 7. TRIGGERS
-- =====================================================

-- Trigger: Crear notificación cuando se asigna una tarea
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS `tr_tarea_asignada` 
AFTER INSERT ON `tareas`
FOR EACH ROW
BEGIN
    INSERT INTO notificaciones (idusuario, tipo, titulo, mensaje, url)
    VALUES (
        NEW.idusuario,
        'Tarea',
        'Nueva tarea asignada',
        CONCAT('Tarea: ', NEW.titulo, ' - Vence: ', DATE_FORMAT(NEW.fecha_vencimiento, '%d/%m/%Y')),
        CONCAT('/tareas/view/', NEW.idtarea)
    );
END$$
DELIMITER ;

-- Trigger: Crear notificación cuando hay un nuevo comentario
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS `tr_nuevo_comentario` 
AFTER INSERT ON `comentarios_lead`
FOR EACH ROW
BEGIN
    -- Notificar al vendedor asignado al lead (si no es quien comentó)
    INSERT INTO notificaciones (idusuario, tipo, titulo, mensaje, url)
    SELECT 
        l.idusuario,
        'Comentario',
        'Nuevo comentario en tu lead',
        CONCAT(u.nombre, ' comentó: ', SUBSTRING(NEW.comentario, 1, 100)),
        CONCAT('/leads/view/', NEW.idlead)
    FROM leads l
    JOIN usuarios u ON u.idusuario = NEW.idusuario
    WHERE l.idlead = NEW.idlead 
    AND l.idusuario != NEW.idusuario;
END$$
DELIMITER ;

-- =====================================================
-- LISTO! 
-- =====================================================
SELECT '✅ Sistema de colaboración creado exitosamente' as Estado;
SELECT 'Ahora los vendedores pueden:' as '';
SELECT '1. Dejar comentarios en leads' as '';
SELECT '2. Recibir notificaciones en tiempo real' as '';
SELECT '3. Transferir leads con contexto' as '';
SELECT '4. Ver historial de transferencias' as '';
