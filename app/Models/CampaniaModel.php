<?php 
namespace App\Models;

use CodeIgniter\Model;

class CampaniaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    protected $allowedFields = [
        'nombre', 
        'descripcion', 
        'tipo_campana',
        'fecha_inicio', 
        'fecha_fin', 
        'presupuesto',
        'objetivo_contactos',
        'canal',
        'estado',
        'activo',
        'responsable'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_creacion'; // La tabla no tiene updated_at
    
    /**
     * Obtener campañas con información del responsable
     */
    public function getCampaniasCompletas($filtros = [])
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('
            c.*,
            CONCAT(p.nombres, " ", p.apellidos) as responsable_nombre,
            COUNT(DISTINCT l.idlead) as total_leads,
            COUNT(DISTINCT CASE WHEN l.estado = "Convertido" THEN l.idlead END) as leads_convertidos
        ');
        $builder->join('usuarios u', 'c.responsable = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('leads l', 'c.idcampania = l.idcampania', 'left');
        
        if (!empty($filtros['estado'])) {
            $builder->where('c.estado', $filtros['estado']);
        }
        
        $builder->groupBy('c.idcampania');
        $builder->orderBy('c.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener estadísticas de una campaña
     */
    public function getEstadisticasCampania($idcampania)
    {
        $builder = $this->db->table('leads l');
        $builder->select('
            COUNT(*) as total_leads,
            COUNT(CASE WHEN l.estado = "Convertido" THEN 1 END) as convertidos,
            COUNT(CASE WHEN l.estado = "Descartado" THEN 1 END) as descartados,
            COUNT(CASE WHEN l.estado IS NULL THEN 1 END) as activos
        ');
        $builder->where('l.idcampania', $idcampania);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Obtener campañas activas
     */
    public function getCampaniasActivas()
    {
        return $this->where('estado', 'Activa')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }
}