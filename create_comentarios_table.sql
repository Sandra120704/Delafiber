USE delafiber;

CREATE TABLE IF NOT EXISTS `comentarios_lead` (
  `idcomentario` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `idlead` INT(11) UNSIGNED NOT NULL COMMENT 'Lead al que pertenece el comentario',
  `idusuario` INT(11) UNSIGNED NOT NULL COMMENT 'Usuario que escribió el comentario',
  `comentario` TEXT NOT NULL COMMENT 'Contenido del comentario',
  `tipo` ENUM('nota_interna', 'solicitud_apoyo', 'respuesta') DEFAULT 'nota_interna' COMMENT 'Tipo de comentario',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  INDEX `idx_idlead` (`idlead`),
  INDEX `idx_idusuario` (`idusuario`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`idlead`) REFERENCES `leads`(`idlead`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`idusuario`) REFERENCES `usuarios`(`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
