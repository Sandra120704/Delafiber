<?php

namespace App\Controllers;

use App\Models\CotizacionModel;
use App\Models\LeadModel;
use App\Models\ServicioModel;
use App\Models\PersonaModel;

class Cotizaciones extends BaseController
{
    protected $cotizacionModel;
    protected $leadModel;
    protected $servicioModel;
    protected $personaModel;

    public function __construct()
    {
        $this->cotizacionModel = new CotizacionModel();
        $this->leadModel = new LeadModel();
        $this->servicioModel = new ServicioModel();
        $this->personaModel = new PersonaModel();
    }

    /**
     * Mostrar lista de cotizaciones
     */
    public function index()
    {
        $userId = session()->get('idusuario');
        $rol = session()->get('rol');

        // Obtener cotizaciones con información completa
        $cotizaciones = $this->cotizacionModel->getCotizacionesCompletas($userId, $rol);

        $data = [
            'title' => 'Cotizaciones',
            'cotizaciones' => $cotizaciones
        ];

        return view('cotizaciones/index', $data);
    }

    /**
     * Mostrar formulario para crear nueva cotización
     */
    public function create()
    {
        $userId = session()->get('idusuario');
        
        // Obtener leads activos del usuario
        $leads = $this->leadModel->getLeadsBasicos(['idusuario' => $userId, 'activos' => true]);
        
        // Obtener servicios activos
        $servicios = $this->servicioModel->getServiciosActivos();

        // Si viene un lead preseleccionado desde la URL
        $leadPreseleccionado = $this->request->getGet('lead');
        $leadSeleccionado = null;
        
        if ($leadPreseleccionado) {
            $leadSeleccionado = $this->leadModel->getLeadCompleto($leadPreseleccionado, $userId);
        }

        $data = [
            'title' => 'Nueva Cotización',
            'leads' => $leads,
            'servicios' => $servicios,
            'lead_preseleccionado' => $leadPreseleccionado,
            'lead_seleccionado' => $leadSeleccionado
        ];

        return view('cotizaciones/create', $data);
    }

    /**
     * Guardar nueva cotización
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'idlead' => 'required|numeric',
            'idservicio' => 'required|numeric',
            'precio_cotizado' => 'required|decimal',
            'vigencia_dias' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'idlead' => $this->request->getPost('idlead'),
            'idservicio' => $this->request->getPost('idservicio'),
            'precio_cotizado' => $this->request->getPost('precio_cotizado'),
            'descuento_aplicado' => $this->request->getPost('descuento_aplicado') ?? 0,
            'precio_instalacion' => $this->request->getPost('precio_instalacion') ?? 0,
            'vigencia_dias' => $this->request->getPost('vigencia_dias') ?? 30,
            'observaciones' => $this->request->getPost('observaciones')
        ];

        if ($this->cotizacionModel->insert($data)) {
            // Mover lead a etapa COTIZACION si no está ya ahí
            $lead = $this->leadModel->find($data['idlead']);
            if ($lead && isset($lead->idetapa) && $lead->idetapa < 4) { 
                $this->leadModel->update($data['idlead'], ['idetapa' => 4]);
            }

            return redirect()->to('/cotizaciones')->with('success', 'Cotización creada exitosamente');
        }

        return redirect()->back()->with('error', 'Error al crear la cotización');
    }

    /**
     * Ver detalle de cotización
     */
    public function show($id)
    {
        $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
        
        if (!$cotizacion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cotización no encontrada');
        }

        $data = [
            'title' => 'Cotización #' . $id,
            'cotizacion' => $cotizacion
        ];

        return view('cotizaciones/show', $data);
    }

    /**
     * Editar cotización
     */
    public function edit($id)
    {
        $cotizacion = $this->cotizacionModel->find($id);
        
        if (!$cotizacion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cotización no encontrada');
        }

        $userId = session()->get('idusuario');
        $leads = $this->leadModel->getLeadsBasicos(['idusuario' => $userId, 'activos' => true]);
        $servicios = $this->servicioModel->getServiciosActivos();

        $data = [
            'title' => 'Editar Cotización',
            'cotizacion' => $cotizacion,
            'leads' => $leads,
            'servicios' => $servicios
        ];

        return view('cotizaciones/edit', $data);
    }

    /**
     * Actualizar cotización
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'precio_cotizado' => 'required|decimal',
            'vigencia_dias' => 'permit_empty|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'precio_cotizado' => $this->request->getPost('precio_cotizado'),
            'descuento_aplicado' => $this->request->getPost('descuento_aplicado') ?? 0,
            'precio_instalacion' => $this->request->getPost('precio_instalacion') ?? 0,
            'vigencia_dias' => $this->request->getPost('vigencia_dias') ?? 30,
            'observaciones' => $this->request->getPost('observaciones')
        ];

        if ($this->cotizacionModel->update($id, $data)) {
            return redirect()->to('/cotizaciones')->with('success', 'Cotización actualizada exitosamente');
        }

        return redirect()->back()->with('error', 'Error al actualizar la cotización');
    }

    /**
     * Cambiar estado de cotización
     */
    public function cambiarEstado($id)
    {
        $estado = $this->request->getPost('estado');
        $estadosValidos = ['vigente', 'vencida', 'aceptada', 'rechazada'];

        if (!in_array($estado, $estadosValidos)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado no válido']);
        }

        $cotizacion = $this->cotizacionModel->find($id);
        if (!$cotizacion) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cotización no encontrada']);
        }

        if ($this->cotizacionModel->update($id, ['estado' => $estado])) {
            // Si la cotización fue aceptada, mover lead a etapa CIERRE
            if ($estado === 'aceptada') {
                $this->leadModel->update($cotizacion['idlead'], ['idetapa' => 6]); // Etapa CIERRE
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar']);
    }

    /**
     * Generar PDF de cotización
     */
    public function generarPDF($id)
    {
        $cotizacion = $this->cotizacionModel->getCotizacionCompleta($id);
        
        if (!$cotizacion) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Cotización no encontrada');
        }

        $data = [
            'cotizacion' => $cotizacion
        ];

        // Aquí podrías usar una librería como TCPDF o mPDF
        return view('cotizaciones/pdf', $data);
    }

    /**
     * Obtener cotizaciones por lead (AJAX)
     */
    public function porLead($idlead)
    {
        $cotizaciones = $this->cotizacionModel->getCotizacionesPorLead($idlead);
        return $this->response->setJSON($cotizaciones);
    }
}
