<?php
namespace App\Models;
use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'idservicio';
    protected $allowedFields = [
        'nombre',
        'descripcion',
        'categoria',
        'precio',
        'estado'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Obtener servicios activos ordenados por precio
     */
    public function getServiciosActivos()
    {
        return $this->where('estado', 'Activo')
            ->orderBy('precio', 'ASC')
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
            SUM(CASE WHEN c.estado = "Aceptada" THEN 1 ELSE 0 END) as cotizaciones_aceptadas,
            AVG(cd.precio_unitario) as precio_promedio_cotizado
        ');
        $builder->join('cotizacion_detalle cd', 's.idservicio = cd.idservicio', 'left');
        $builder->join('cotizaciones c', 'cd.idcotizacion = c.idcotizacion', 'left');
        $builder->where('s.estado', 'Activo');
        $builder->groupBy('s.idservicio');
        $builder->orderBy('s.precio', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener servicios más cotizados
     */
    public function getServiciosMasCotizados($limit = 5)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' s');
        $builder->select('s.*, COUNT(DISTINCT c.idcotizacion) as total_cotizaciones')
            ->join('cotizacion_detalle cd', 's.idservicio = cd.idservicio', 'left')
            ->join('cotizaciones c', 'cd.idcotizacion = c.idcotizacion', 'left')
            ->where('s.estado', 'Activo')
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
            COUNT(DISTINCT c.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN c.estado = "Aceptada" THEN 1 ELSE 0 END) as aceptadas,
            ROUND((SUM(CASE WHEN c.estado = "Aceptada" THEN 1 ELSE 0 END) / COUNT(DISTINCT c.idcotizacion)) * 100, 2) as tasa_conversion
        ');
        $builder->join('cotizacion_detalle cd', 's.idservicio = cd.idservicio', 'left');
        $builder->join('cotizaciones c', 'cd.idcotizacion = c.idcotizacion', 'left');
        $builder->where('s.estado', 'Activo');
        $builder->groupBy('s.idservicio');
        $builder->having('total_cotizaciones >', 0);
        $builder->orderBy('tasa_conversion', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Buscar servicio por velocidad
     */
    public function buscarPorCategoria($categoria)
    {
        return $this->where('estado', 'Activo')
            ->where('categoria', $categoria)
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
            COUNT(DISTINCT c.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN c.estado = "Aceptada" THEN 1 ELSE 0 END) as cotizaciones_aceptadas
        ');
        $builder->join('cotizacion_detalle cd', 's.idservicio = cd.idservicio', 'left');
        $builder->join('cotizaciones c', 'cd.idcotizacion = c.idcotizacion', 'left');
        $builder->where('s.estado', 'Activo');
        $builder->groupBy('s.idservicio');
        $builder->orderBy('cotizaciones_aceptadas', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }
}