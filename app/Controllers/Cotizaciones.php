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
        $rol = session()->get('nombreRol'); // Corregido: nombreRol en lugar de rol

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
        
        // Obtener servicios activos (si la tabla existe)
        $servicios = [];
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('servicios')) {
                $servicios = $this->servicioModel->getServiciosActivos();
            }
        } catch (\Exception $e) {
            log_message('warning', 'No se pudo cargar servicios: ' . $e->getMessage());
        }

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
            'lead_seleccionado' => $leadSeleccionado,
            'tabla_servicios_existe' => !empty($servicios)
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

        // Obtener ID de usuario
        $userId = session()->get('idusuario') ?: session()->get('user_id');
        
        // Calcular totales
        $precioCotizado = floatval($this->request->getPost('precio_cotizado'));
        $descuento = floatval($this->request->getPost('descuento_aplicado') ?? 0);
        $precioInstalacion = floatval($this->request->getPost('precio_instalacion') ?? 0);
        
        $descuentoMonto = $precioCotizado * ($descuento / 100);
        $subtotal = $precioCotizado - $descuentoMonto + $precioInstalacion;
        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;
        
        // Datos de la cotización
        $dataCotizacion = [
            'idlead' => $this->request->getPost('idlead'),
            'idusuario' => $userId,
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => 'Borrador'
        ];

        try {
            $db = \Config\Database::connect();
            $db->transStart();
            
            // Insertar cotización
            $idcotizacion = $this->cotizacionModel->insert($dataCotizacion);
            
            if ($idcotizacion) {
                // Insertar detalle de servicio
                $db->table('cotizacion_detalle')->insert([
                    'idcotizacion' => $idcotizacion,
                    'idservicio' => $this->request->getPost('idservicio'),
                    'cantidad' => 1,
                    'precio_unitario' => $precioCotizado,
                    'subtotal' => $precioCotizado - $descuentoMonto
                ]);
                
                // Mover lead a etapa COTIZACION si no está ya ahí
                $lead = $this->leadModel->find($dataCotizacion['idlead']);
                if ($lead && isset($lead['idetapa']) && $lead['idetapa'] < 4) { 
                    $this->leadModel->update($dataCotizacion['idlead'], ['idetapa' => 4]);
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return redirect()->to('/cotizaciones')->with('success', 'Cotización creada exitosamente');
            
        } catch (\Exception $e) {
            log_message('error', 'Error al crear cotización: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear la cotización: ' . $e->getMessage());
        }
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
        
        // Obtener servicios activos (si la tabla existe)
        $servicios = [];
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('servicios')) {
                $servicios = $this->servicioModel->getServiciosActivos();
            }
        } catch (\Exception $e) {
            log_message('warning', 'No se pudo cargar servicios: ' . $e->getMessage());
        }

        $data = [
            'title' => 'Editar Cotización',
            'cotizacion' => $cotizacion,
            'leads' => $leads,
            'servicios' => $servicios,
            'tabla_servicios_existe' => !empty($servicios)
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
        
        if (!$this->cotizacionModel->cambiarEstado($id, $estado)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Estado no válido o error al actualizar']);
        }

        $cotizacion = $this->cotizacionModel->find($id);
        if (!$cotizacion) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cotización no encontrada']);
        }

        // Si la cotización fue aceptada, mover lead a etapa CIERRE
        if ($estado === 'Aceptada') {
            $this->leadModel->update($cotizacion['idlead'], ['idetapa' => 5]); // Etapa CIERRE
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado']);
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

    /**
     * Buscar leads para Select2 (AJAX)
     */
    public function buscarLeads()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['results' => []]);
        }

        $searchTerm = $this->request->getGet('q') ?? '';
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Obtener ID de usuario
        $userId = session()->get('idusuario') ?: session()->get('user_id');
        
        if (!$userId) {
            return $this->response->setJSON(['results' => []]);
        }

        $builder = $this->leadModel
            ->select('leads.idlead, 
                     CONCAT(personas.nombres, " ", personas.apellidos) as text,
                     personas.telefono,
                     personas.dni,
                     etapas.nombre as etapa')
            ->join('personas', 'leads.idpersona = personas.idpersona')
            ->join('etapas', 'leads.idetapa = etapas.idetapa', 'left')
            ->where('leads.estado', 'Activo')
            ->where('leads.idusuario', $userId);

        // Búsqueda
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('personas.nombres', $searchTerm)
                ->orLike('personas.apellidos', $searchTerm)
                ->orLike('personas.telefono', $searchTerm)
                ->orLike('personas.dni', $searchTerm)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        
        $leads = $builder
            ->orderBy('leads.created_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        // Formatear para Select2
        $results = array_map(function($lead) {
            return [
                'id' => $lead['idlead'],
                'text' => $lead['text'] . ' - ' . $lead['telefono'],
                'telefono' => $lead['telefono'],
                'dni' => $lead['dni'],
                'etapa' => $lead['etapa']
            ];
        }, $leads);

        return $this->response->setJSON([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
}
