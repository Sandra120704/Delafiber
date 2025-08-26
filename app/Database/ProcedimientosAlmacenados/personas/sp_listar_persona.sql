DELIMITER $$

CREATE PROCEDURE sp_listar_personas ()
BEGIN
  SELECT 
    idpersona,
    CONCAT(nombres, ' ', apellidos) AS nombre_completo,
    email,
    telprimario,
    telsecundario,
    direccion,
    distrito,
    creado,
    modificado
  FROM personas;
END $$

DELIMITER ;
