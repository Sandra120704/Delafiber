<?php

namespace App\Models;

use CodeIgniter\Model;

class CotizacionModel extends Model
{
    protected $table = 'cotizaciones';
    protected $primaryKey = 'idcotizacion';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'idlead',
        'idservicio',
        'precio_cotizado',
        'descuento_aplicado',
        'precio_instalacion',
        'vigencia_dias',
        'estado',
        'observaciones',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'idlead' => 'required|integer',
        'idservicio' => 'required|integer',
        'precio_cotizado' => 'required|decimal',
        'vigencia_dias' => 'integer'
    ];
    protected $validationMessages = [
        'idlead' => [
            'required' => 'El lead es obligatorio'
        ],
        'idservicio' => [
            'required' => 'El servicio es obligatorio'
        ],
        'precio_cotizado' => [
            'required' => 'El precio es obligatorio'
        ]
    ];
    protected $skipValidation = false;

    /**
     * Obtener cotizaciones de un lead con información del servicio
     */
    public function getCotizacionesByLead($idlead)
    {
        return $this->select('cotizaciones.*, servicios_catalogo.nombre as servicio_nombre, 
                             servicios_catalogo.velocidad, servicios_catalogo.descripcion as servicio_descripcion')
            ->join('servicios_catalogo', 'servicios_catalogo.idservicio = cotizaciones.idservicio')
            ->where('cotizaciones.idlead', $idlead)
            ->orderBy('cotizaciones.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener cotización completa con todos los detalles
     */
    public function getCotizacionCompleta($idcotizacion)
    {
        return $this->select('cotizaciones.*, 
                             servicios_catalogo.nombre as servicio_nombre,
                             servicios_catalogo.velocidad,
                             servicios_catalogo.descripcion as servicio_descripcion,
                             CONCAT(personas.nombres, " ", personas.apellidos) as cliente_nombre,
                             personas.correo as cliente_correo,
                             personas.telefono as cliente_telefono')
            ->join('servicios_catalogo', 'servicios_catalogo.idservicio = cotizaciones.idservicio')
            ->join('leads', 'leads.idlead = cotizaciones.idlead')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->where('cotizaciones.idcotizacion', $idcotizacion)
            ->first();
    }

    /**
     * Crear nueva cotización
     */
    public function crearCotizacion($data)
    {
        // Calcular precio final si hay descuento
        if (isset($data['descuento_aplicado']) && $data['descuento_aplicado'] > 0) {
            $precioBase = $data['precio_cotizado'];
            $descuento = $data['descuento_aplicado'];
            $data['precio_cotizado'] = $precioBase - ($precioBase * ($descuento / 100));
        }

        return $this->insert($data);
    }

    /**
     * Cambiar estado de cotización
     */
    public function cambiarEstado($idcotizacion, $nuevoEstado)
    {
        $estadosValidos = ['vigente', 'vencida', 'aceptada', 'rechazada'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return false;
        }

        return $this->update($idcotizacion, ['estado' => $nuevoEstado]);
    }

    /**
     * Verificar y actualizar cotizaciones vencidas
     */
    public function actualizarCotizacionesVencidas()
    {
        $hoy = date('Y-m-d H:i:s');
        
        return $this->where('estado', 'vigente')
            ->where("DATE_ADD(created_at, INTERVAL vigencia_dias DAY) <", $hoy)
            ->set(['estado' => 'vencida'])
            ->update();
    }

    /**
     * Obtener cotizaciones vigentes de un lead
     */
    public function getCotizacionesVigentes($idlead)
    {
        $hoy = date('Y-m-d H:i:s');
        
        return $this->select('cotizaciones.*, servicios_catalogo.nombre as servicio_nombre')
            ->join('servicios_catalogo', 'servicios_catalogo.idservicio = cotizaciones.idservicio')
            ->where('cotizaciones.idlead', $idlead)
            ->where('cotizaciones.estado', 'vigente')
            ->where("DATE_ADD(cotizaciones.created_at, INTERVAL cotizaciones.vigencia_dias DAY) >=", $hoy)
            ->findAll();
    }

    /**
     * Obtener estadísticas de cotizaciones
     */
    public function getEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $builder = $this->builder();
        
        if ($fechaInicio && $fechaFin) {
            $builder->where('created_at >=', $fechaInicio)
                   ->where('created_at <=', $fechaFin);
        }

        return $builder->select('
            COUNT(*) as total_cotizaciones,
            SUM(CASE WHEN estado = "vigente" THEN 1 ELSE 0 END) as vigentes,
            SUM(CASE WHEN estado = "aceptada" THEN 1 ELSE 0 END) as aceptadas,
            SUM(CASE WHEN estado = "rechazada" THEN 1 ELSE 0 END) as rechazadas,
            SUM(CASE WHEN estado = "vencida" THEN 1 ELSE 0 END) as vencidas,
            AVG(precio_cotizado) as precio_promedio,
            SUM(CASE WHEN estado = "aceptada" THEN precio_cotizado ELSE 0 END) as valor_aceptado
        ')
        ->get()
        ->getRowArray();
    }

    /**
     * Obtener tasa de conversión de cotizaciones
     */
    public function getTasaConversion($periodo = 30)
    {
        $fechaInicio = date('Y-m-d', strtotime("-{$periodo} days"));
        
        $resultado = $this->select('
            COUNT(*) as total,
            SUM(CASE WHEN estado = "aceptada" THEN 1 ELSE 0 END) as aceptadas,
            ROUND((SUM(CASE WHEN estado = "aceptada" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as tasa_conversion
        ')
        ->where('created_at >=', $fechaInicio)
        ->first();

        return $resultado;
    }

    /**
     * Obtener servicios más cotizados
     */
    public function getServiciosMasCotizados($limit = 5)
    {
        return $this->select('
            servicios_catalogo.nombre,
            servicios_catalogo.velocidad,
            COUNT(cotizaciones.idcotizacion) as total_cotizaciones,
            SUM(CASE WHEN cotizaciones.estado = "aceptada" THEN 1 ELSE 0 END) as aceptadas,
            AVG(cotizaciones.precio_cotizado) as precio_promedio
        ')
        ->join('servicios_catalogo', 'servicios_catalogo.idservicio = cotizaciones.idservicio')
        ->groupBy('cotizaciones.idservicio')
        ->orderBy('total_cotizaciones', 'DESC')
        ->limit($limit)
        ->findAll();
    }

    /**
     * Duplicar cotización (para revisiones)
     */
    public function duplicarCotizacion($idcotizacion)
    {
        $cotizacion = $this->find($idcotizacion);
        
        if (!$cotizacion) {
            return false;
        }

        // Remover el ID y actualizar estado
        unset($cotizacion['idcotizacion']);
        $cotizacion['estado'] = 'vigente';
        $cotizacion['created_at'] = date('Y-m-d H:i:s');

        return $this->insert($cotizacion);
    }
}