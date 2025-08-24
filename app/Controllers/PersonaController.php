<?php
namespace App\Controllers;

use App\Models\PersonaModel;

use CodeIgniter\Controller;

class PersonaController extends BaseController
{
    protected $personaModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
    }

    // Listar personas
    public function index()
    {
        $data['personas'] = $this->personaModel->listarPersonasSP(); // ✅
        return view('personas/index', $data);
    }

    // Mostrar formulario para registrar
    public function crear()
    {
        return view('personas/crear');
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

    // Mostrar formulario de edición
    public function edit($id)
    {
        $data['persona'] = $this->personaModel->obtenerPersonaPorId($id);
        return view('personas/editar', $data);
    }

    // Actualizar persona
    public function update($id)
    {
        $data = $this->request->getPost();
        $this->personaModel->actualizarPersona($id, $data);

        return redirect()->to('/personas')->with('success', 'Persona actualizada exitosamente.');
    }

    // Eliminar persona
    public function delete($id)
    {
        $this->personaModel->eliminarPersona($id);
        return redirect()->to('/personas')->with('success', 'Persona eliminada exitosamente.');
    }
}
