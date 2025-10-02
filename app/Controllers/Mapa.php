<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\PersonaModel;
use App\Models\CampaniaModel;

class Mapa extends BaseController
{
    protected $leadModel;
    protected $personaModel;
    protected $campaniaModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->personaModel = new PersonaModel();
        $this->campaniaModel = new CampaniaModel();
    }

    /**
     * Vista principal del mapa
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Mapa Interactivo - Delafiber CRM'
        ];

        return view('mapa/index', $data);
    }

    /**
     * API: Obtener leads para el mapa
     */
    public function getLeadsParaMapa()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $db = \Config\Database::connect();
        
        // Obtener leads con coordenadas (dirección + distrito)
        $leads = $db->query("
            SELECT 
                l.idlead,
                l.estado,
                CONCAT(p.nombres, ' ', p.apellidos) as cliente,
                p.telefono,
                p.correo,
                p.direccion,
                d.nombre as distrito,
                prov.nombre as provincia,
                dept.nombre as departamento,
                e.nombre as etapa,
                o.nombre as origen,
                c.nombre as campania,
                l.fecha_registro,
                CONCAT(pu.nombres, ' ', pu.apellidos) as vendedor
            FROM leads l
            INNER JOIN personas p ON l.idpersona = p.idpersona
            LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
            LEFT JOIN provincias prov ON d.idprovincia = prov.idprovincia
            LEFT JOIN departamentos dept ON prov.iddepartamento = dept.iddepartamento
            LEFT JOIN etapas e ON l.idetapa = e.idetapa
            LEFT JOIN origenes o ON l.idorigen = o.idorigen
            LEFT JOIN campanias c ON l.idcampania = c.idcampania
            LEFT JOIN usuarios u ON l.idusuario = u.idusuario
            LEFT JOIN personas pu ON u.idpersona = pu.idpersona
            WHERE p.direccion IS NOT NULL 
            AND p.direccion != ''
            ORDER BY l.fecha_registro DESC
        ")->getResultArray();

        // Formatear datos para el mapa
        $marcadores = [];
        foreach ($leads as $lead) {
            $marcadores[] = [
                'id' => $lead['idlead'],
                'tipo' => 'lead',
                'cliente' => $lead['cliente'],
                'telefono' => $lead['telefono'],
                'correo' => $lead['correo'],
                'direccion' => $lead['direccion'],
                'distrito' => $lead['distrito'],
                'provincia' => $lead['provincia'],
                'departamento' => $lead['departamento'],
                'etapa' => $lead['etapa'],
                'origen' => $lead['origen'],
                'campania' => $lead['campania'],
                'vendedor' => $lead['vendedor'],
                'estado' => $lead['estado'] ?? 'Activo',
                'fecha_registro' => $lead['fecha_registro'],
                // Dirección completa para geocoding
                'direccion_completa' => $this->formatearDireccion($lead)
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'marcadores' => $marcadores
        ]);
    }

    /**
     * API: Obtener estadísticas por zona
     */
    public function getEstadisticasPorZona()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $db = \Config\Database::connect();
        
        // Estadísticas por distrito
        $estadisticas = $db->query("
            SELECT 
                d.nombre as distrito,
                COUNT(l.idlead) as total_leads,
                SUM(CASE WHEN l.estado = 'Convertido' THEN 1 ELSE 0 END) as convertidos,
                SUM(CASE WHEN l.estado IS NULL THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN l.estado = 'Descartado' THEN 1 ELSE 0 END) as descartados,
                ROUND(
                    SUM(CASE WHEN l.estado = 'Convertido' THEN 1 ELSE 0 END) * 100.0 / 
                    NULLIF(COUNT(l.idlead), 0), 
                    1
                ) as tasa_conversion
            FROM distritos d
            LEFT JOIN personas p ON p.iddistrito = d.iddistrito
            LEFT JOIN leads l ON l.idpersona = p.idpersona
            GROUP BY d.iddistrito
            HAVING total_leads > 0
            ORDER BY total_leads DESC
        ")->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * API: Obtener campañas por zona
     */
    public function getCampaniasPorZona()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $db = \Config\Database::connect();
        
        // Campañas activas con leads por distrito
        $campanias = $db->query("
            SELECT 
                c.idcampania,
                c.nombre as campania,
                c.descripcion,
                c.presupuesto,
                c.fecha_inicio,
                c.fecha_fin,
                d.nombre as distrito,
                COUNT(l.idlead) as leads_generados,
                SUM(CASE WHEN l.estado = 'Convertido' THEN 1 ELSE 0 END) as conversiones
            FROM campanias c
            LEFT JOIN leads l ON l.idcampania = c.idcampania
            LEFT JOIN personas p ON l.idpersona = p.idpersona
            LEFT JOIN distritos d ON p.iddistrito = d.iddistrito
            WHERE c.estado = 'Activa'
            AND d.nombre IS NOT NULL
            GROUP BY c.idcampania, d.iddistrito
            HAVING leads_generados > 0
            ORDER BY c.fecha_inicio DESC
        ")->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'campanias' => $campanias
        ]);
    }

    /**
     * API: Obtener zonas de cobertura
     */
    public function getZonasCobertura()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acceso no autorizado']);
        }

        $db = \Config\Database::connect();
        
        // Distritos con servicio (basado en leads convertidos)
        $zonas = $db->query("
            SELECT 
                d.nombre as distrito,
                COUNT(DISTINCT CASE WHEN l.estado = 'Convertido' THEN l.idlead END) as clientes_activos,
                COUNT(DISTINCT CASE WHEN l.estado IS NULL THEN l.idlead END) as leads_activos,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN l.estado = 'Convertido' THEN l.idlead END) > 10 THEN 'alta'
                    WHEN COUNT(DISTINCT CASE WHEN l.estado = 'Convertido' THEN l.idlead END) > 0 THEN 'media'
                    ELSE 'sin_cobertura'
                END as nivel_cobertura
            FROM distritos d
            LEFT JOIN personas p ON p.iddistrito = d.iddistrito
            LEFT JOIN leads l ON l.idpersona = p.idpersona
            GROUP BY d.iddistrito
            ORDER BY clientes_activos DESC
        ")->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'zonas' => $zonas
        ]);
    }

    /**
     * Helper: Formatear dirección completa
     */
    private function formatearDireccion($lead)
    {
        $partes = array_filter([
            $lead['direccion'],
            $lead['distrito'],
            $lead['provincia'],
            $lead['departamento'] ?? 'Lima'
        ]);

        return implode(', ', $partes);
    }
}
