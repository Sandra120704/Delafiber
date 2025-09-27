<?php

namespace App\Controllers\Dashboard;

use CodeIgniter\Controller;

class Index extends Controller
{
    public function index()
    {
        $resumen = [
            'tareas_vencidas' => 0, // Cambia por el valor real
            'leads_calientes' => 0, // Cambia por el valor real
            'total_leads' => 0,     // Cambia por el valor real
            'tareas_pendientes' => 0, // Cambia por el valor real
            'oportunidades_abiertas' => 0, // Cambia por el valor real
            'clientes' => 0,        // Cambia por el valor real
            'usuarios' => 0,        // Cambia por el valor real
            'productos' => 0,       // Cambia por el valor real
            'servicios' => 0,      // Cambia por el valor real
            'facturas' => 0,        // Cambia por el valor real
            'cotizaciones' => 0,    // Cambia por el valor real
            'pagos' => 0,           // Cambia por el valor real
            'gastos' => 0,          // Cambia por el valor real
            'proveedores' => 0,     // Cambia por el valor real
            'proyectos' => 0,       // Cambia por el valor real
            'conversiones_mes' => 0, // Cambia por el valor real
            
            
        ];
        $data = [
            'user_name' => session()->get('user_name'),
            'resumen' => $resumen,
        ];
        return view('dashboard/index', $data);
    }
}
