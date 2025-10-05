<?php

namespace App\Models;

use CodeIgniter\Model;

class AsignacionZonaModel extends Model
{
    protected $table = 'tb_asignaciones_zona';
    protected $primaryKey = 'id_asignacion';
    protected $allowedFields = [
        'id_zona',
        'id_usuario',
        'fecha_asignacion',
        'fecha_fin',
        'meta_contactos',
        'meta_conversiones',
        'activo'
    ];
    
    protected $useTimestamps = false;
    protected $createdField = 'create_at';
    protected $updatedField = 'update_at';
    
    protected $returnType = 'array';
    protected $validationRules = [
        'id_zona' => 'required|integer',
        'id_usuario' => 'required|integer',
        'meta_contactos' => 'permit_empty|integer',
        'meta_conversiones' => 'permit_empty|integer'
    ];
    
    /**
     * Obtener asignaciones de un agente
     */
    public function getAsignacionesPorAgente($idUsuario, $soloActivas = true)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('
            a.*,
            z.nombre_zona,
            z.prioridad,
            z.color,
            z.area_m2,
            c.nombre as campana_nombre,
            c.tipo_campana,
            COUNT(DISTINCT p.idpersona) as total_prospectos,
            COUNT(DISTINCT i.id_interaccion) as interacciones_realizadas,
            SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) as conversiones_logradas
        ');
        $builder->join('tb_zonas_campana z', 'a.id_zona = z.id_zona', 'left');
        $builder->join('campanias c', 'z.id_campana = c.idcampania', 'left');
        $builder->join('personas p', 'p.id_zona = z.id_zona', 'left');
        $builder->join('tb_interacciones i', 'i.id_prospecto = p.idpersona AND i.id_usuario = a.id_usuario', 'left');
        $builder->where('a.id_usuario', $idUsuario);
        
        if ($soloActivas) {
            $builder->where('a.activo', 1);
            $builder->where('(a.fecha_fin IS NULL OR a.fecha_fin >= CURDATE())');
        }
        
