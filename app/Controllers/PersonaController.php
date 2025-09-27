<?php

namespace App\Controllers;

use App\Models\PersonaModel;

class PersonaController extends BaseController
{
  public function index(): string
  {
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
  $model = new PersonaModel();
    $datos['personas'] = $model->findAll();
    return view('Personas/index', $datos);
  }

  public function create()
  {
    $model = new PersonaModel();
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    if ($this->request->getMethod() === 'post') {
      $model->insert($this->request->getPost());
      return redirect()->to('/personas');
    }
    return view('Personas/create', $datos);
  }

  public function edit($id = null)
  {
    $model = new PersonaModel();
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    $datos['persona'] = $model->find($id);
    if ($this->request->getMethod() === 'post') {
      $model->update($id, $this->request->getPost());
      return redirect()->to('/personas');
    }
    return view('Personas/edit', $datos);
  }

  public function delete($id = null)
  {
    $model = new PersonaModel();
    $model->delete($id);
    return redirect()->to('/personas');
  }
}
