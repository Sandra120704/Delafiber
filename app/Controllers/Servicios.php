<?php

namespace App\Controllers;

use App\Models\ServicioModel;
use App\Models\CotizacionModel;

class Servicios extends BaseController
{
    protected $servicioModel;
    protected $cotizacionModel;

    public function __construct()
    {
        $this->servicioModel = new ServicioModel();
        $this->cotizacionModel = new CotizacionModel();
    }

    /**
     * Mostrar lista de servicios
     */
    public function index()
    {
        $servicios = $this->servicioModel->getServiciosConEstadisticas();

        $data = [
            'title' => 'Catálogo de Servicios',
            'servicios' => $servicios
        ];

        return view('servicios/index', $data);
    }

    /**
     * Mostrar formulario para crear nuevo servicio
     */
    public function create()
    {
        $data = [
            'title' => 'Nuevo Servicio',
            'servicio' => null
        ];

        return view('servicios/create', $data);
    }

    /**
     * Guardar nuevo servicio
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[150]',
            'velocidad' => 'required|max_length[50]',
            'precio_referencial' => 'required|decimal',
            'precio_instalacion' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'velocidad' => $this->request->getPost('velocidad'),
            'precio_referencial' => $this->request->getPost('precio_referencial'),
            'precio_instalacion' => $this->request->getPost('precio_instalacion') ?? 0,
            'activo' => $this->request->getPost('activo') ? 1 : 0
        ];

        if ($this->servicioModel->insert($data)) {
            return redirect()->to('/servicios')->with('success', 'Servicio creado exitosamente');
        }

        return redirect()->back()->with('error', 'Error al crear el servicio');
    }

    /**
     * Editar servicio
     */
    public function edit($id)
    {
        $servicio = $this->servicioModel->find($id);
        
        if (!$servicio) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Servicio no encontrado');
        }

        $data = [
            'title' => 'Editar Servicio',
            'servicio' => $servicio
        ];

        return view('servicios/create', $data);
    }

    /**
     * Actualizar servicio
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[150]',
            'velocidad' => 'required|max_length[50]',
            'precio_referencial' => 'required|decimal',
            'precio_instalacion' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'velocidad' => $this->request->getPost('velocidad'),
            'precio_referencial' => $this->request->getPost('precio_referencial'),
            'precio_instalacion' => $this->request->getPost('precio_instalacion') ?? 0,
            'activo' => $this->request->getPost('activo') ? 1 : 0
        ];

        if ($this->servicioModel->update($id, $data)) {
            return redirect()->to('/servicios')->with('success', 'Servicio actualizado exitosamente');
        }

        return redirect()->back()->with('error', 'Error al actualizar el servicio');
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleEstado($id)
    {
        $servicio = $this->servicioModel->find($id);
        
        if (!$servicio) {
            return $this->response->setJSON(['success' => false, 'message' => 'Servicio no encontrado']);
        }

        $nuevoEstado = $servicio['activo'] ? 0 : 1;
        
        if ($this->servicioModel->update($id, ['activo' => $nuevoEstado])) {
            $mensaje = $nuevoEstado ? 'Servicio activado' : 'Servicio desactivado';
            return $this->response->setJSON(['success' => true, 'message' => $mensaje]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al cambiar estado']);
    }

    /**
     * Estadísticas de servicios
     */
    public function estadisticas()
    {
        // Puedes preparar datos estadísticos aquí si lo necesitas
        $data = [
            'title' => 'Estadísticas de Servicios'
            // 'estadisticas' => $estadisticas // si tienes datos
        ];
        return view('servicios/estadisticas', $data);
    }

    /**
     * API: Obtener servicios activos (para AJAX)
     */
    public function getServiciosActivos()
    {
        $servicios = $this->servicioModel->getServiciosActivos();
        return $this->response->setJSON($servicios);
    }

    /**
     * API: Obtener información de un servicio específico
     */
    public function getServicio($id)
    {
        $servicio = $this->servicioModel->find($id);
        
        if (!$servicio) {
            return $this->response->setJSON(['error' => 'Servicio no encontrado'], 404);
        }

        return $this->response->setJSON($servicio);
    }
}
