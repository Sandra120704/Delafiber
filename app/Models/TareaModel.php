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
     * Obtiene tareas del día para un usuario
     */
    public function getTareasHoy($userId)
    {
        $hoy = date('Y-m-d');
        
        return $this->db->table('tareas t')
            ->join('leads l', 't.idlead = l.idlead')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->select('t.*, CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre, 
                     p.telefono as cliente_telefono, l.idlead')
            ->where('t.idusuario', $userId)
            ->where('t.estado', 'Pendiente')
            ->where('DATE(t.fecha_vencimiento)', $hoy)
            ->orderBy('t.fecha_vencimiento', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene tareas vencidas para un usuario
     */
    public function getTareasVencidas($userId)
    {
        $ahora = date('Y-m-d H:i:s');
        
        return $this->db->table('tareas t')
            ->join('leads l', 't.idlead = l.idlead')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->select('t.*, CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre,
                     p.telefono as cliente_telefono, l.idlead')
            ->where('t.idusuario', $userId)
            ->where('t.estado', 'Pendiente')
            ->where('t.fecha_vencimiento <', $ahora)
            ->orderBy('t.fecha_vencimiento', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene próximos vencimientos
     */
    public function getProximosVencimientos($userId, $dias = 3)
    {
        $fechaInicio = date('Y-m-d H:i:s');
        $fechaFin = date('Y-m-d 23:59:59', strtotime("+$dias days"));
        
        return $this->db->table('tareas t')
            ->join('leads l', 't.idlead = l.idlead')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->select('t.*, CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre,
                     p.telefono as cliente_telefono')
            ->where('t.idusuario', $userId)
            ->where('t.estado', 'Pendiente')
            ->where('t.fecha_vencimiento >=', $fechaInicio)
            ->where('t.fecha_vencimiento <=', $fechaFin)
            ->orderBy('t.fecha_vencimiento', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Crea una nueva tarea
     */
    public function crearTarea($datos)
    {
        // Validar datos requeridos
        if (empty($datos['idlead']) || empty($datos['idusuario'])) {
            return false;
        }

        // Datos por defecto
        $tarea = [
            'idlead' => $datos['idlead'],
            'idusuario' => $datos['idusuario'],
            'titulo' => $datos['titulo'] ?? 'Tarea sin título',
            'descripcion' => $datos['descripcion'] ?? '',
            'tipo_tarea' => $datos['tipo_tarea'] ?? 'llamada',
            'prioridad' => $datos['prioridad'] ?? 'media',
            'fecha_inicio' => $datos['fecha_inicio'] ?? date('Y-m-d'),
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? date('Y-m-d H:i:s', strtotime('+1 day')),
            'estado' => 'Pendiente'
        ];

        return $this->insert($tarea);
    }

    /**
     * Completa una tarea
     */
    public function completarTarea($tareaId, $notas = '')
    {
        return $this->update($tareaId, [
            'estado' => 'Completada',
            'fecha_completado' => date('Y-m-d H:i:s'),
            'notas_resultado' => $notas
        ]);
    }

    /**
     * Obtiene la próxima tarea de un lead
     */
    public function getProximaTarea($leadId)
    {
        return $this->db->table('tareas t')
            ->select('t.*, u.usuario')
            ->join('usuarios u', 't.idusuario = u.idusuario')
            ->where('t.idlead', $leadId)
            ->where('t.estado', 'Pendiente')
            ->orderBy('t.fecha_vencimiento', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    /**
     * Obtiene estadísticas de tareas para un usuario
     */
    public function getEstadisticasTareas($userId)
    {
        // Tareas pendientes
        $pendientes = $this->where('idusuario', $userId)
                          ->where('estado', 'Pendiente')
                          ->countAllResults();

        // Tareas vencidas
        $vencidas = $this->where('idusuario', $userId)
                        ->where('estado', 'Pendiente')
                        ->where('fecha_vencimiento <', date('Y-m-d H:i:s'))
                        ->countAllResults();

        // Tareas completadas hoy
        $completadasHoy = $this->where('idusuario', $userId)
                              ->where('estado', 'Completada')
                              ->where('DATE(fecha_completado)', date('Y-m-d'))
                              ->countAllResults();

        // Tareas completadas este mes
        $completadasMes = $this->where('idusuario', $userId)
                              ->where('estado', 'Completada')
                              ->where('MONTH(fecha_completado)', date('m'))
                              ->where('YEAR(fecha_completado)', date('Y'))
                              ->countAllResults();

        // Promedio de tareas por día (últimos 30 días)
        $totalCompletadas30Dias = $this->where('idusuario', $userId)
                                      ->where('estado', 'Completada')
                                      ->where('fecha_completado >=', date('Y-m-d', strtotime('-30 days')))
                                      ->countAllResults();
        $promedioDiario = round($totalCompletadas30Dias / 30, 1);

        return [
            'pendientes' => $pendientes,
            'vencidas' => $vencidas,
            'completadas_hoy' => $completadasHoy,
            'completadas_mes' => $completadasMes,
            'promedio_diario' => $promedioDiario
        ];
    }

    /**
     * Obtiene tareas por tipo
     */
    public function getTareasPorTipo($userId, $tipo = null)
    {
        $builder = $this->db->table('tareas t')
            ->join('leads l', 't.idlead = l.idlead')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->select('t.*, CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre,
                     p.telefono as cliente_telefono')
            ->where('t.idusuario', $userId);

        if ($tipo) {
            $builder->where('t.tipo_tarea', $tipo);
        }

        return $builder->where('t.estado', 'Pendiente')
            ->orderBy('t.fecha_vencimiento', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Programa seguimiento automático
     */
    public function programarSeguimientoAutomatico($leadId, $usuarioId, $tipoInteraccion = 'llamada')
    {
        $configuraciones = [
            'llamada' => [
                'titulo' => 'Seguimiento post-llamada',
                'descripcion' => 'Realizar seguimiento después de la llamada realizada',
                'dias' => 2,
                'hora' => '10:00:00'
            ],
            'whatsapp' => [
                'titulo' => 'Seguimiento WhatsApp',
                'descripcion' => 'Verificar respuesta del mensaje enviado por WhatsApp',
                'dias' => 1,
                'hora' => '16:00:00'
            ],
            'cotizacion' => [
                'titulo' => 'Seguimiento de cotización',
                'descripcion' => 'Verificar si el cliente revisó la cotización enviada',
                'dias' => 3,
                'hora' => '11:00:00'
            ]
        ];

        $config = $configuraciones[$tipoInteraccion] ?? $configuraciones['llamada'];
        
        $fechaVencimiento = date('Y-m-d ' . $config['hora'], strtotime("+{$config['dias']} days"));

        return $this->crearTarea([
            'idlead' => $leadId,
            'idusuario' => $usuarioId,
            'titulo' => $config['titulo'],
            'descripcion' => $config['descripcion'],
            'tipo_tarea' => 'seguimiento',
            'prioridad' => 'media',
            'fecha_vencimiento' => $fechaVencimiento
        ]);
    }

    /**
     * Reprograma una tarea
     */
    public function reprogramarTarea($tareaId, $nuevaFecha, $motivo = '')
    {
        $tarea = $this->find($tareaId);
        if (!$tarea) return false;

        // Actualizar fecha
        $this->update($tareaId, [
            'fecha_vencimiento' => $nuevaFecha,
            'descripcion' => $tarea['descripcion'] . "\n\nReprogramada: " . $motivo
        ]);

        return true;
    }

    /**
     * Obtiene resumen de productividad
     */
    public function getResumenProductividad($userId, $dias = 7)
    {
        $fechaInicio = date('Y-m-d', strtotime("-$dias days"));
        
        // Tareas completadas por día
        $completadasPorDia = $this->db->table('tareas')
            ->select('DATE(fecha_completado) as fecha, COUNT(*) as total')
            ->where('idusuario', $userId)
            ->where('estado', 'Completada')
            ->where('fecha_completado >=', $fechaInicio)
            ->groupBy('DATE(fecha_completado)')
            ->orderBy('fecha', 'ASC')
            ->get()
            ->getResultArray();

        // Tareas por tipo
        $tareasPorTipo = $this->db->table('tareas')
            ->select('tipo_tarea, COUNT(*) as total')
            ->where('idusuario', $userId)
            ->where('fecha_completado >=', $fechaInicio)
            ->where('estado', 'Completada')
            ->groupBy('tipo_tarea')
            ->get()
            ->getResultArray();

        return [
            'completadas_por_dia' => $completadasPorDia,
            'tareas_por_tipo' => $tareasPorTipo
        ];
    }

    /**
     * Elimina tareas obsoletas
     */
    public function limpiarTareasObsoletas($diasVencimiento = 30)
    {
        $fechaLimite = date('Y-m-d H:i:s', strtotime("-$diasVencimiento days"));
        
        return $this->where('estado', 'Pendiente')
                   ->where('fecha_vencimiento <', $fechaLimite)
                   ->delete();
    }
}