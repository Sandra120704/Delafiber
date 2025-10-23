-- =====================================================
-- MEJORAS PARA INTEGRACIÓN WHATSAPP BUSINESS
-- Sistema Delafiber CRM
-- Fecha: 2025-10-22
-- =====================================================

-- =====================================================
-- 1. TABLA PARA DOCUMENTOS ADJUNTOS
-- =====================================================
-- Almacena fotos de DNI, recibos de luz/agua, etc.
CREATE TABLE IF NOT EXISTS `documentos_lead` (
  `iddocumento` INT UNSIGNED AUTO_INCREMENT,
  `idlead` INT UNSIGNED NOT NULL COMMENT 'Lead al que pertenece el documento',
  `idpersona` INT UNSIGNED NOT NULL COMMENT 'Persona dueña del documento',
  `tipo_documento` ENUM('dni_frontal', 'dni_reverso', 'recibo_luz', 'recibo_agua', 'foto_domicilio', 'otro') NOT NULL COMMENT 'Tipo de documento',
  `nombre_archivo` VARCHAR(255) NOT NULL COMMENT 'Nombre original del archivo',
  `ruta_archivo` VARCHAR(500) NOT NULL COMMENT 'Ruta donde se guardó el archivo',
  `extension` VARCHAR(10) NOT NULL COMMENT 'Extensión del archivo (jpg, png, pdf)',
  `tamano_kb` INT UNSIGNED COMMENT 'Tamaño del archivo en KB',
  `origen` ENUM('whatsapp', 'formulario_web', 'manual', 'email') DEFAULT 'formulario_web' COMMENT 'De dónde proviene el documento',
  `whatsapp_media_id` VARCHAR(100) COMMENT 'ID del archivo en WhatsApp Business API',
  `verificado` BOOLEAN DEFAULT FALSE COMMENT 'Si el documento fue verificado por un usuario',
    `idusuario_verificacion` INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que verificó el documento',
  `fecha_verificacion` DATETIME COMMENT 'Fecha de verificación',
  `observaciones` TEXT COMMENT 'Observaciones sobre el documento',
    `idusuario_registro` INT UNSIGNED DEFAULT NULL COMMENT 'Usuario que subió el documento',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `inactive_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete - Fecha de inactivación',
  `iduser_inactive` INT UNSIGNED COMMENT 'Usuario que inactivó el registro',
  PRIMARY KEY (`iddocumento`),
  KEY `idx_doc_lead` (`idlead`),
  KEY `idx_doc_persona` (`idpersona`),
  KEY `idx_doc_tipo` (`tipo_documento`),
  KEY `idx_doc_verificado` (`verificado`),
  CONSTRAINT `fk_doc_lead` 
    FOREIGN KEY (`idlead`) 
    REFERENCES `leads` (`idlead`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_doc_persona` 
    FOREIGN KEY (`idpersona`) 
    REFERENCES `personas` (`idpersona`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_doc_usuario_registro` 
    FOREIGN KEY (`idusuario_registro`) 
    REFERENCES `usuarios` (`idusuario`) 
    ON DELETE SET NULL,
  CONSTRAINT `fk_doc_usuario_verificacion` 
    FOREIGN KEY (`idusuario_verificacion`) 
    REFERENCES `usuarios` (`idusuario`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- 2. AGREGAR CAMPOS PARA WHATSAPP EN TABLA PERSONAS
-- =====================================================
ALTER TABLE `personas` 
ADD COLUMN IF NOT EXISTS `whatsapp` VARCHAR(20) COMMENT 'Número de WhatsApp (puede ser diferente al teléfono)' AFTER `telefono`,
ADD COLUMN IF NOT EXISTS `whatsapp_chat_id` VARCHAR(100) COMMENT 'ID del chat de WhatsApp Business' AFTER `whatsapp`,
ADD COLUMN IF NOT EXISTS `whatsapp_timestamp` TIMESTAMP NULL COMMENT 'Fecha del primer contacto por WhatsApp' AFTER `whatsapp_chat_id`,
ADD COLUMN IF NOT EXISTS `foto_dni_frontal` VARCHAR(255) COMMENT 'Ruta de foto DNI frontal' AFTER `dni`,
ADD COLUMN IF NOT EXISTS `foto_dni_reverso` VARCHAR(255) COMMENT 'Ruta de foto DNI reverso' AFTER `foto_dni_frontal`,
ADD COLUMN IF NOT EXISTS `dni_verificado` BOOLEAN DEFAULT FALSE COMMENT 'Si el DNI fue verificado' AFTER `foto_dni_reverso`;

-- =====================================================
-- 3. AGREGAR CAMPOS PARA UBICACIÓN Y COBERTURA EN LEADS
-- =====================================================
ALTER TABLE `leads`
ADD COLUMN IF NOT EXISTS `ubicacion_compartida` VARCHAR(500) COMMENT 'URL de ubicación compartida por WhatsApp' AFTER `coordenadas_servicio`,
ADD COLUMN IF NOT EXISTS `coordenadas_whatsapp` VARCHAR(100) COMMENT 'Coordenadas GPS desde WhatsApp (lat,lng)' AFTER `ubicacion_compartida`,
ADD COLUMN IF NOT EXISTS `tiene_cobertura` BOOLEAN DEFAULT NULL COMMENT 'Si la dirección tiene cobertura de servicio' AFTER `zona_servicio`,
ADD COLUMN IF NOT EXISTS `fecha_verificacion_cobertura` DATETIME COMMENT 'Cuándo se verificó la cobertura' AFTER `tiene_cobertura`,
ADD COLUMN IF NOT EXISTS `observaciones_cobertura` TEXT COMMENT 'Detalles sobre la cobertura' AFTER `fecha_verificacion_cobertura`,
ADD COLUMN IF NOT EXISTS `recibo_luz_agua` VARCHAR(255) COMMENT 'Ruta de foto del recibo de luz o agua' AFTER `direccion_servicio`,
ADD COLUMN IF NOT EXISTS `recibo_verificado` BOOLEAN DEFAULT FALSE COMMENT 'Si el recibo fue verificado' AFTER `recibo_luz_agua`;

-- =====================================================
-- 4. CREAR ÍNDICES ADICIONALES PARA BÚSQUEDAS
-- =====================================================
ALTER TABLE `personas` 
ADD INDEX IF NOT EXISTS `idx_persona_whatsapp` (`whatsapp`),
ADD INDEX IF NOT EXISTS `idx_persona_dni_verificado` (`dni_verificado`);

ALTER TABLE `leads`
ADD INDEX IF NOT EXISTS `idx_lead_cobertura` (`tiene_cobertura`),
ADD INDEX IF NOT EXISTS `idx_lead_coordenadas_whatsapp` (`coordenadas_whatsapp`);

-- =====================================================
-- 5. VISTA PARA LEADS CON DOCUMENTOS
-- =====================================================
CREATE OR REPLACE VIEW `v_leads_con_documentos` AS
SELECT 
    l.idlead,
    l.idpersona,
    CONCAT(p.nombres, ' ', p.apellidos) as cliente_nombre,
    p.telefono,
    p.whatsapp,
    p.dni,
    p.dni_verificado,
    l.direccion_servicio,
    l.tiene_cobertura,
    l.estado,
    e.nombre as etapa,
    -- Contar documentos por tipo
    COUNT(DISTINCT CASE WHEN d.tipo_documento IN ('dni_frontal', 'dni_reverso') THEN d.iddocumento END) as docs_dni,
    COUNT(DISTINCT CASE WHEN d.tipo_documento IN ('recibo_luz', 'recibo_agua') THEN d.iddocumento END) as docs_recibo,
    COUNT(DISTINCT CASE WHEN d.verificado = TRUE THEN d.iddocumento END) as docs_verificados,
    COUNT(DISTINCT d.iddocumento) as total_documentos,
    -- Estado de documentación
    CASE 
        WHEN COUNT(DISTINCT CASE WHEN d.tipo_documento IN ('dni_frontal', 'dni_reverso') THEN d.iddocumento END) >= 2 
         AND COUNT(DISTINCT CASE WHEN d.tipo_documento IN ('recibo_luz', 'recibo_agua') THEN d.iddocumento END) >= 1
        THEN 'completo'
        WHEN COUNT(DISTINCT d.iddocumento) > 0 THEN 'incompleto'
        ELSE 'sin_documentos'
    END as estado_documentacion,
    l.created_at
FROM leads l
INNER JOIN personas p ON l.idpersona = p.idpersona
INNER JOIN etapas e ON l.idetapa = e.idetapa
LEFT JOIN documentos_lead d ON l.idlead = d.idlead AND d.inactive_at IS NULL
WHERE l.deleted_at IS NULL
GROUP BY l.idlead, l.idpersona, p.nombres, p.apellidos, p.telefono, p.whatsapp, 
         p.dni, p.dni_verificado, l.direccion_servicio, l.tiene_cobertura, 
         l.estado, e.nombre, l.created_at;

-- =====================================================
-- 6. PROCEDIMIENTO PARA VERIFICAR CLIENTE EXISTENTE
-- =====================================================
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_verificar_cliente_existente`(
    IN p_telefono VARCHAR(20),
    IN p_dni CHAR(8)
)
BEGIN
    -- Buscar persona por teléfono o DNI
    SELECT 
        p.idpersona,
        p.dni,
        p.nombres,
        p.apellidos,
        p.telefono,
        p.whatsapp,
        p.correo,
        p.direccion,
        p.dni_verificado,
        -- Verificar si ya es lead
        l.idlead,
        l.estado as estado_lead,
        e.nombre as etapa_actual,
        -- Verificar si ya es cliente
        c.idcliente,
        c.estado as estado_cliente,
        -- Contar leads activos
        COUNT(DISTINCT l2.idlead) as total_leads,
        -- Última interacción
        MAX(l.updated_at) as ultima_interaccion
    FROM personas p
    LEFT JOIN leads l ON p.idpersona = l.idpersona AND l.deleted_at IS NULL
    LEFT JOIN etapas e ON l.idetapa = e.idetapa
    LEFT JOIN clientes c ON p.idpersona = c.idpersona AND c.deleted_at IS NULL
    LEFT JOIN leads l2 ON p.idpersona = l2.idpersona AND l2.estado = 'activo' AND l2.deleted_at IS NULL
    WHERE 
        (p.telefono = p_telefono OR p.whatsapp = p_telefono OR p.dni = p_dni)
        AND p.deleted_at IS NULL
    GROUP BY p.idpersona, p.dni, p.nombres, p.apellidos, p.telefono, p.whatsapp, 
             p.correo, p.direccion, p.dni_verificado, l.idlead, l.estado, 
             e.nombre, c.idcliente, c.estado
    ORDER BY p.created_at DESC
    LIMIT 1;
END$$

DELIMITER ;

-- =====================================================
-- 7. FUNCIÓN PARA VERIFICAR COBERTURA POR COORDENADAS
-- =====================================================
DELIMITER $$

CREATE FUNCTION IF NOT EXISTS `fn_verificar_cobertura_coordenadas`(
    p_lat DECIMAL(10,7),
    p_lng DECIMAL(10,7)
) RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE tiene_cobertura BOOLEAN DEFAULT FALSE;
    
    -- Verificar si las coordenadas están dentro de alguna zona activa
    SELECT COUNT(*) > 0 INTO tiene_cobertura
    FROM tb_zonas_campana z
    WHERE z.estado = 'activa'
    AND ST_Contains(
        ST_GeomFromGeoJSON(z.poligono),
        ST_GeomFromText(CONCAT('POINT(', p_lng, ' ', p_lat, ')'))
    );
    
    RETURN tiene_cobertura;
END$$

DELIMITER ;

-- =====================================================
-- 8. INSERTAR MODALIDAD WHATSAPP SI NO EXISTE
-- =====================================================
INSERT INTO `modalidades` (`nombre`, `icono`, `estado`)
SELECT 'WhatsApp', 'icon-social-whatsapp', 'activo'
WHERE NOT EXISTS (
    SELECT 1 FROM `modalidades` WHERE `nombre` = 'WhatsApp'
);

-- =====================================================
-- 9. INSERTAR ORIGEN WHATSAPP SI NO EXISTE
-- =====================================================
INSERT INTO `origenes` (`nombre`, `descripcion`, `estado`)
SELECT 'WhatsApp Business', 'Cliente contactó directamente por WhatsApp', 'activo'
WHERE NOT EXISTS (
    SELECT 1 FROM `origenes` WHERE `nombre` = 'WhatsApp Business'
);

-- =====================================================
-- 10. CREAR DIRECTORIO PARA ALMACENAR DOCUMENTOS
-- =====================================================
-- NOTA: Esto debe ejecutarse desde PHP o manualmente en el servidor
-- mkdir -p public/uploads/documentos/dni
-- mkdir -p public/uploads/documentos/recibos
-- mkdir -p public/uploads/documentos/otros
-- chmod 755 public/uploads/documentos -R

-- =====================================================
-- PROCEDIMIENTO: CONVERTIR LEAD A CLIENTE
-- =====================================================

DELIMITER $$

CREATE PROCEDURE `spu_lead_convertir_cliente` (
    IN `p_idlead` INT,
    IN `p_id_paquete` INT,
    IN `p_id_sector` INT,
    IN `p_fecha_inicio` DATE,
    IN `p_nota_adicional` TEXT,
    IN `p_id_responsable` INT,
    OUT `p_id_contrato` INT,
    OUT `p_mensaje` VARCHAR(255)
)
BEGIN
    -- =====================================================
    -- PROCEDIMIENTO: Convertir Lead a Cliente
    -- Base de datos origen: delafiber (leads)
    -- Base de datos destino: u647805867_delatelgestion (gestión)
    -- =====================================================
    DECLARE v_idpersona INT;
    DECLARE v_id_persona_gestion INT DEFAULT NULL;
    DECLARE v_id_cliente_gestion INT DEFAULT NULL;
    DECLARE v_nombres VARCHAR(100);
    DECLARE v_apellidos VARCHAR(100);
    DECLARE v_dni VARCHAR(15);
    DECLARE v_telefono VARCHAR(20);
    DECLARE v_correo VARCHAR(100);
    DECLARE v_direccion VARCHAR(250);
    DECLARE v_referencias VARCHAR(150);
    DECLARE v_coordenadas VARCHAR(100);
    DECLARE v_nota_lead TEXT;
    
    -- Variables ajustadas para tb_personas (límites de 30 caracteres)
    DECLARE v_nombres_corto VARCHAR(30);
    DECLARE v_apellidos_corto VARCHAR(30);
    DECLARE v_telefono_corto CHAR(9);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al convertir lead a cliente';
        SET p_id_contrato = NULL;
    END;
    
    START TRANSACTION;
    
    -- 1. Obtener datos del lead (BD: delafiber)
    SELECT 
        l.idpersona,
        p.nombres,
        p.apellidos,
        p.dni,
        p.telefono,
        p.correo,
        p.direccion,
        l.referencias,
        COALESCE(l.coordenadas_servicio, l.coordenadas_whatsapp) as coordenadas,
        l.nota_inicial
    INTO 
        v_idpersona,
        v_nombres,
        v_apellidos,
        v_dni,
        v_telefono,
        v_correo,
        v_direccion,
        v_referencias,
        v_coordenadas,
        v_nota_lead
    FROM delafiber.leads l
    INNER JOIN delafiber.personas p ON l.idpersona = p.idpersona
    WHERE l.idlead = p_idlead
    AND l.estado = 'activo'
    AND l.deleted_at IS NULL;
    
    IF v_idpersona IS NULL THEN
        SET p_mensaje = 'Lead no encontrado o inactivo';
        ROLLBACK;
    ELSE
        -- Ajustar datos a los límites de tb_personas
        SET v_nombres_corto = LEFT(v_nombres, 30);
        SET v_apellidos_corto = LEFT(v_apellidos, 30);
        SET v_telefono_corto = RIGHT(CONCAT('000000000', v_telefono), 9);
        
        -- Validar que el sector no sea NULL
        IF p_id_sector IS NULL OR p_id_sector = 0 THEN
            SET p_mensaje = 'El sector es obligatorio para crear el contrato';
            ROLLBACK;
        ELSE
            -- 2. Buscar en tb_personas (BD: Delatel)
            SELECT id_persona 
            INTO v_id_persona_gestion
            FROM Delatel.tb_personas 
            WHERE nro_doc = v_dni
            AND tipo_doc = 'DNI'
            AND inactive_at IS NULL
            LIMIT 1;
            
            -- 3. Si no existe, crear en tb_personas
            IF v_id_persona_gestion IS NULL THEN
                INSERT INTO Delatel.tb_personas (
                    tipo_doc,
                    nro_doc,
                    apellidos,
                    nombres,
                    telefono,
                    nacionalidad,
                    email,
                    iduser_create,
                    create_at
                )
                VALUES (
                    'DNI',
                    v_dni,
                    v_apellidos_corto,
                    v_nombres_corto,
                    v_telefono_corto,
                    'Peruano',
                    v_correo,
                    p_id_responsable,
                    NOW()
                );
                
                SET v_id_persona_gestion = LAST_INSERT_ID();
            END IF;
            
            -- 4. Buscar en tb_clientes
            SELECT id_cliente 
            INTO v_id_cliente_gestion
            FROM Delatel.tb_clientes 
            WHERE id_persona = v_id_persona_gestion
            AND inactive_at IS NULL
            LIMIT 1;
            
            -- 5. Si no existe, crear en tb_clientes
            IF v_id_cliente_gestion IS NULL THEN
                INSERT INTO Delatel.tb_clientes (
                    id_persona,
                    direccion,
                    referencia,
                    coordenadas,
                    iduser_create,
                    create_at
                )
                VALUES (
                    v_id_persona_gestion,
                    v_direccion,
                    v_referencias,
                    v_coordenadas,
                    p_id_responsable,
                    NOW()
                );
                
                SET v_id_cliente_gestion = LAST_INSERT_ID();
            END IF;
            
            -- 6. Crear contrato en tb_contratos
            INSERT INTO Delatel.tb_contratos (
                id_cliente,
                id_paquete,
                id_sector,
                direccion_servicio,
                referencia,
                coordenada,
                fecha_inicio,
                fecha_registro,
                nota,
                ficha_instalacion,
                id_usuario_registro
            )
            VALUES (
                v_id_cliente_gestion,
                p_id_paquete,
                p_id_sector,
                v_direccion,
                v_referencias,
                v_coordenadas,
                p_fecha_inicio,
                CURDATE(),
                CONCAT(
                    'Lead convertido - ID: ', p_idlead, '\n',
                    'Nota inicial: ', COALESCE(v_nota_lead, ''), '\n',
                    COALESCE(p_nota_adicional, '')
                ),
                '{}',
                p_id_responsable
            );
            
            SET p_id_contrato = LAST_INSERT_ID();
            
            -- 7. Actualizar lead como convertido (BD: delafiber)
            UPDATE delafiber.leads 
            SET idetapa = 6,
                estado = 'convertido',
                fecha_conversion = NOW()
            WHERE idlead = p_idlead;
            
            -- 8. Registrar en historial (BD: delafiber)
            INSERT INTO delafiber.historial_leads (
                idlead,
                idusuario,
                idetapa_anterior,
                idetapa_nueva,
                observacion
            )
            SELECT 
                p_idlead,
                p_id_responsable,
                idetapa,
                6,
                CONCAT('Lead convertido a cliente. Contrato ID: ', p_id_contrato)
            FROM delafiber.leads
            WHERE idlead = p_idlead;
            
            SET p_mensaje = 'Lead convertido exitosamente';
            
            COMMIT;
        END IF;
    END IF;
    
END$$

DELIMITER ;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
