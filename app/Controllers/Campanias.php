<?php

namespace App\Controllers;

use App\Models\CampaniaModel;
use App\Models\LeadModel;

class Campanias extends BaseController
{
    protected $campaniaModel;
    protected $leadModel;

    public function __construct()
    {
        $this->campaniaModel = new CampaniaModel();
        $this->leadModel = new LeadModel();
    }

    /**
     * Mostrar lista de campañas
     */
    public function index()
    {
        // Obtener todas las campañas con conteo de leads
        $campanias = $this->campaniaModel
            ->select('campanias.*, COUNT(leads.idlead) as total_leads')
            ->join('leads', 'leads.idcampania = campanias.idcampania', 'left')
            ->groupBy('campanias.idcampania')
            ->orderBy('campanias.fecha_inicio', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Gestión de Campañas',
            'campanias' => $campanias
        ];

        return view('campanias/index', $data);
    }

    /**
     * Formulario de crear campaña
     */
    public function create()
    {
        $data = [
            'title' => 'Nueva Campaña'
        ];

        return view('campanias/create', $data);
    }

    /**
     * Guardar nueva campaña
     */
    public function store()
    {
        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|min_length[3]|max_length[100]',
            'tipo' => 'required',
            'fecha_inicio' => 'required|valid_date',
            'presupuesto' => 'permit_empty|decimal'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Preparar datos
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'tipo' => $this->request->getPost('tipo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin' => $this->request->getPost('fecha_fin'),
            'presupuesto' => $this->request->getPost('presupuesto') ?: 0,
            'estado' => 'Activa',
            'activo' => 1
        ];

        // Guardar
        if ($this->campaniaModel->insert($data)) {
            return redirect()->to('campanias')
                ->with('success', 'Campaña creada exitosamente');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la campaña');
        }
    }

    /**
     * Formulario de editar campaña
     */
    public function edit($id)
    {
        $campania = $this->campaniaModel->find($id);
        
        if (!$campania) {
            return redirect()->to('campanias')
                ->with('error', 'Campaña no encontrada');
        }

        $data = [
            'title' => 'Editar Campaña',
            'campania' => $campania
        ];

        return view('campanias/edit', $data);
    }

    /**
     * Actualizar campaña existente
     */
    public function update($id)
    {
        // Verificar que existe
        $campania = $this->campaniaModel->find($id);
        if (!$campania) {
            return redirect()->to('campanias')
                ->with('error', 'Campaña no encontrada');
        }

        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|min_length[3]|max_length[100]',
            'tipo' => 'required',
            'fecha_inicio' => 'required|valid_date',
            'presupuesto' => 'permit_empty|decimal'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Preparar datos
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'tipo' => $this->request->getPost('tipo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin' => $this->request->getPost('fecha_fin'),
            'presupuesto' => $this->request->getPost('presupuesto') ?: 0,
            'activo' => $this->request->getPost('activo') ? 1 : 0
        ];

        // Actualizar
        if ($this->campaniaModel->update($id, $data)) {
            return redirect()->to('campanias')
                ->with('success', 'Campaña actualizada exitosamente');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la campaña');
        }
    }

    /**
     * Eliminar campaña
     */
    public function delete($id)
    {
        // Verificar que existe
        $campania = $this->campaniaModel->find($id);
        if (!$campania) {
            return redirect()->to('campanias')
                ->with('error', 'Campaña no encontrada');
        }

        // Verificar si tiene leads asociados
        $leadsAsociados = $this->leadModel
            ->where('idcampania', $id)
            ->countAllResults();

        if ($leadsAsociados > 0) {
            return redirect()->to('campanias')
                ->with('error', "No se puede eliminar. Hay {$leadsAsociados} leads asociados a esta campaña");
        }

        // Eliminar
        if ($this->campaniaModel->delete($id)) {
            return redirect()->to('campanias')
                ->with('success', 'Campaña eliminada exitosamente');
        } else {
            return redirect()->to('campanias')
                ->with('error', 'Error al eliminar la campaña');
        }
    }

    /**
     * Ver detalle de campaña con estadísticas
     */
    public function view($id)
    {
        $campania = $this->campaniaModel->find($id);
        
        if (!$campania) {
            return redirect()->to('campanias')
                ->with('error', 'Campaña no encontrada');
        }

        // Obtener leads de la campaña
        $leads = $this->leadModel
            ->where('idcampania', $id)
            ->orderBy('fecha_registro', 'DESC')
            ->findAll();

        // Leads recientes (últimos 5)
        $leads_recientes = array_slice($leads, 0, 5);

        // Calcular estadísticas
        $totalLeads = count($leads);
        $convertidos = count(array_filter($leads, fn($l) => isset($l['estado']) && $l['estado'] === 'Convertido'));
        $activos = count(array_filter($leads, fn($l) => isset($l['estado']) && $l['estado'] === 'Activo'));
        
        $estadisticas = [
            'total_leads' => $totalLeads,
            'convertidos' => $convertidos,
            'activos' => $activos,
            'tasa_conversion' => $totalLeads > 0 ? ($convertidos / $totalLeads) * 100 : 0
        ];

        $data = [
            'title' => $campania['nombre'],
            'campania' => $campania,
            'leads_recientes' => $leads_recientes,
            'estadisticas' => $estadisticas,
            'difusiones' => [], // Aquí deberías cargar las difusiones reales
            'medios' => [] // Aquí deberías cargar los medios disponibles
        ];

        return view('campanias/view', $data);
    }

    /**
     * Cambiar estado de campaña (Activa/Inactiva)
     */
    public function toggleEstado($id)
    {
        $campania = $this->campaniaModel->find($id);
        
        if (!$campania) {
            return redirect()->to('campanias')
                ->with('error', 'Campaña no encontrada');
        }

        $nuevoEstado = $campania['estado'] === 'Activa' ? 'Inactiva' : 'Activa';
        
        $this->campaniaModel->update($id, ['estado' => $nuevoEstado]);
        
        return redirect()->back()
            ->with('success', "Campaña {$nuevoEstado} correctamente");
    }
}