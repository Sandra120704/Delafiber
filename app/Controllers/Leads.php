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

        $campanias = $this->campaniaModel->findAll();  

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

        return view('leads/index', $data);  
    }  

    // 📌 Formulario nuevo lead  
    public function create()  
    {  
        $data = [  
            'title' => 'Nuevo Lead - Delafiber CRM',  
            'distritos' => $this->distritoModel->getDistritosDelafiber(),  
            'origenes' => $this->origenModel->getOrigenesActivos(),  
            'campanias' => $this->campaniaModel->getCampaniasActivas(),  
            'etapas' => $this->etapaModel->getEtapasActivas(),  
            'modalidades' => $this->modalidadModel->getModalidadesActivas(),  
            'user_name' => session()->get('user_name')  
        ];  

        return view('leads/create', $data);  
    }  

    // 📌 Guardar nuevo lead  
    public function store()  
    {  
        $rules = [  
            'nombres' => 'required|min_length[2]',  
            'apellidos' => 'required|min_length[2]',  
            'telefono' => 'required|exact_length[9]|numeric',  
            'origen' => 'required|numeric'  
        ];  

        if (!$this->validate($rules)) {  
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
                'idetapa' => 1, // CAPTACIÓN (mejor usar método getEtapaInicial())  
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
            log_message('error', '[LEADS STORE] ' . $e->getMessage());  

            return redirect()->back()  
                ->withInput()  
                ->with('error', 'Error al crear el lead: ' . $e->getMessage());  
        }  
    }  

    // 📌 Ver lead  
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

    // 📌 Buscar lead por DNI  
    public function buscarPorDni()  
    {  
        $dni = $this->request->getGet('dni');  

        if (!$dni) {  
            return $this->response->setJSON([  
                'success' => false,  
                'message' => 'DNI no proporcionado'  
            ]);  
        }  

        $lead = $this->leadModel  
            ->select('leads.*, personas.*')  
            ->join('personas', 'personas.idpersona = leads.idpersona')  
            ->where('personas.dni', $dni)  
            ->first();  

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

    // 📌 Mover lead de etapa con historial  
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
            $lead = $this->leadModel->find($idlead);  
            $etapaAnterior = $lead->idetapa;  

            $this->leadModel->update($idlead, ['idetapa' => $idetapa]);  

            $db = \Config\Database::connect();  
            $db->table('leads_historial')->insert([  
                'idlead' => $idlead,  
                'idusuario' => session()->get('user_id'), // ✅ corregido  
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
            log_message('error', '[LEADS MOVER ETAPA] ' . $e->getMessage());  

            return $this->response->setJSON([  
                'success' => false,  
                'message' => 'Error al mover el lead: ' . $e->getMessage()  
            ]);  
        }  
    }  
}  
