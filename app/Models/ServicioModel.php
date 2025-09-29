<?php
namespace App\Models;
use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table = 'servicios_catalogo';
    protected $primaryKey = 'idservicio';
    protected $allowedFields = [
        'nombre',
        'descripcion',
        'velocidad',
        'precio_referencial',
        'precio_instalacion',
        'activo'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Obtener servicios activos ordenados por precio
     */
    public function getServiciosActivos()
    {
        return $this->where('activo', 1)
            ->orderBy('precio_referencial', 'ASC')
            ->findAll();
    }

    /**
     * Obtener servicios con estadísticas de cotizaciones
     */
    public function getServiciosConEstadisticas()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s');
        $builder->select('
            s.*,
            COUNT(c.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN c.estado = "aceptada" THEN 1 ELSE 0 END) as cotizaciones_aceptadas,
            AVG(c.precio_cotizado) as precio_promedio_cotizado
        ');
        $builder->join('cotizaciones c', 's.idservicio = c.idservicio', 'left');
        $builder->where('s.activo', 1);
        $builder->groupBy('s.idservicio');
        $builder->orderBy('s.precio_referencial', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener servicios más cotizados
     */
    public function getServiciosMasCotizados($limit = 5)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s');
        $builder->select('s.*, COUNT(c.idcotizacion) as total_cotizaciones')
            ->join('cotizaciones c', 's.idservicio = c.idservicio', 'left')
            ->where('s.activo', 1)
            ->groupBy('s.idservicio')
            ->orderBy('total_cotizaciones', 'DESC')
            ->limit($limit);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener servicio con mejor tasa de conversión
     */
    public function getServiciosConMejorConversion()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s');
        $builder->select('
            s.*,
            COUNT(c.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN c.estado = "aceptada" THEN 1 ELSE 0 END) as aceptadas,
            ROUND((SUM(CASE WHEN c.estado = "aceptada" THEN 1 ELSE 0 END) / COUNT(c.idcotizacion)) * 100, 2) as tasa_conversion
        ');
        $builder->join('cotizaciones c', 's.idservicio = c.idservicio', 'left');
        $builder->where('s.activo', 1);
        $builder->groupBy('s.idservicio');
        $builder->having('total_cotizaciones >', 0);
        $builder->orderBy('tasa_conversion', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Buscar servicio por velocidad
     */
    public function buscarPorVelocidad($velocidad)
    {
        return $this->where('activo', 1)
            ->like('velocidad', $velocidad)
            ->findAll();
    }

    /**
     * Obtener servicios más utilizados por cantidad de cotizaciones aceptadas
     */
    public function getServiciosMasUtilizados($limit = 5)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s');
        $builder->select('
            s.*,
            COUNT(c.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN c.estado = "aceptada" THEN 1 ELSE 0 END) as cotizaciones_aceptadas
        ');
        $builder->join('cotizaciones c', 's.idservicio = c.idservicio', 'left');
        $builder->where('s.activo', 1);
        $builder->groupBy('s.idservicio');
        $builder->orderBy('cotizaciones_aceptadas', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }
}