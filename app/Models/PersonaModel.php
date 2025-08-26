<?php
namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $allowedFields = [
        'apellidos', 'nombres', 'telprimario', 'telalternativo',
        'email', 'direccion', 'referencia', 'iddistrito',
        'creado', 'modificado'
    ];

    protected $useTimestamps = false;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Registrar persona
    public function registrar($data)
    {
        $this->db->query(
            "CALL sp_registrar_persona(?, ?, ?, ?, ?, ?, ?, ?, @nuevo_id)",
            [
                $data['apellidos'],
                $data['nombres'],
                $data['telprimario'],
                $data['telalternativo'] ?? null,
                $data['email'] ?? null,
                $data['direccion'] ?? null,
                $data['referencia'] ?? null,
                $data['iddistrito']
            ]
        );

        $result = $this->db->query("SELECT @nuevo_id AS nuevo_id")->getRow();
        return $result->nuevo_id ?? null;
    }

    // Actualizar persona
    public function actualizar($idpersona, $data)
    {
        return $this->db->query(
            "CALL sp_actualizar_persona(?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $idpersona,
                $data['apellidos'],
                $data['nombres'],
                $data['telprimario'],
                $data['telalternativo'] ?? null,
                $data['email'] ?? null,
                $data['direccion'] ?? null,
                $data['referencia'] ?? null,
                $data['iddistrito']
            ]
        );
    }

    // Eliminar persona
    public function eliminar($idpersona)
    {
        return $this->db->query("CALL sp_eliminar_persona(?)", [$idpersona]);
    }

    // Listar personas
    public function listar()
    {
        $query = $this->db->query("CALL sp_listar_personas()");
        return $query->getResultArray();
    }

    // Obtener persona por ID
    public function obtener($idpersona)
    {
        $query = $this->db->query("CALL sp_obtener_persona_por_id(?)", [$idpersona]);
        return $query->getRowArray();
    }

    /* ---------------------------
       Alias para que funcione con PersonaController
    --------------------------- */
    public function listarPersonasSP()
    {
        return $this->listar();
    }

    public function actualizarPersona($id, $data)
    {
        return $this->actualizar($id, $data);
    }

    public function eliminarPersona($id)
    {
        return $this->eliminar($id);
    }
}
