<?php
namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\PersonaModel;
use CodeIgniter\Controller;

class LeadController extends BaseController
{
    public function index()
    {
      $model = new LeadModel();

      // Con este codigo obtenemos los leads
      $data['leads'] = $model->findAll();

      //Cargar la vista
      return view('leads/index', $data);
    }
    public function create()
    {
      return view('leads/create');
    }

  public function store()
    {
        $model = new LeadModel();

        $params = [
            'iddifusion' => $this->request->getPost('iddifusion'),
            'idpersona' => $this->request->getPost('idpersona'),
            'idusuarioregistro' => $this->request->getPost('idusuarioregistro'),
            'idusuarioresponsable' => $this->request->getPost('idusuarioresponsable'),
            'fechasignacion' => $this->request->getPost('fechasignacion'),
        ];

        $nuevoLeadID = $model->registrarLeadSP($params);

        if ($nuevoLeadID) {
            return redirect()->to('/leads')->with('success', 'Lead registrado correctamente. ID: ' . $nuevoLeadID);
        } else {
            return redirect()->back()->with('error', 'No se pudo registrar el lead. Verifica los datos.');
        }
    }
}