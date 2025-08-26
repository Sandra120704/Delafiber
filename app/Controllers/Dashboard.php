<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $personas = [
                ['idpersona'=>1,'nombres'=>'Juan','apellidos'=>'Perez','email'=>'juan@gmail.com'],
                ['idpersona'=>2,'nombres'=>'Ana','apellidos'=>'Lopez','email'=>'ana@gmail.com']
            ];
        $Leads = [
            ['idlead'=>1,'nombre'=>'Lead 1','email'=>'lead1@gmail.com'],
            ['idlead'=>2,'nombre'=>'Lead 2','email'=>'lead2@gmail.com']
        ];

        $data = [
            'totalPersonas' => count($personas),
            'totalLeads' => count($Leads),
            'ultimasPersonas' => $personas,
            'ultimosLeads' => $Leads
        ];

        return view('dashboard/index', $data);
    }
}
