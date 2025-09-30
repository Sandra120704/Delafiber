<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\LeadModel;

class Tareas extends BaseController
{
    protected $tareaModel;
    protected $leadModel;

    public function __construct()
    {
        $this->tareaModel = new TareaModel();
        $this->leadModel = new LeadModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        $idusuario = session()->get('idusuario');
        
        // Datos básicos para evitar errores
        $data = [
            'title' => 'Mis Tareas - Delafiber CRM',
            'pendientes' => [],
            'hoy' => [],
            'vencidas' => [],
            'completadas' => [],
            'leads' => [],
            'tareas_pendientes_count' => 0
        ];

        try {
            // Intentar obtener tareas básicas
            $pendientes = $this->tareaModel
                ->where('idusuario', $idusuario)
                ->where('estado', 'Pendiente')
                ->findAll();
            
            $data['pendientes'] = $pendientes;
            $data['tareas_pendientes_count'] = count($pendientes);
            
            // Obtener leads básicos
            $leads = $this->leadModel->findAll(20);
            $data['leads'] = $leads;
            
        } catch (\Exception $e) {
            log_message('error', 'Error en Tareas::index: ' . $e->getMessage());
        }

        return view('tareas/index', $data);
    }

    private function getTareasPendientes($idusuario)
    {
        try {
            return $this->tareaModel
                ->select('tareas.*, COALESCE(CONCAT(p.nombres, " ", p.apellidos), "Sin lead") as lead_nombre, COALESCE(p.telefono, "") as lead_telefono')
                ->join('leads l', 'l.idlead = tareas.idlead', 'left')
                ->join('personas p', 'p.idpersona = l.idpersona', 'left')
                ->where('tareas.idusuario', $idusuario)
                ->where('tareas.estado', 'Pendiente')
                ->where('tareas.fecha_vencimiento >', date('Y-m-d H:i:s'))
                ->orderBy('tareas.prioridad', 'DESC')
                ->orderBy('tareas.fecha_vencimiento', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error en getTareasPendientes: ' . $e->getMessage());
            return [];
        }
    }

    private function getTareasHoy($idusuario)
    {
        try {
            $hoy_inicio = date('Y-m-d 00:00:00');
            $hoy_fin = date('Y-m-d 23:59:59');
            
            return $this->tareaModel
                ->select('tareas.*, COALESCE(CONCAT(p.nombres, " ", p.apellidos), "Sin lead") as lead_nombre')
                ->join('leads l', 'l.idlead = tareas.idlead', 'left')
                ->join('personas p', 'p.idpersona = l.idpersona', 'left')
                ->where('tareas.idusuario', $idusuario)
                ->where('tareas.estado', 'Pendiente')
                ->where('tareas.fecha_vencimiento >=', $hoy_inicio)
                ->where('tareas.fecha_vencimiento <=', $hoy_fin)
                ->orderBy('tareas.fecha_vencimiento', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error en getTareasHoy: ' . $e->getMessage());
            return [];
        }
    }

    private function getTareasVencidas($idusuario)
    {
        try {
            return $this->tareaModel
                ->select('tareas.*, COALESCE(CONCAT(p.nombres, " ", p.apellidos), "Sin lead") as lead_nombre')
                ->join('leads l', 'l.idlead = tareas.idlead', 'left')
                ->join('personas p', 'p.idpersona = l.idpersona', 'left')
                ->where('tareas.idusuario', $idusuario)
                ->where('tareas.estado', 'Pendiente')
                ->where('tareas.fecha_vencimiento <', date('Y-m-d H:i:s'))
                ->orderBy('tareas.fecha_vencimiento', 'ASC')
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Error en getTareasVencidas: ' . $e->getMessage());
            return [];
        }
    }

    private function getTareasCompletadas($idusuario)
    {
        return $this->tareaModel
            ->select('tareas.*, CONCAT(p.nombres, " ", p.apellidos) as lead_nombre')
            ->join('leads l', 'l.idlead = tareas.idlead', 'left')
            ->join('personas p', 'p.idpersona = l.idpersona', 'left')
            ->where('tareas.idusuario', $idusuario)
            ->where('tareas.estado', 'Completada')
            ->orderBy('tareas.fecha_completado', 'DESC')
            ->limit(50)
            ->findAll();
    }

    /**
     * Guardar nueva tarea
     */
    public function crear()
    {
        // Validación
        $rules = [
            'titulo' => 'required|min_length[5]|max_length[200]',
            'tipo_tarea' => 'required',
            'prioridad' => 'required|in_list[baja,media,alta,urgente]',
            'fecha_vencimiento' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        $data = [
            'idlead' => $this->request->getPost('idlead') ?: null,
            'idusuario' => session()->get('idusuario'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'tipo_tarea' => $this->request->getPost('tipo_tarea'),
            'prioridad' => $this->request->getPost('prioridad'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'fecha_inicio' => date('Y-m-d'),
            'estado' => 'Pendiente'
        ];

        try {
            $this->tareaModel->insert($data);
            
            return redirect()->to('tareas')
                ->with('success', 'Tarea creada exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la tarea: ' . $e->getMessage());
        }
    }

    public function completar($id = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $tarea = $this->tareaModel->find($id);
        
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No autorizado'
            ]);
        }

        $data = [
            'estado' => 'Completada',
            'fecha_completado' => date('Y-m-d H:i:s'),
            'notas_resultado' => $this->request->getPost('notas_resultado')
        ];

        try {
            $this->tareaModel->update($id, $data);
            
            // Seguimiento automático si se solicita
            if ($this->request->getPost('fecha_seguimiento')) {
                $nuevaTarea = [
                    'idlead' => $tarea['idlead'],
                    'idusuario' => $tarea['idusuario'],
                    'titulo' => 'Seguimiento: ' . $tarea['titulo'],
                    'descripcion' => 'Tarea de seguimiento generada automáticamente',
                    'tipo_tarea' => 'seguimiento',
                    'prioridad' => 'media',
                    'fecha_vencimiento' => $this->request->getPost('fecha_seguimiento'),
                    'fecha_inicio' => date('Y-m-d'),
                    'estado' => 'Pendiente'
                ];
                $this->tareaModel->insert($nuevaTarea);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tarea completada'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error'
            ]);
        }
    }

    // Resto de métodos...
    public function reprogramar()
    {
        if (!$this->request->isAJAX()) return redirect()->back();

        $json = $this->request->getJSON(true);
        $tarea = $this->tareaModel->find($json['idtarea']);
        
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->tareaModel->update($json['idtarea'], [
            'fecha_vencimiento' => $json['nueva_fecha']
        ]);
        
        return $this->response->setJSON(['success' => true]);
    }

    public function completarMultiples()
    {
        if (!$this->request->isAJAX()) return redirect()->back();

        $ids = $this->request->getJSON(true)['ids'];
        
        foreach ($ids as $id) {
            $tarea = $this->tareaModel->find($id);
            if ($tarea && $tarea['idusuario'] == session()->get('idusuario')) {
                $this->tareaModel->update($id, [
                    'estado' => 'Completada',
                    'fecha_completado' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        return $this->response->setJSON(['success' => true]);
    }

    public function eliminarMultiples()
    {
        if (!$this->request->isAJAX()) return redirect()->back();

        $ids = $this->request->getJSON(true)['ids'];
        
        foreach ($ids as $id) {
            $tarea = $this->tareaModel->find($id);
            if ($tarea && $tarea['idusuario'] == session()->get('idusuario')) {
                $this->tareaModel->delete($id);
            }
        }
        
        return $this->response->setJSON(['success' => true]);
    }

    public function detalle($id)
    {
        if (!$this->request->isAJAX()) return redirect()->back();

        $tarea = $this->tareaModel
            ->select('tareas.*, CONCAT(p.nombres, " ", p.apellidos) as lead_nombre, p.telefono, p.correo')
            ->join('leads l', 'l.idlead = tareas.idlead')
            ->join('personas p', 'p.idpersona = l.idpersona')
            ->find($id);

        return $this->response->setJSON([
            'success' => !!$tarea,
            'tarea' => $tarea
        ]);
    }

    public function verificarProximasVencer()
    {
        if (!$this->request->isAJAX()) return redirect()->back();

        $count = $this->tareaModel
            ->where('idusuario', session()->get('idusuario'))
            ->where('estado', 'Pendiente')
            ->where('fecha_vencimiento <=', date('Y-m-d H:i:s', strtotime('+2 hours')))
            ->where('fecha_vencimiento >', date('Y-m-d H:i:s'))
            ->countAllResults();

        return $this->response->setJSON(['count' => $count]);
    }
}