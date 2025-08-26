DELIMITER $$

CREATE PROCEDURE sp_listar_leads(
    IN p_idusuario INT -- Opcional: si envías NULL lista todos, si envías un ID filtra
)
BEGIN 
  SELECT 
    l.idlead,
    CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
    p.email,
    p.telprimario,
    l.fechasignacion,
    e.nombreetapa AS etapa,        -- Etapa del pipeline
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
  JOIN etapas e ON l.idetapa = e.idetapa
  WHERE (p_idusuario IS NULL OR l.idusuarioresponsable = p_idusuario)
  ORDER BY l.fechasignacion DESC;
END $$

DELIMITER ;

CALL sp_listar_leads(NULL);
CALL sp_listar_leads(2);

