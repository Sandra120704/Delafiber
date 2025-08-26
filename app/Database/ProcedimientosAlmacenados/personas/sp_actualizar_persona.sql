DELIMITER $$
DROP PROCEDURE IF EXISTS sp_actualizar_persona $$
CREATE PROCEDURE sp_actualizar_persona (
  IN p_idpersona      INT,
  IN p_apellidos      VARCHAR(100),
  IN p_nombres        VARCHAR(100),
  IN p_telprimario    VARCHAR(20),
  IN p_telalternativo VARCHAR(20),
  IN p_email          VARCHAR(100),
  IN p_direccion      VARCHAR(200),
  IN p_referencia     VARCHAR(200),
  IN p_iddistrito     INT
)
BEGIN
  UPDATE personas
  SET apellidos      = p_apellidos,
      nombres        = p_nombres,
      telprimario    = p_telprimario,
      telalternativo = p_telalternativo,
      email          = p_email,
      direccion      = p_direccion,
      referencia     = p_referencia,
      iddistrito     = p_iddistrito,
      modificado     = NOW()
  WHERE idpersona = p_idpersona;
END $$
DELIMITER ;
