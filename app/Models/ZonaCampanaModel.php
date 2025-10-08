<?php

namespace App\Models;

use CodeIgniter\Model;

class ZonaCampanaModel extends Model
{
    protected $table = 'tb_zonas_campana';
    protected $primaryKey = 'id_zona';
    protected $allowedFields = [
        'id_campana',
        'nombre_zona',
        'descripcion',
        'poligono',
        'color',
        'prioridad',
        'area_m2',
        'iduser_create',
        'iduser_update',
        'inactive_at'
    ];
    
    protected $useTimestamps = false;
    protected $createdField = 'create_at';
    protected $updatedField = 'update_at';
    protected $deletedField = 'inactive_at';
    protected $useSoftDeletes = true;
    
    protected $returnType = 'array';
    protected $validationRules = [
        'id_campana' => 'required|integer',
        'nombre_zona' => 'required|min_length[3]|max_length[100]',
        'poligono' => 'required',
        'prioridad' => 'in_list[Alta,Media,Baja]'
    ];
    
    protected $validationMessages = [
        'nombre_zona' => [
            'required' => 'El nombre de la zona es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres'
        ],
        'poligono' => [
            'required' => 'Debe definir el polígono de la zona'
        ]
    ];
    
    /**
     * Obtener zonas de una campaña con estadísticas
     */
    public function getZonasPorCampana($idCampana, $incluirInactivas = false)
    {
        $builder = $this->db->table($this->table . ' z');
        $builder->select('
            z.*,
            c.nombre as nombre_campana,
            COUNT(DISTINCT p.idpersona) as total_prospectos,
            COUNT(DISTINCT a.idusuario) as agentes_asignados,
            ROUND(z.area_m2 / 1000000, 2) as area_km2
        ');
        $builder->join('campanias c', 'z.id_campana = c.idcampania', 'left');
        $builder->join('personas p', 'p.id_zona = z.id_zona', 'left');
        $builder->join('tb_asignaciones_zona a', 'a.id_zona = z.id_zona AND a.estado = "Activa"', 'left');
        $builder->where('z.id_campana', $idCampana);
        
        if (!$incluirInactivas) {
            $builder->where('z.estado', 'Activa');
        }
        
        $builder->groupBy('z.id_zona');
        $builder->orderBy('z.prioridad', 'ASC');
        $builder->orderBy('z.nombre_zona', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Obtener zona con detalles completos
     */
    public function getZonaDetalle($idZona)
    {
        $builder = $this->db->table($this->table . ' z');
        $builder->select('
            z.*,
            c.nombre as nombre_campana,
            c.tipo_campana,
            c.estado as estado_campana,
            CONCAT(pc.nombres, " ", pc.apellidos) as creado_por,
            CONCAT(pu.nombres, " ", pu.apellidos) as actualizado_por,
            COUNT(DISTINCT p.idpersona) as total_prospectos,
            COUNT(DISTINCT a.idusuario) as agentes_asignados,
            ROUND(z.area_m2 / 1000000, 2) as area_km2
        ');
        $builder->join('campanias c', 'z.id_campana = c.idcampania', 'left');
        $builder->join('usuarios uc', 'z.iduser_create = uc.idusuario', 'left');
        $builder->join('personas pc', 'uc.idpersona = pc.idpersona', 'left');
        $builder->join('usuarios uu', 'z.iduser_update = uu.idusuario', 'left');
        $builder->join('personas pu', 'uu.idpersona = pu.idpersona', 'left');
        $builder->join('personas p', 'p.id_zona = z.id_zona', 'left');
        $builder->join('tb_asignaciones_zona a', 'a.id_zona = z.id_zona AND a.estado = "Activa"', 'left');
        $builder->where('z.id_zona', $idZona);
        $builder->groupBy('z.id_zona');
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Obtener todas las zonas activas para el mapa
     */
    public function getZonasParaMapa($idCampana = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('id_zona, id_campana, nombre_zona, poligono, color, prioridad, area_m2');
        $builder->where('inactive_at IS NULL');
        
        if ($idCampana !== null) {
            $builder->where('id_campana', $idCampana);
        }
        
        $zonas = $builder->get()->getResultArray();
        
        // Decodificar JSON de polígonos
        foreach ($zonas as &$zona) {
            if (is_string($zona['poligono'])) {
                $zona['poligono'] = json_decode($zona['poligono'], true);
            }
        }
        
        return $zonas;
    }
    
    /**
     * Verificar si un punto está dentro de alguna zona
     */
    public function buscarZonaPorCoordenadas($lat, $lng, $idCampana = null)
    {
        // Esta función retorna las zonas candidatas
        // La validación exacta se hace con Turf.js en el frontend
        $builder = $this->db->table($this->table);
        $builder->select('id_zona, nombre_zona, poligono');
        $builder->where('inactive_at IS NULL');
        
        if ($idCampana !== null) {
            $builder->where('id_campana', $idCampana);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Obtener métricas de una zona
     */
    public function getMetricasZona($idZona, $fechaInicio = null, $fechaFin = null)
    {
        $builder = $this->db->table('tb_metricas_zona');
        $builder->select('
            fecha,
            total_prospectos,
            contactados,
            interesados,
            convertidos,
            rechazados,
            tasa_conversion,
            tasa_contacto,
            roi
        ');
        $builder->where('id_zona', $idZona);
        
        if ($fechaInicio) {
            $builder->where('fecha >=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $builder->where('fecha <=', $fechaFin);
        }
        
        $builder->orderBy('fecha', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Obtener prospectos de una zona
     */
    public function getProspectosZona($idZona)
    {
        $builder = $this->db->table('personas p');
        $builder->select('
            p.idpersona,
            p.nombres,
            p.apellidos,
            p.telefono,
            p.correo,
            p.direccion,
            p.coordenadas,
            p.origen,
            COUNT(i.id_interaccion) as total_interacciones,
            MAX(i.fecha_interaccion) as ultima_interaccion,
            MAX(i.resultado) as ultimo_resultado
        ');
        $builder->join('tb_interacciones i', 'i.id_prospecto = p.idpersona', 'left');
        $builder->where('p.id_zona', $idZona);
        $builder->groupBy('p.idpersona');
        $builder->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Actualizar área de zona
     */
    public function actualizarArea($idZona, $areaM2)
    {
        return $this->update($idZona, ['area_m2' => $areaM2]);
    }
    
    /**
     * Cambiar prioridad de zona
     */
    public function cambiarPrioridad($idZona, $prioridad)
    {
        if (!in_array($prioridad, ['Alta', 'Media', 'Baja'])) {
            return false;
        }
        
        return $this->update($idZona, ['prioridad' => $prioridad]);
    }
    
    /**
     * Desactivar zona (soft delete)
     */
    public function desactivarZona($idZona, $idUsuario)
    {
        return $this->update($idZona, [
            'inactive_at' => date('Y-m-d H:i:s'),
            'iduser_update' => $idUsuario
        ]);
    }
    
    /**
     * Reactivar zona
     */
    public function reactivarZona($idZona, $idUsuario)
    {
        return $this->update($idZona, [
            'inactive_at' => null,
            'iduser_update' => $idUsuario
        ]);
    }
}
