<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\TareaModel;
use App\Models\CampaniaModel;

class Reportes extends BaseController
{
    protected $leadModel;
    protected $tareaModel;
    protected $campaniaModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->tareaModel = new TareaModel();
        $this->campaniaModel = new CampaniaModel();
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        try {
            // KPIs básicos
            $totalLeads = $this->leadModel->countAll();
            $leadsMes = $this->leadModel
                ->where('DATE(fecha_registro) >=', date('Y-m-01'))
                ->countAllResults();
            
            $kpis = [
                'total_leads' => $totalLeads,
                'leads_mes' => $leadsMes,
                'variacion_leads' => 0, // Calcular variación vs mes anterior
                'conversiones' => 0, // Leads convertidos
                'tasa_conversion' => 0, // Porcentaje de conversión
                'ingresos' => 0, // Ingresos estimados
                'ticket_promedio' => 0, // Ticket promedio
                'tareas_pendientes' => $this->tareaModel
                    ->where('estado', 'Pendiente')
                    ->countAllResults(),
                'campanias_activas' => $this->campaniaModel
                    ->where('estado', 'Activa')
                    ->countAllResults()
            ];

            $data = [
                'title' => 'Reportes - Delafiber CRM',
                'kpis' => $kpis
            ];

            return view('reportes/test', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error en Reportes::index: ' . $e->getMessage());
            
            $data = [
                'title' => 'Reportes - Delafiber CRM',
                'kpis' => [
                    'total_leads' => 0,
                    'leads_mes' => 0,
                    'variacion_leads' => 0,
                    'conversiones' => 0,
                    'tasa_conversion' => 0,
                    'ingresos' => 0,
                    'ticket_promedio' => 0,
                    'tareas_pendientes' => 0,
                    'campanias_activas' => 0
                ]
            ];
            
            return view('reportes/test', $data);
        }
    }

    public function exportar()
    {
        // Método para exportar reportes
        return redirect()->back()->with('info', 'Función de exportación en desarrollo');
    }
}
