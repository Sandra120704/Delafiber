<?php
namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\DistritoModel;

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
        $data = [
            'personas'  => $this->personaModel->listarPersonasSP(),
            'distritos' => $this->distritoModel->findAll()
        ];
        return view('personas/index', $data);
    }

    // Formulario nuevo
    public function crear()
    {
        return view('personas/create', [
            'distritos' => $this->distritoModel->findAll()
        ]);
    }

    // Guardar persona nueva
    public function store()
    {
        $data = $this->request->getPost();
        $idNuevaPersona = $this->personaModel->registrar($data);

        return $idNuevaPersona
            ? redirect()->to('/personas')->with('success', 'Persona registrada exitosamente.')
            : redirect()->back()->withInput()->with('error', 'Error al registrar.');
    }

    // Formulario editar
    public function edit($id)
    {
        $data = [
            'persona'   => $this->personaModel->obtener($id),
            'distritos' => $this->distritoModel->findAll()
        ];
        return view('personas/editar', $data);
    }

    // Actualizar
    public function update()
    {
        $data = $this->request->getPost();
        $id   = $data['idpersona'];

        $this->personaModel->actualizarPersona($id, $data);

        return redirect()->to('/personas')->with('success', 'Persona actualizada exitosamente.');
    }

    // Eliminar
    public function delete($id)
    {
        $this->personaModel->eliminarPersona($id);
        return redirect()->to('/personas')->with('success', 'Persona eliminada exitosamente.');
    }
}
