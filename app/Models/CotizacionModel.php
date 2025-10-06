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
        'idusuario',
        'numero_cotizacion',
        'subtotal',
        'igv',
        'total',
        'observaciones',
        'estado',
        'fecha_envio',
        'fecha_respuesta'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Validation
    protected $validationRules = [
        'idlead' => 'required|integer',
        'idusuario' => 'required|integer',
        'subtotal' => 'required|decimal',
        'total' => 'required|decimal'
    ];
    
    protected $validationMessages = [
        'idlead' => [
            'required' => 'El lead es obligatorio'
        ],
        'idusuario' => [
            'required' => 'El usuario es obligatorio'
        ],
        'total' => [
            'required' => 'El total es obligatorio'
        ]
    ];
    
    protected $skipValidation = false;

    /**
     * Verificar si una tabla existe en la base de datos
     */
    private function tableExists($tableName)
    {
        try {
            return $this->db->tableExists($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si una columna existe en una tabla
     */
    private function columnExists($tableName, $columnName)
    {
        try {
            $fields = $this->db->getFieldNames($tableName);
            return in_array($columnName, $fields);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener cotizaciones completas con filtros
     */
    public function getCotizacionesCompletas($userId = null, $rol = null)
    {
        $builder = $this->db->table($this->table . ' c');
        
        // Verificar si la columna idusuario existe
        $hasUsuarioColumn = false;
        try {
            $fields = $this->db->getFieldNames($this->table);
            $hasUsuarioColumn = in_array('idusuario', $fields);
        } catch (\Exception $e) {
            log_message('warning', 'No se pudo verificar columnas de cotizaciones');
        }
        
        if ($hasUsuarioColumn) {
            $builder->select('
                c.*,
                CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre,
                p.telefono as cliente_telefono,
                u.usuario as usuario_nombre,
                l.idlead
            ');
            $builder->join('leads l', 'c.idlead = l.idlead', 'left');
            $builder->join('personas p', 'l.idpersona = p.idpersona', 'left');
            $builder->join('usuarios u', 'c.idusuario = u.idusuario', 'left');
            
            // Si no es admin, solo mostrar cotizaciones de sus leads
            if ($rol !== 'admin' && $userId) {
                $builder->where('c.idusuario', $userId);
            }
        } else {
            // Sin columna idusuario, filtrar por leads del usuario
            $builder->select('
                c.*,
                CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre,
                p.telefono as cliente_telefono,
                u.usuario as usuario_nombre,
                l.idlead
            ');
            $builder->join('leads l', 'c.idlead = l.idlead', 'left');
            $builder->join('personas p', 'l.idpersona = p.idpersona', 'left');
            $builder->join('usuarios u', 'l.idusuario = u.idusuario', 'left');
            
            // Si no es admin, filtrar por leads del usuario
            if ($rol !== 'admin' && $userId) {
                $builder->where('l.idusuario', $userId);
            }
        }
        
        $builder->orderBy('c.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener cotizaciones por lead
     */
    public function getCotizacionesPorLead($idlead)
    {
        return $this->select('cotizaciones.*')
            ->where('cotizaciones.idlead', $idlead)
            ->orderBy('cotizaciones.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener cotización completa con todos los detalles
     */
    public function getCotizacionCompleta($idcotizacion)
    {
        // Verificar si la columna idusuario existe
        $hasUsuarioColumn = false;
        try {
            $fields = $this->db->getFieldNames($this->table);
            $hasUsuarioColumn = in_array('idusuario', $fields);
        } catch (\Exception $e) {
            log_message('warning', 'No se pudo verificar columnas de cotizaciones');
        }
        
        if ($hasUsuarioColumn) {
            $cotizacion = $this->select('cotizaciones.*, 
                                 CONCAT(personas.nombres, " ", personas.apellidos) as cliente_nombre,
                                 personas.correo as cliente_correo,
                                 personas.telefono as cliente_telefono,
                                 personas.direccion as cliente_direccion,
                                 u.usuario as usuario_nombre')
                ->join('leads', 'leads.idlead = cotizaciones.idlead')
                ->join('personas', 'personas.idpersona = leads.idpersona')
                ->join('usuarios u', 'cotizaciones.idusuario = u.idusuario', 'left')
                ->where('cotizaciones.idcotizacion', $idcotizacion)
                ->first();
        } else {
            $cotizacion = $this->select('cotizaciones.*, 
                                 CONCAT(personas.nombres, " ", personas.apellidos) as cliente_nombre,
                                 personas.correo as cliente_correo,
                                 personas.telefono as cliente_telefono,
                                 personas.direccion as cliente_direccion,
                                 u.usuario as usuario_nombre')
                ->join('leads', 'leads.idlead = cotizaciones.idlead')
                ->join('personas', 'personas.idpersona = leads.idpersona')
                ->join('usuarios u', 'leads.idusuario = u.idusuario', 'left')
                ->where('cotizaciones.idcotizacion', $idcotizacion)
                ->first();
        }
        
        // Obtener detalles de servicios (si las tablas existen)
        if ($cotizacion) {
            try {
                if ($this->tableExists('cotizacion_detalle') && $this->tableExists('servicios')) {
                    $db = \Config\Database::connect();
                    $cotizacion['detalles'] = $db->table('cotizacion_detalle cd')
                        ->select('cd.*, s.nombre as servicio_nombre, s.descripcion as servicio_descripcion')
                        ->join('servicios s', 'cd.idservicio = s.idservicio')
                        ->where('cd.idcotizacion', $idcotizacion)
                        ->get()
                        ->getResultArray();
                } else {
                    $cotizacion['detalles'] = [];
                }
            } catch (\Exception $e) {
                log_message('warning', 'No se pudo obtener detalles de cotización: ' . $e->getMessage());
                $cotizacion['detalles'] = [];
            }
        }
        
        return $cotizacion;
    }

    /**
     * Crear nueva cotización
     */
    public function crearCotizacion($data, $detalles = [])
    {
        // Generar número de cotización
        if (!isset($data['numero_cotizacion'])) {
            $data['numero_cotizacion'] = 'COT-' . date('Y') . '-' . str_pad($this->countAll() + 1, 4, '0', STR_PAD_LEFT);
        }
        
        // Calcular IGV y total si no están definidos
        if (isset($data['subtotal']) && !isset($data['total'])) {
            $data['igv'] = $data['subtotal'] * 0.18;
            $data['total'] = $data['subtotal'] + $data['igv'];
        }

        $idcotizacion = $this->insert($data);
        
        // Insertar detalles si existen y las tablas están disponibles
        if ($idcotizacion && !empty($detalles)) {
            try {
                if ($this->tableExists('cotizacion_detalle') && $this->tableExists('servicios')) {
                    $db = \Config\Database::connect();
                    foreach ($detalles as $detalle) {
                        $detalle['idcotizacion'] = $idcotizacion;
                        $db->table('cotizacion_detalle')->insert($detalle);
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', 'No se pudieron insertar detalles de cotización: ' . $e->getMessage());
            }
        }
        
        return $idcotizacion;
    }

    /**
     * Cambiar estado de cotización
     */
    public function cambiarEstado($idcotizacion, $nuevoEstado)
    {
        $estadosValidos = ['Borrador', 'Enviada', 'Aceptada', 'Rechazada'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return false;
        }
        
        $updateData = ['estado' => $nuevoEstado];
        
        // Registrar fecha según el estado
        if ($nuevoEstado === 'Enviada') {
            $updateData['fecha_envio'] = date('Y-m-d H:i:s');
        } elseif (in_array($nuevoEstado, ['Aceptada', 'Rechazada'])) {
            $updateData['fecha_respuesta'] = date('Y-m-d H:i:s');
        }

        return $this->update($idcotizacion, $updateData);
    }

    /**
     * Verificar y actualizar cotizaciones vencidas
     */
    public function getCotizacionesPendientes($userId = null)
    {
        $builder = $this->where('estado', 'Borrador');
        
        if ($userId) {
            $builder->where('idusuario', $userId);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Obtener cotizaciones vigentes de un lead
     */
    public function getCotizacionesEnviadas($idlead)
    {
        return $this->where('idlead', $idlead)
            ->where('estado', 'Enviada')
            ->orderBy('created_at', 'DESC')
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
            SUM(CASE WHEN estado = "Borrador" THEN 1 ELSE 0 END) as borradores,
            SUM(CASE WHEN estado = "Enviada" THEN 1 ELSE 0 END) as enviadas,
            SUM(CASE WHEN estado = "Aceptada" THEN 1 ELSE 0 END) as aceptadas,
            SUM(CASE WHEN estado = "Rechazada" THEN 1 ELSE 0 END) as rechazadas,
            AVG(total) as precio_promedio,
            SUM(CASE WHEN estado = "Aceptada" THEN total ELSE 0 END) as valor_aceptado
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
            SUM(CASE WHEN estado = "Aceptada" THEN 1 ELSE 0 END) as aceptadas,
            ROUND((SUM(CASE WHEN estado = "Aceptada" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as tasa_conversion
        ')
        ->where('created_at >=', $fechaInicio)
        ->first();

        return $resultado;
    }
    
    /**
     * Obtener detalles de una cotización
     */
    public function getDetallesCotizacion($idcotizacion)
    {
        $db = \Config\Database::connect();
        return $db->table('cotizacion_detalle cd')
            ->select('cd.*, s.nombre as servicio_nombre, s.descripcion, s.categoria')
            ->join('servicios s', 'cd.idservicio = s.idservicio')
            ->where('cd.idcotizacion', $idcotizacion)
            ->get()
            ->getResultArray();
    }
}