        $builder->groupBy('a.id_asignacion');
        $builder->orderBy('a.fecha_asignacion', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Obtener asignaciones de una zona
     */
    public function getAsignacionesPorZona($idZona, $soloActivas = true)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('
            a.*,
            CONCAT(p.nombres, " ", p.apellidos) as agente_nombre,
            u.correo as agente_correo,
            r.nombre as rol_nombre,
            COUNT(DISTINCT i.id_interaccion) as interacciones_realizadas,
            SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) as conversiones_logradas
        ');
        $builder->join('usuarios u', 'a.id_usuario = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('roles r', 'u.idrol = r.idrol', 'left');
        $builder->join('tb_interacciones i', 'i.id_usuario = a.id_usuario', 'left');
        $builder->where('a.id_zona', $idZona);
        
        if ($soloActivas) {
            $builder->where('a.activo', 1);
        }
        
        $builder->groupBy('a.id_asignacion');
        $builder->orderBy('a.fecha_asignacion', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Asignar zona a agente
     */
    public function asignarZona($datos)
    {
        // Verificar si ya existe una asignación activa
        $existente = $this->where([
            'id_zona' => $datos['id_zona'],
            'id_usuario' => $datos['id_usuario'],
            'activo' => 1
        ])->first();
        
        if ($existente) {
            return ['success' => false, 'message' => 'El agente ya tiene esta zona asignada'];
        }
        
        // Establecer fecha de asignación si no se proporciona
        if (!isset($datos['fecha_asignacion'])) {
            $datos['fecha_asignacion'] = date('Y-m-d');
        }
        
        $datos['activo'] = 1;
        
        $result = $this->insert($datos);
        
        if ($result) {
            return ['success' => true, 'id' => $result];
        }
        
        return ['success' => false, 'message' => 'Error al asignar zona'];
    }
    
    /**
     * Desasignar zona de agente
     */
    public function desasignarZona($idAsignacion)
    {
        return $this->update($idAsignacion, [
            'activo' => 0,
            'fecha_fin' => date('Y-m-d')
        ]);
    }
    
    /**
     * Actualizar metas de asignación
     */
    public function actualizarMetas($idAsignacion, $metaContactos, $metaConversiones)
    {
        return $this->update($idAsignacion, [
            'meta_contactos' => $metaContactos,
            'meta_conversiones' => $metaConversiones
        ]);
    }
    
    /**
     * Obtener rendimiento de asignación
     */
    public function getRendimientoAsignacion($idAsignacion)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('
            a.*,
            z.nombre_zona,
            CONCAT(p.nombres, " ", p.apellidos) as agente_nombre,
            COUNT(DISTINCT pros.idpersona) as total_prospectos_zona,
            COUNT(DISTINCT i.id_interaccion) as interacciones_realizadas,
            COUNT(DISTINCT CASE WHEN i.resultado IN ("Contactado", "Interesado", "Agendado", "Convertido") THEN i.id_interaccion END) as contactos_exitosos,
            SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) as conversiones_logradas,
            ROUND(
                (SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) / NULLIF(a.meta_conversiones, 0)) * 100,
                2
            ) as porcentaje_meta_conversiones,
            ROUND(
                (COUNT(DISTINCT CASE WHEN i.resultado IN ("Contactado", "Interesado", "Agendado", "Convertido") THEN i.id_interaccion END) / NULLIF(a.meta_contactos, 0)) * 100,
                2
            ) as porcentaje_meta_contactos
        ');
        $builder->join('tb_zonas_campana z', 'a.id_zona = z.id_zona', 'left');
        $builder->join('usuarios u', 'a.id_usuario = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('personas pros', 'pros.id_zona = z.id_zona', 'left');
        $builder->join('tb_interacciones i', 'i.id_usuario = a.id_usuario AND i.id_prospecto = pros.idpersona', 'left');
        $builder->where('a.id_asignacion', $idAsignacion);
        $builder->groupBy('a.id_asignacion');
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Obtener ranking de agentes por rendimiento
     */
    public function getRankingAgentes($idCampana = null, $fechaInicio = null, $fechaFin = null)
    {
        $builder = $this->db->table($this->table . ' a');
        $builder->select('
            a.id_usuario,
            CONCAT(p.nombres, " ", p.apellidos) as agente_nombre,
            COUNT(DISTINCT a.id_zona) as zonas_asignadas,
            SUM(a.meta_contactos) as meta_contactos_total,
            SUM(a.meta_conversiones) as meta_conversiones_total,
            COUNT(DISTINCT i.id_interaccion) as interacciones_realizadas,
            SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) as conversiones_logradas,
            ROUND(
                (SUM(CASE WHEN i.resultado = "Convertido" THEN 1 ELSE 0 END) / NULLIF(SUM(a.meta_conversiones), 0)) * 100,
                2
            ) as porcentaje_cumplimiento
        ');
        $builder->join('usuarios u', 'a.id_usuario = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('tb_zonas_campana z', 'a.id_zona = z.id_zona', 'left');
        $builder->join('tb_interacciones i', 'i.id_usuario = a.id_usuario', 'left');
        $builder->where('a.activo', 1);
        
        if ($idCampana) {
            $builder->where('z.id_campana', $idCampana);
        }
        
        if ($fechaInicio) {
            $builder->where('a.fecha_asignacion >=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $builder->where('(a.fecha_fin IS NULL OR a.fecha_fin <=', $fechaFin . ')');
        }
        
        $builder->groupBy('a.id_usuario');
        $builder->orderBy('conversiones_logradas', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Verificar disponibilidad de agente
     */
    public function verificarDisponibilidadAgente($idUsuario, $maxZonas = 5)
    {
        $zonasActivas = $this->where([
            'id_usuario' => $idUsuario,
            'activo' => 1
        ])->countAllResults();
        
        return [
            'disponible' => $zonasActivas < $maxZonas,
            'zonas_activas' => $zonasActivas,
            'zonas_disponibles' => max(0, $maxZonas - $zonasActivas)
        ];
    }
    
    /**
     * Reasignar zona a otro agente
     */
    public function reasignarZona($idZona, $idUsuarioAnterior, $idUsuarioNuevo, $motivo = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Desactivar asignación anterior
        $this->where([
            'id_zona' => $idZona,
            'id_usuario' => $idUsuarioAnterior,
            'activo' => 1
        ])->set([
            'activo' => 0,
            'fecha_fin' => date('Y-m-d')
        ])->update();
        
        // Crear nueva asignación
        $nuevaAsignacion = [
            'id_zona' => $idZona,
            'id_usuario' => $idUsuarioNuevo,
            'fecha_asignacion' => date('Y-m-d'),
            'activo' => 1
        ];
        
        $this->insert($nuevaAsignacion);
        
        $db->transComplete();
        
        return $db->transStatus();
    }
}
