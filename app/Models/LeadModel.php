<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'iddifusion', 'idpersona', 'idusuarioregistro', 'idusuarioresponsable',
        'fechasignacion', 'estado', 'fecharegistro', 'creado', 'modificado'
    ];
    protected $useTimestamps = false;

    // Método que llama al SP para registrar un lead
    public function registrarLeadSP($params)
    {
        $db = \Config\Database::connect();

        $db->query("CALL sp_registrar_lead(?, ?, ?, ?, ?, @nuevo_id)", [
            $params['iddifusion'],
            $params['idpersona'],
            $params['idusuarioregistro'],
            $params['idusuarioresponsable'],
            $params['fechasignacion'],
        ]);

        $result = $db->query("SELECT @nuevo_id as idlead")->getRow();
        return $result->idlead ?? null;
    }

    //Actualizacion De Leads
    public function actualizarLead($params)
    {
        $db = \Config\Database::connect();

        $db->query("CALL sp_actualizar_lead(?, ?, ?, ?, ?, ?)", [
            $params['idlead'],
            $params['idusuarioresponsable'],
            $params['fechasignacion'],
            $params['estado'],
        ]);
    }

    //Eliminar Un Leads Por ID
    public function eliminarLead($idlead)
    {
        $db = \Config\Database::connect();

        $db->query("CALL sp_eliminar_lead(?)", [$idlead]);
    }

    //Obtencion de leads por un Id
    public function obtenerLeadPorId($idlead)
    {
        $db = \Config\Database::connect();

        $query = $db->query("CALL sp_obtener_lead_por_id(?)", [$idlead]);
        return $query->getRow();
    }

    //Listar Leads 
    public function listarLeads()
    {
        $db = \Config\Database::connect();

        $query = $db->query("CALL sp_listar_leads()");
        return $query->getResultArray();
    }

  }