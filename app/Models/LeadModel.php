<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function contarLeads()
    {
        $query = $this->db->query("SELECT COUNT(*) as total FROM leads");
        return $query->getRow()->total ?? 0;
    }

    public function contarLeadsPorEstado()
    {
        $query = $this->db->query("
            SELECT estado, COUNT(*) as cantidad 
            FROM leads 
            GROUP BY estado
        ")->getResultArray();

        $result = [
            'nuevo' => 0,
            'contactado' => 0,
            'interesado' => 0,
            'no interesado' => 0,
            'perdido' => 0
        ];

        foreach ($query as $row) {
            $result[$row['estado']] = $row['cantidad'];
        }

        return array_values($result);
    }
}
