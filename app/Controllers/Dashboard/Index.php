<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\TareaModel;
use App\Models\SeguimientoModel;

class Index extends BaseController
{
    protected $leadModel;
    protected $tareaModel;
    protected $seguimientoModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->tareaModel = new TareaModel();
        $this->seguimientoModel = new SeguimientoModel();
    }

    public function index()
    {
        $idusuario = session()->get('idusuario');
        $db = \Config\Database::connect();
        
        // Obtener resumen de datos
        $resumen = [
            'total_leads' => $this->leadModel->where('idusuario', $idusuario)
                                             ->where('estado IS NULL')
                                             ->countAllResults(),
            
            'tareas_pendientes' => $this->tareaModel->where('idusuario', $idusuario)
                                                    ->where('estado', 'Pendiente')
                                                    ->countAllResults(),
            
            'tareas_vencidas' => $this->tareaModel->where('idusuario', $idusuario)
                                                  ->where('estado', 'Pendiente')
                                                  ->where('fecha_vencimiento <', date('Y-m-d H:i:s'))
                                                  ->countAllResults(),
            
            'conversiones_mes' => $this->leadModel->where('idusuario', $idusuario)
                                                  ->where('estado', 'Convertido')
                                                  ->where('MONTH(fecha_conversion_contrato)', date('m'))
                                                  ->where('YEAR(fecha_conversion_contrato)', date('Y'))
                                                  ->countAllResults(),
            
            'leads_calientes' => $this->leadModel->where('idusuario', $idusuario)
                                                 ->where('idetapa >=', 4) // COTIZACION o superior
                                                 ->where('estado IS NULL')
                                                 ->countAllResults(),
        ];
        
        // Tareas de hoy
        $tareas_hoy = $db->query("
            SELECT t.*, 
                   CONCAT(p.nombres, ' ', p.apellidos) as cliente_nombre,
                   p.telefono as cliente_telefono
            FROM tareas t
            INNER JOIN leads l ON t.idlead = l.idlead
            INNER JOIN personas p ON l.idpersona = p.idpersona
            WHERE t.idusuario = ?
            AND t.estado = 'Pendiente'
            AND DATE(t.fecha_vencimiento) = CURDATE()
            ORDER BY t.fecha_vencimiento ASC
            LIMIT 5
        ", [$idusuario])->getResultArray();
        
        // Leads calientes (en etapas avanzadas)
        $leads_calientes = $db->query("
            SELECT l.idlead,
                   CONCAT(p.nombres, ' ', p.apellidos) as cliente_nombre,
                   p.telefono,
                   d.nombre as distrito,
                   e.nombre as etapa
            FROM leads l
            INNER JOIN personas p ON l.idpersona = p.idpersona
            INNER JOIN etapas e ON l.idetapa = e.idetapa
            LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
            WHERE l.idusuario = ?
            AND l.idetapa >= 4
            AND l.estado IS NULL
            ORDER BY l.fecha_modificacion DESC
            LIMIT 5
        ", [$idusuario])->getResultArray();
        
        // Actividad reciente
        $actividad_reciente = $db->query("
            SELECT CONCAT(p.nombres, ' ', p.apellidos) as cliente_nombre,
                   m.nombre as modalidad,
                   s.fecha
            FROM seguimiento s
            INNER JOIN leads l ON s.idlead = l.idlead
            INNER JOIN personas p ON l.idpersona = p.idpersona
            INNER JOIN modalidades m ON s.idmodalidad = m.idmodalidad
            WHERE s.idusuario = ?
            ORDER BY s.fecha DESC
            LIMIT 5
        ", [$idusuario])->getResultArray();
        
        $data = [
            'title' => 'Dashboard - Delafiber CRM',
            'user_name' => session()->get('nombre_completo') ?? 'Usuario',
            'resumen' => $resumen,
            'tareas_hoy' => $tareas_hoy,
            'leads_calientes' => $leads_calientes,
            'actividad_reciente' => $actividad_reciente,
        ];
        
        return view('Dashboard/index', $data);
    }
    
    public function getLeadQuickInfo($idlead)
    {
        // Para acciones rápidas desde el dashboard
        $db = \Config\Database::connect();
        $lead = $db->query("
            SELECT l.*, 
                   CONCAT(p.nombres, ' ', p.apellidos) as cliente_nombre,
                   p.telefono, p.correo, p.direccion
            FROM leads l
            INNER JOIN personas p ON l.idpersona = p.idpersona
            WHERE l.idlead = ?
        ", [$idlead])->getRowArray();
        
        return $this->response->setJSON($lead);
    }
    
    public function completarTarea()
    {
        $idtarea = $this->request->getJSON()->idtarea ?? null;
        
        if ($idtarea) {
            $this->tareaModel->update($idtarea, [
                'estado' => 'Completada',
                'fecha_completado' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON(['success' => true]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }
}
