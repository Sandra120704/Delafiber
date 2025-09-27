public function store()
{
    // ...validación...
    $personaId = $this->personaModel->insert([
        'nombres' => $this->request->getPost('nombres'),
        'apellidos' => $this->request->getPost('apellidos'),
        'dni' => $this->request->getPost('dni'),
        'telefono' => $this->request->getPost('telefono'),
        'direccion' => $this->request->getPost('direccion'),
        'iddistrito' => $this->request->getPost('distrito')
    ]);
    $leadId = $this->leadModel->insert([
        'idpersona' => $personaId,
        'idusuario' => session('user_id'),
        'idorigen' => $this->request->getPost('origen'),
        'idcampania' => $this->request->getPost('campania'),
        'idetapa' => $this->request->getPost('etapa'),
        'observaciones' => $this->request->getPost('observaciones')
    ]);
    // ...redirección y mensaje...
}
