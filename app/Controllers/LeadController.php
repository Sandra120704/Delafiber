<?php
namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\PersonaModel;
use App\Models\SeguimientoModel;

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

    public function convertir($idpersona)
    {
        $leadModel = new LeadModel();
        $personaModel = new PersonaModel();
        $seguimientoModel = new SeguimientoModel();

        // Verificar si la persona existe
        $persona = $personaModel->find($idpersona);

        if (!$persona) {
            return redirect()->to('/personas')->with('error', 'Persona no encontrada.');
        }

         // Si ya es lead, redirige a su ficha o edición
        $leadExistente = $leadModel->where('idpersona', $idpersona)->first();
        if ($leadExistente) {
            return redirect()->to('/leads/create/' . $leadExistente['idlead'])
                             ->with('info', 'Esta persona ya es un lead. Puedes editarlo.');
       }
        // Crear nuevo lead
       $params = [
          'iddifusion' => 1,
          'idpersona' => $idpersona,
          'idusuarioregistro' => 1,
          'idusuarioresponsable' => 1,
          'fechasignacion' => date('Y-m-d'),
        ];

      $nuevoLeadID = $leadModel->insert($params, true);
      if ($nuevoLeadID) {
         // Crear seguimiento
         $seguimientoModel->insert([
         'idlead' => $nuevoLeadID,
         'idetapa' => 1,
         'modalidadcontacto' => 'registro',
         'fecha' => date('Y-m-d'),
         'hora' => date('H:i:s'),
         'comentarios' => 'Lead creado automáticamente.',
         'idusuario' => 1,
         'estado' => 1,
         ]);

      $personaModel->update($idpersona, ['es_lead' => 1]);

       return redirect()->to('/leads/edit/' . $nuevoLeadID)
                        ->with('success', 'Persona convertida en lead exitosamente.');
      }
      return redirect()->back()->with('error', 'No se pudo convertir la persona en lead.');
   }
   public function edit($id)
  {
      $leadModel = new LeadModel();
      $lead = $leadModel->find($id);

      if (!$lead) {
          return redirect()->to('/leads')->with('error', 'Lead no encontrado.');
      }

      return view('leads/edit', ['lead' => $lead]);
  }

  public function update($id)
  {
      $leadModel = new LeadModel();
      $data = $this->request->getPost();

      $leadModel->update($id, $data);

      return redirect()->to('/leads')->with('success', 'Lead actualizado correctamente.');
  }


}