<?php
namespace App\Controllers;

use App\Models\LeadModel;

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

}
