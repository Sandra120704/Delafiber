<?php

namespace App\Models;

use CodeIgniter\Model;
class DifusionModel extends Model
{
    protected $table = 'difusiones';
    protected $primaryKey = 'iddifusion';
    protected $allowedFields = [
        'idcampania',
        'idmedio',
        'presupuesto',
        'leads_generados'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = null;

    /**
     * Obtener difusiones de una campaña con información del medio
     */
    public function getDifusionesCampania($idcampania)
    {
        $builder = $this->db->table($this->table . ' d');
        $builder->select('
            d.*,
            m.nombre as medio_nombre,
            m.descripcion as medio_descripcion
        ');
        $builder->join('medios m', 'd.idmedio = m.idmedio', 'left');
        $builder->where('d.idcampania', $idcampania);
        $builder->orderBy('d.fecha_creacion', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener estadísticas de una difusión
     */
    public function getEstadisticasDifusion($iddifusion)
    {
        $difusion = $this->find($iddifusion);
        
        if (!$difusion) {
            return null;
        }

        $costo_por_lead = 0;
        if ($difusion['leads_generados'] > 0) {
            $costo_por_lead = $difusion['presupuesto'] / $difusion['leads_generados'];
        }

        return [
            'difusion' => $difusion,
            'costo_por_lead' => $costo_por_lead,
            'roi' => 0 // Puedes calcular ROI según tus métricas
        ];
    }

    /**
     * Incrementar contador de leads generados
     */
    public function incrementarLeads($iddifusion)
    {
        $difusion = $this->find($iddifusion);
        
        if ($difusion) {
            $this->update($iddifusion, [
                'leads_generados' => $difusion['leads_generados'] + 1
            ]);
            return true;
        }
        
        return false;
    }

    /**
     * Obtener resumen por medio
     */
    public function getResumenPorMedio($idcampania = null)
    {
        $builder = $this->db->table($this->table . ' d');
        $builder->select('
            m.nombre as medio,
            COUNT(d.iddifusion) as total_difusiones,
            SUM(d.presupuesto) as presupuesto_total,
            SUM(d.leads_generados) as leads_total
        ');
        $builder->join('medios m', 'd.idmedio = m.idmedio', 'left');
        
        if ($idcampania) {
            $builder->where('d.idcampania', $idcampania);
        }
        
        $builder->groupBy('d.idmedio');
        
        return $builder->get()->getResultArray();
    }
}

/**
 * MODELO: MedioModel
 * Gestión de medios de publicidad
 */
class MedioModel extends Model
{
    protected $table = 'medios';
    protected $primaryKey = 'idmedio';
    protected $allowedFields = ['nombre', 'descripcion', 'activo'];

    /**
     * Obtener todos los medios activos
     */
    public function getMediosActivos()
    {
        return $this->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener medio con estadísticas de uso
     */
    public function getMedioConEstadisticas($idmedio)
    {
        $medio = $this->find($idmedio);
        
        if (!$medio) {
            return null;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('difusiones');
        $builder->select('
            COUNT(*) as total_difusiones,
            SUM(presupuesto) as presupuesto_total,
            SUM(leads_generados) as leads_total
        ');
        $builder->where('idmedio', $idmedio);
        
        $stats = $builder->get()->getRowArray();
        
        return [
            'medio' => $medio,
            'estadisticas' => $stats
        ];
    }

    /**
     * Obtener ranking de medios por efectividad
     */
    public function getRankingMedios()
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table . ' m');
        $builder->select('
            m.*,
            COUNT(d.iddifusion) as total_difusiones,
            SUM(d.presupuesto) as inversion_total,
            SUM(d.leads_generados) as leads_generados,
            CASE 
                WHEN SUM(d.presupuesto) > 0 
                THEN ROUND(SUM(d.leads_generados) / SUM(d.presupuesto), 2)
                ELSE 0 
            END as efectividad
        ');
        $builder->join('difusiones d', 'm.idmedio = d.idmedio', 'left');
        $builder->where('m.activo', 1);
        $builder->groupBy('m.idmedio');
        $builder->orderBy('efectividad', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener medios más utilizados
     */
    public function getMediosMasUtilizados($limit = 5)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $builder->select('medios.*, COUNT(difusiones.iddifusion) as total_uso')
            ->join('difusiones', 'medios.idmedio = difusiones.idmedio', 'left')
            ->where('medios.activo', 1)
            ->groupBy('medios.idmedio')
            ->orderBy('total_uso', 'DESC')
            ->limit($limit);
        
        return $builder->get()->getResultArray();
    }
}

/**
 * MODELO: CotizacionModel
 * Gestión de cotizaciones de servicios a leads
 */
class CotizacionModel extends Model
{
    protected $table = 'cotizaciones';
    protected $primaryKey = 'idcotizacion';
    protected $allowedFields = [
        'idlead',
        'idservicio',
        'precio_cotizado',
        'descuento_aplicado',
        'precio_instalacion',
        'vigencia_dias',
        'estado',
        'observaciones'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Obtener cotizaciones de un lead
     */
    public function getCotizacionesLead($idlead)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('
            c.*,
            s.nombre as servicio_nombre,
            s.velocidad as servicio_velocidad
        ');
        $builder->join('servicios_catalogo s', 'c.idservicio = s.idservicio', 'left');
        $builder->where('c.idlead', $idlead);
        $builder->orderBy('c.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Crear cotización automática basada en servicio
     */
    public function crearCotizacion($data)
    {
        // Obtener información del servicio
        $servicioModel = new \App\Models\ServicioModel();
        $servicio = $servicioModel->find($data['idservicio']);
        
        if (!$servicio) {
            return false;
        }

        // Calcular precio con descuento
        $precio_base = $servicio['precio_referencial'];
        $descuento = $data['descuento_aplicado'] ?? 0;
        $precio_final = $precio_base - ($precio_base * ($descuento / 100));

        $cotizacion = [
            'idlead' => $data['idlead'],
            'idservicio' => $data['idservicio'],
            'precio_cotizado' => $precio_final,
            'descuento_aplicado' => $descuento,
            'precio_instalacion' => $servicio['precio_instalacion'],
            'vigencia_dias' => $data['vigencia_dias'] ?? 30,
            'estado' => 'vigente',
            'observaciones' => $data['observaciones'] ?? null
        ];

        return $this->insert($cotizacion);
    }

    /**
     * Verificar y actualizar estado de cotizaciones vencidas
     */
    public function actualizarCotizacionesVencidas()
    {
        $builder = $this->db->table($this->table);
        $builder->set('estado', 'vencida');
        $builder->where('estado', 'vigente');
        $builder->where('DATE_ADD(created_at, INTERVAL vigencia_dias DAY) <', date('Y-m-d'));
        
        return $builder->update();
    }
}

/**
 * MODELO: ServicioModel
 * Catálogo de servicios de internet
 */
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
     * Obtener servicios activos
     */
    public function getServiciosActivos()
    {
        return $this->where('activo', 1)
            ->orderBy('precio_referencial', 'ASC')
            ->findAll();
    }

    /**
     * Obtener servicios más cotizados
     */
    public function getServiciosMasCotizados($limit = 5)
    {
        $builder = $this->db->table($this->table . ' s');
        $builder->select('
            s.*,
            COUNT(c.idcotizacion) as total_cotizaciones
        ');
        $builder->join('cotizaciones c', 's.idservicio = c.idservicio', 'left');
        $builder->where('s.activo', 1);
        $builder->groupBy('s.idservicio');
        $builder->orderBy('total_cotizaciones', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
}