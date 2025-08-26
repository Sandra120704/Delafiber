<?php
namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\DistritoModel;

use CodeIgniter\Controller;

class PersonaController extends BaseController
{
    protected $personaModel;
    protected $distritoModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
        $this->distritoModel = new DistritoModel();
    }

    // Listar personas
    public function index()
    {
        $personas = $this->personaModel->listarPersonasSP();
        $distritos = $this->distritoModel->findAll();

        return view('personas/index', [
        'personas' => $personas,
        'distritos' => $distritos 
      ]);
    }

    // Mostrar formulario para registrar
    public function crear()
    {
        $db = \Config\Database::connect();
        $distritos = $db->query("SELECT * FROM distritos")->getResultArray();

        return view('personas/create', ['distritos' => $distritos]);
    }

    // Guardar persona nueva
    public function store()
    {
        $data = $this->request->getPost();
        $idNuevaPersona = $this->personaModel->registrarPersona($data);

        if ($idNuevaPersona) {
            return redirect()->to('/personas')->with('success', 'Persona registrada exitosamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al registrar la persona.');
        }
    }

    private function obtenerDistritos()
    {
        $distritoModel = new DistritoModel();
        return $distritoModel->findAll();
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $data['persona'] = $this->personaModel->obtenerPersonaPorId($id);
        $data['distritos'] = $this->obtenerDistritos();
        return view('personas/editar', $data);
    }

    // Actualizar persona
    public function update()
    {
        $data = $this->request->getPost();
        $id = $data['idpersona'];
        unset($data['idpersona']); // opcional
        $this->personaModel->actualizarPersona($id, $data);

        return redirect()->to('/personas')->with('success', 'Persona actualizada exitosamente.');
    }


    // Eliminar persona
    public function delete($id)
    {
        $this->personaModel->eliminarPersona($id);
        return redirect()->to('/personas')->with('success', 'Persona eliminada exitosamente.');
    }
    
    public function formularioEditar($idpersona)
    {
        $personaModel = new PersonaModel();
        $persona = $personaModel->find($idpersona);

        $distritoModel = new DistritoModel();
        $distritos = $distritoModel->findAll();

        if (!$persona) {
            return "Persona no encontrada";
        }

        return view('personas/editar', [
            'persona' => $persona,
            'distritos' => $distritos,
        ]);
    }



}
