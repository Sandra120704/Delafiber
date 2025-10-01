<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CampaniaModel;
use App\Models\PersonaModel;
use App\Models\LeadModel;
use App\Models\SeguimientoModel;
use App\Models\TareaModel;
use App\Models\OrigenModel;
use App\Models\ModalidadModel;
use App\Models\EtapaModel;
use App\Models\DistritoModel;

class Leads extends BaseController
{
    protected $leadModel;
    protected $personaModel;
    protected $seguimientoModel;
    protected $tareaModel;
    protected $campaniaModel;
    protected $origenModel;
    protected $modalidadModel;
    protected $etapaModel;
    protected $distritoModel; 

    public function __construct()
    {
        // Verificar que esté logueado
        if (!session()->get('logged_in')) {
            // No retornes aquí, solo redirige
            redirect()->to('/auth')->send();
            exit;
        }
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->seguimientoModel = new SeguimientoModel();
        $this->tareaModel = new TareaModel();
        $this->campaniaModel = new CampaniaModel(); 
        $this->origenModel = new OrigenModel();
        $this->modalidadModel = new ModalidadModel();
        $this->etapaModel = new EtapaModel();
        $this->distritoModel = new DistritoModel();
    }

    // Lista de leads con filtros
    public function index()
    {
        $userId = session()->get('user_id');
        $filtro_etapa = $this->request->getGet('etapa');
        $filtro_origen = $this->request->getGet('origen');
        $filtro_busqueda = $this->request->getGet('buscar');
        $leads = $this->leadModel->getLeadsConFiltros($userId, [
            'etapa' => $filtro_etapa,
            'origen' => $filtro_origen,
            'busqueda' => $filtro_busqueda
        ]);
        // Obtener campañas desde el modelo
        $campaignsModel = new CampaniaModel(); // Usa el modelo correcto
        $campanias = $campaignsModel->findAll();

        $data = [
            'title' => 'Mis Leads - Delafiber CRM',
            'leads' => $leads,
            'total_leads' => count($leads),
            'etapas' => $this->etapaModel->getEtapasActivas(),
            'origenes' => $this->origenModel->getOrigenesActivos(),
            'filtro_etapa' => $filtro_etapa,
            'filtro_origen' => $filtro_origen,
            'filtro_busqueda' => $filtro_busqueda,
            'user_name' => session()->get('user_name'),
            'campanias' => $campanias,
        ];
        return view('leads/index', $data); // leads/index.php debe tener extend('Layouts/header'), section('content'), endSection()
    }
      public function create()   
      {
        // Obtén solo los datos relevantes y ordenados
        $distritos = $this->distritoModel->getDistritosDelafiber();
        $origenes = $this->origenModel->getOrigenesActivos();
        $campanias = $this->campaniaModel->getCampaniasActivas(); // Usa el método filtrado si lo tienes
        $etapas = $this->etapaModel->getEtapasActivas();
        $modalidades = $this->modalidadModel->getModalidadesActivas(); // Agregar modalidades
    
        $data = [
            'title' => 'Nuevo Lead - Delafiber CRM',
            'distritos' => $distritos,
            'origenes' => $origenes,
            'campanias' => $campanias,
            'etapas' => $etapas,
            'modalidades' => $modalidades, 
            'user_name' => session()->get('user_name')
        ];
    
        return view('leads/create', $data);
    }
// Guardar nuevo lead con validación y transacción
public function store()
{
    $rules = [
            'nombres' => 'required|min_length[2]',
            'apellidos' => 'required|min_length[2]',
            'telefono' => 'required|min_length[9]|max_length[9]',
            'origen' => 'required|numeric'
        ];
        $messages = [
            'nombres' => [
                'required' => 'Los nombres son obligatorios',
                'min_length' => 'Los nombres deben tener al menos 2 caracteres'
            ],
            'apellidos' => [
                'required' => 'Los apellidos son obligatorios',
                'min_length' => 'Los apellidos deben tener al menos 2 caracteres'
            ],
            'telefono' => [
                'required' => 'El teléfono es obligatorio',
                'min_length' => 'El teléfono debe tener 9 dígitos',
                'max_length' => 'El teléfono debe tener 9 dígitos'
            ],
            'origen' => [
                'required' => 'Debes seleccionar el origen del lead'
            ]
        ];
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $personaData = [
                'nombres' => $this->request->getPost('nombres'),
                'apellidos' => $this->request->getPost('apellidos'),
                'dni' => $this->request->getPost('dni'),
                'correo' => $this->request->getPost('correo'),
                'telefono' => $this->request->getPost('telefono'),
                'direccion' => $this->request->getPost('direccion'),
                'referencias' => $this->request->getPost('referencias'),
                'iddistrito' => $this->request->getPost('distrito')
            ];
            $personaId = $this->personaModel->insert($personaData);
            if (!$personaId) throw new \Exception('Error al crear la persona');
            $leadData = [
                'idpersona' => $personaId,
                'idetapa' => 1, // CAPTACION
                'idusuario' => session()->get('user_id'),
                'idorigen' => $this->request->getPost('origen'),
                'medio_comunicacion' => $this->request->getPost('medio_comunicacion'),
                'idusuario_registro' => session()->get('user_id')
            ];
            $leadId = $this->leadModel->insert($leadData);
            if (!$leadId) throw new \Exception('Error al crear el lead');
            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception('Error en la transacción');
            $nombreCompleto = $personaData['nombres'] . ' ' . $personaData['apellidos'];
            return redirect()->to('/leads')
                ->with('success', "Lead '$nombreCompleto' creado exitosamente");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el lead: ' . $e->getMessage());
        }
    }

    // Ver detalles de un lead
    public function view($leadId)
    {
        $userId = session()->get('user_id');
        $lead = $this->leadModel->getLeadCompleto($leadId, $userId);
        if (!$lead) {
            return redirect()->to('/leads')
                ->with('error', 'Lead no encontrado');
        }
        $data = [
            'title' => 'Lead: ' . $lead['nombres'] . ' ' . $lead['apellidos'],
            'lead' => $lead,
            'historial' => $this->leadModel->getHistorialLead($leadId),
            'tareas' => $this->leadModel->getTareasLead($leadId),
            'user_name' => session()->get('user_name')
        ];
        return view('leads/view', $data);
    }

    // Buscar lead por teléfono (AJAX)
    public function buscarPorTelefono()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }
        $telefono = $this->request->getPost('telefono');
        if (!$telefono || strlen($telefono) < 9) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Teléfono inválido'
            ]);
        }
        $lead = $this->leadModel->buscarPorTelefono($telefono);
        if ($lead) {
            return $this->response->setJSON([
                'success' => true,
                'existe' => true,
                'lead' => $lead,
                'message' => 'Este teléfono ya está registrado'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'existe' => false,
                'message' => 'Teléfono disponible'
            ]);
        }
    }

    // Pipeline visual (vista Kanban)
    public function pipeline()
    {
        $userId = session()->get('user_id');
        $pipeline = $this->leadModel->getPipelineUsuario($userId);
        $data = [
            'title' => 'Pipeline de Ventas - Delafiber CRM',
            'pipeline' => $pipeline,
            'user_name' => session()->get('user_name')
        ];
        return view('leads/pipeline', $data);
    }

    // Actualizar etapa de lead (AJAX para Kanban)
    public function updateEtapa()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }
        $idlead = $this->request->getPost('idlead');
        $idetapa = $this->request->getPost('idetapa');
        if ($idlead && $idetapa) {
            $this->leadModel->update($idlead, ['idetapa' => $idetapa]);
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Datos inválidos']);
    }

    // Mover lead a otra etapa (con historial)
    public function moverEtapa()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }
        
        $idlead = $this->request->getPost('idlead');
        $idetapa = $this->request->getPost('idetapa');
        
        if (!$idlead || !$idetapa) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos inválidos'
            ]);
        }
        
        try {
            // Obtener etapa anterior
            $lead = $this->leadModel->find($idlead);
            $etapaAnterior = $lead['idetapa'];
            
            // Actualizar etapa
            $this->leadModel->update($idlead, ['idetapa' => $idetapa]);
            
            // Registrar en historial (opcional)
            $db = \Config\Database::connect();
            $db->table('leads_historial')->insert([
                'idlead' => $idlead,
                'idusuario' => session()->get('idusuario'),
                'accion' => 'cambio_etapa',
                'descripcion' => 'Lead movido de etapa',
                'etapa_anterior' => $etapaAnterior,
                'etapa_nueva' => $idetapa,
                'fecha' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead movido exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al mover el lead: ' . $e->getMessage()
            ]);
        }
    }

    public function update($idlead)
    {
        $rules = [
            'nombres' => 'required|min_length[2]',
            'apellidos' => 'required|min_length[2]',
            'telefono' => 'required|min_length[9]|max_length[9]',
            'origen' => 'required|numeric'
        ];
        $messages = [
            'nombres' => [
                'required' => 'Los nombres son obligatorios',
                'min_length' => 'Los nombres deben tener al menos 2 caracteres'
            ],
            'apellidos' => [
                'required' => 'Los apellidos son obligatorios',
                'min_length' => 'Los apellidos deben tener al menos 2 caracteres'
            ],
            'telefono' => [
                'required' => 'El teléfono es obligatorio',
                'min_length' => 'El teléfono debe tener 9 dígitos',
                'max_length' => 'El teléfono debe tener 9 dígitos'
            ],
            'origen' => [
                'required' => 'Debes seleccionar el origen del lead'
            ]
        ];
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $lead = $this->leadModel->find($idlead);
        if (!$lead) {
            return redirect()->to('/leads')->with('error', 'Lead no encontrado');
        }

        $personaId = $lead['idpersona'];
        $personaData = [
            'nombres' => $this->request->getPost('nombres'),
            'apellidos' => $this->request->getPost('apellidos'),
            'dni' => $this->request->getPost('dni'),
            'correo' => $this->request->getPost('correo'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
            'referencias' => $this->request->getPost('referencias'),
            'iddistrito' => $this->request->getPost('distrito')
        ];
        $leadData = [
            'idorigen' => $this->request->getPost('origen'),
            'medio_comunicacion' => $this->request->getPost('medio_comunicacion'),
            'observaciones' => $this->request->getPost('observaciones')
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $this->personaModel->update($personaId, $personaData);
            $this->leadModel->update($idlead, $leadData);
            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception('Error en la transacción');
            return redirect()->to('/leads/view/' . $idlead)
                ->with('success', 'Lead actualizado correctamente');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el lead: ' . $e->getMessage());
        }
    }

    /**
     * Buscar lead por DNI (AJAX/API Decolecta)
     */
    public function buscarPorDni()
    {
        $dni = $this->request->getGet('dni');
        if (!$dni) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'DNI no proporcionado'
            ]);
        }

        // Ejemplo: consulta a modelo o API externa
        $lead = model('LeadModel')->where('dni', $dni)->first();

        if ($lead) {
            return $this->response->setJSON([
                'success' => true,
                'lead' => $lead
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se encontró lead con ese DNI'
            ]);
        }
    }
}

