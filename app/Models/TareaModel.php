<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaModel extends Model
{
    protected $table = 'tareas';
    protected $primaryKey = 'idtarea';
    protected $allowedFields = [
        'idlead',
        'idusuario',
        'titulo',
        'descripcion',
        'tipo_tarea',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        'fecha_vencimiento',
        'fecha_completado',
        'estado',
        'notas_resultado'
    ];
    
    /**
     * Obtener tareas con información completa
     */
    public function getTareasCompletas($filtros = [])
    {
        $builder = $this->db->table($this->table . ' t');
        $builder->select('
            t.*,
            CONCAT(p.nombres, " ", p.apellidos) as usuario_nombre,
            CONCAT(pl.nombres, " ", pl.apellidos) as lead_nombre,
            l.idlead
        ');
        $builder->join('usuarios u', 't.idusuario = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('leads l', 't.idlead = l.idlead', 'left');
        $builder->join('personas pl', 'l.idpersona = pl.idpersona', 'left');
        
        // Filtros
        if (!empty($filtros['idusuario'])) {
            $builder->where('t.idusuario', $filtros['idusuario']);
        }
        
        if (!empty($filtros['estado'])) {
            $builder->where('t.estado', $filtros['estado']);
        }
        
        if (!empty($filtros['idlead'])) {
            $builder->where('t.idlead', $filtros['idlead']);
        }
        
        if (!empty($filtros['prioridad'])) {
            $builder->where('t.prioridad', $filtros['prioridad']);
        }
        
        $builder->orderBy('t.fecha_vencimiento', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obtener tareas pendientes de un usuario
     */
    public function getTareasPendientes($idusuario, $limit = null)
    {
        $filtros = [
            'idusuario' => $idusuario,
            'estado' => 'Pendiente'
        ];
        
        $tareas = $this->getTareasCompletas($filtros);
        
        if ($limit) {
            return array_slice($tareas, 0, $limit);
        }
        
        return $tareas;
    }

    /**
     * Obtener tareas vencidas
     */
    public function getTareasVencidas($idusuario = null)
    {
        $builder = $this->db->table($this->table . ' t');
        $builder->select('
            t.*,
            CONCAT(p.nombres, " ", p.apellidos) as usuario_nombre,
            CONCAT(pl.nombres, " ", pl.apellidos) as lead_nombre
        ');
        $builder->join('usuarios u', 't.idusuario = u.idusuario', 'left');
        $builder->join('personas p', 'u.idpersona = p.idpersona', 'left');
        $builder->join('leads l', 't.idlead = l.idlead', 'left');
        $builder->join('personas pl', 'l.idpersona = pl.idpersona', 'left');
        
        $builder->where('t.estado !=', 'Completada');
        $builder->where('t.fecha_vencimiento <', date('Y-m-d H:i:s'));
        
        if ($idusuario) {
            $builder->where('t.idusuario', $idusuario);
        }
        
        $builder->orderBy('t.fecha_vencimiento', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Marcar tarea como completada
     */
    public function completarTarea($idtarea, $notas_resultado = null)
    {
        $data = [
            'estado' => 'Completada',
            'fecha_completado' => date('Y-m-d H:i:s')
        ];
        
        if ($notas_resultado) {
            $data['notas_resultado'] = $notas_resultado;
        }
        
        return $this->update($idtarea, $data);
    }

    /**
     * Obtener tareas de hoy
     */
    public function getTareasHoy($idusuario)
    {
        $builder = $this->db->table($this->table . ' t');
        $builder->select('
            t.*,
            CONCAT(pl.nombres, " ", pl.apellidos) as lead_nombre
        ');
        $builder->join('leads l', 't.idlead = l.idlead', 'left');
        $builder->join('personas pl', 'l.idpersona = pl.idpersona', 'left');
        
        $builder->where('t.idusuario', $idusuario);
        $builder->where('t.estado !=', 'Completada');
        $builder->where('DATE(t.fecha_vencimiento)', date('Y-m-d'));
        
        $builder->orderBy('t.fecha_vencimiento', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Crear tarea y registrar en historial de lead
     */
    public function crearTarea($data)
    {
        if ($this->insert($data)) {
            $idtarea = $this->getInsertID();
            
            // Registrar en historial del lead
            $leadModel = new LeadModel();
            $leadModel->registrarHistorial(
                $data['idlead'],
                $data['idusuario'],
                'Tarea programada: ' . $data['titulo']
            );
            
            return $idtarea;
        }
        
        return false;
    }
}


