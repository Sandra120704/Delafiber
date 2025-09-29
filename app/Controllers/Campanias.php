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
            'activo' => $this->request->getPost('activo') ? 1 : 0
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
        $leads = $this->leadModel->getLeadsByCampania($id);

        // Calcular estadísticas
        $totalLeads = count($leads);
        $conversiones = array_filter($leads, fn($l) => $l['estado'] === 'Convertido');
        $tasaConversion = $totalLeads > 0 ? (count($conversiones) / $totalLeads) * 100 : 0;
        
        $ingresos = array_sum(array_column($conversiones, 'presupuesto_estimado'));
        $costoPorLead = $campania['presupuesto'] > 0 && $totalLeads > 0 
            ? $campania['presupuesto'] / $totalLeads 
            : 0;
        
        $roi = $campania['presupuesto'] > 0 
            ? (($ingresos - $campania['presupuesto']) / $campania['presupuesto']) * 100 
            : 0;

        $data = [
            'title' => $campania['nombre'],
            'campania' => $campania,
            'leads' => $leads,
            'estadisticas' => [
                'total_leads' => $totalLeads,
                'conversiones' => count($conversiones),
                'tasa_conversion' => $tasaConversion,
                'ingresos' => $ingresos,
                'costo_por_lead' => $costoPorLead,
                'roi' => $roi
            ]
        ];

        return view('campanias/view', $data);
    }
}