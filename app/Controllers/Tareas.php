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

    /**
     * Mostrar lista de tareas del usuario
     */
    public function index()
    {
        $idusuario = session()->get('idusuario');

        // Obtener todas las tareas del usuario
        $tareas = $this->tareaModel->getTareasConDetalles([
            'idusuario' => $idusuario
        ]);

        // Calcular contadores
        $contadores = [
            'pendientes' => 0,
            'hoy' => 0,
            'completadas' => 0
        ];

        $hoy = date('Y-m-d');
        foreach ($tareas as $tarea) {
            if ($tarea['estado'] == 'Completada') {
                $contadores['completadas']++;
            } elseif (date('Y-m-d', strtotime($tarea['fecha_vencimiento'])) == $hoy) {
                $contadores['hoy']++;
            } elseif (strtotime($tarea['fecha_vencimiento']) < strtotime($hoy)) {
                $contadores['pendientes']++;
            }
        }

        // Obtener leads para el select
        $leads = $this->leadModel->getLeadsBasicos([
            'idusuario' => $idusuario,
            'activos' => true
        ]);

        $data = [
            'title' => 'Mis Tareas',
            'tareas' => $tareas,
            'contadores' => $contadores,
            'leads' => $leads
        ];

        return view('tareas/index', $data);
    }

    /**
     * Guardar nueva tarea
     */
    public function store()
    {
        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'titulo' => 'required|min_length[3]|max_length[200]',
            'fecha_vencimiento' => 'required',
            'prioridad' => 'required|in_list[Baja,Media,Alta]',
            'estado' => 'required|in_list[Pendiente,En Progreso,Completada,Cancelada]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Preparar datos
        $data = [
            'idlead' => $this->request->getPost('idlead') ?: null,
            'idusuario' => session()->get('idusuario'),
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'prioridad' => $this->request->getPost('prioridad'),
            'estado' => $this->request->getPost('estado')
        ];

        // Guardar
        if ($this->tareaModel->insert($data)) {
            // Determinar redirección
            $redirect = $this->request->getGet('redirect') ?: 'tareas';
            
            return redirect()->to($redirect)
                ->with('success', 'Tarea creada exitosamente');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la tarea');
        }
    }

    /**
     * Actualizar tarea existente
     */
    public function update($id)
    {
        // Verificar que existe y pertenece al usuario
        $tarea = $this->tareaModel->find($id);
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return redirect()->to('tareas')
                ->with('error', 'Tarea no encontrada');
        }

        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'titulo' => 'required|min_length[3]|max_length[200]',
            'fecha_vencimiento' => 'required',
            'prioridad' => 'required|in_list[Baja,Media,Alta]',
            'estado' => 'required|in_list[Pendiente,En Progreso,Completada,Cancelada]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Preparar datos
        $data = [
            'idlead' => $this->request->getPost('idlead') ?: null,
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento'),
            'prioridad' => $this->request->getPost('prioridad'),
            'estado' => $this->request->getPost('estado')
        ];

        // Si se marca como completada, registrar fecha
        if ($data['estado'] == 'Completada' && $tarea['estado'] != 'Completada') {
            $data['fecha_completado'] = date('Y-m-d H:i:s');
        }

        // Actualizar
        if ($this->tareaModel->update($id, $data)) {
            return redirect()->to('tareas')
                ->with('success', 'Tarea actualizada exitosamente');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la tarea');
        }
    }

    /**
     * Marcar tarea como completada (acción rápida)
     */
    public function completar($id)
    {
        // Verificar que existe y pertenece al usuario
        $tarea = $this->tareaModel->find($id);
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return redirect()->to('tareas')
                ->with('error', 'Tarea no encontrada');
        }

        // Actualizar estado
        $data = [
            'estado' => 'Completada',
            'fecha_completado' => date('Y-m-d H:i:s')
        ];

        if ($this->tareaModel->update($id, $data)) {
            // Determinar redirección
            $redirect = $this->request->getGet('redirect') ?: 'tareas';
            
            return redirect()->to($redirect)
                ->with('success', 'Tarea completada exitosamente');
        } else {
            return redirect()->back()
                ->with('error', 'Error al completar la tarea');
        }
    }

    /**
     * Eliminar tarea
     */
    public function delete($id)
    {
        // Verificar que existe y pertenece al usuario
        $tarea = $this->tareaModel->find($id);
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return redirect()->to('tareas')
                ->with('error', 'Tarea no encontrada');
        }

        // Eliminar
        if ($this->tareaModel->delete($id)) {
            return redirect()->to('tareas')
                ->with('success', 'Tarea eliminada exitosamente');
        } else {
            return redirect()->to('tareas')
                ->with('error', 'Error al eliminar la tarea');
        }
    }

    /**
     * API: Obtener tareas por lead (para AJAX)
     */
    public function getTareasByLead($idlead)
    {
        $tareas = $this->tareaModel->getTareasConDetalles([
            'idlead' => $idlead,
            'idusuario' => session()->get('idusuario')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'tareas' => $tareas
        ]);
    }

    /**
     * API: Cambiar estado de tarea (para AJAX)
     */
    public function cambiarEstado($id)
    {
        // Verificar que existe y pertenece al usuario
        $tarea = $this->tareaModel->find($id);
        if (!$tarea || $tarea['idusuario'] != session()->get('idusuario')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ]);
        }

        $nuevoEstado = $this->request->getJSON()->estado ?? 'Completada';
        
        $data = [
            'estado' => $nuevoEstado
        ];

        if ($nuevoEstado == 'Completada') {
            $data['fecha_completado'] = date('Y-m-d H:i:s');
        }

        if ($this->tareaModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar'
            ]);
        }
    }

    /**
     * Obtener tareas vencidas (para notificaciones)
     */
    public function getVencidas()
    {
        $idusuario = session()->get('idusuario');
        
        $tareas = $this->tareaModel
            ->where('idusuario', $idusuario)
            ->where('estado !=', 'Completada')
            ->where('fecha_vencimiento <', date('Y-m-d H:i:s'))
            ->orderBy('fecha_vencimiento', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'count' => count($tareas),
            'tareas' => $tareas
        ]);
    }
}