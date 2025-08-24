DELIMITER $$

CREATE PROCEDURE sp_obtener_lead_por_id (
  IN p_idlead INT
)
BEGIN
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
  JOIN medios m ON d.idmedio = m.idmedio
  WHERE l.idlead = p_idlead;
END $$

DELIMITER ;
