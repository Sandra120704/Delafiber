<?php

namespace App\Controllers;

class Reportes extends BaseController
{
    public function index()
    {
        // Ejemplo de KPIs para evitar error de variable indefinida
        $kpis = [
            'total_leads' => 0,
            'variacion_leads' => 0,
            // Puedes agregar más KPIs según tu lógica
        ];
        return view('Reportes/index', compact('kpis'));
    }
}
