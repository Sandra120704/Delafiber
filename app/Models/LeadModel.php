<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'idpersona', 'idetapa', 'idusuario', 'idorigen', 'idcampania',
        'medio_comunicacion', 'idmodalidad', 'idusuario_registro',
        'referido_por', 'estado', 'numero_contrato_externo',
        'fecha_conversion_contrato'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_modificacion';

    /**
     * Obtiene leads nuevos que necesitan contacto inicial
     */
    public function getLeadsNuevos($userId, $limit = 5)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('distritos d', 'p.iddistrito = d.iddistrito')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono, p.correo, 
                     d.nombre as distrito, e.nombre as etapa, l.fecha_registro,
                     CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL') // Solo leads activos
            ->where('e.nombre', 'CAPTACION') // Solo en etapa inicial
            ->where('l.fecha_registro >=', date('Y-m-d H:i:s', strtotime('-24 hours'))) // Últimas 24 horas
            ->orderBy('l.fecha_registro', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene leads calientes en etapas avanzadas
     */
    public function getLeadsCalientes($userId, $limit = 8)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('distritos d', 'p.iddistrito = d.iddistrito')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono, p.correo,
                     d.nombre as distrito, e.nombre as etapa, l.fecha_registro,
                     CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL')
            ->whereIn('e.nombre', ['INTERES', 'COTIZACION', 'NEGOCIACION', 'CIERRE'])
            ->orderBy('e.orden', 'DESC') // Prioridad a etapas más avanzadas
            ->orderBy('l.fecha_modificacion', 'ASC') // Los que hace más tiempo no se actualizan
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene leads sin seguimiento reciente
     */
    public function getLeadsSinSeguimiento($userId, $dias = 2, $limit = 10)
    {
        $fechaLimite = date('Y-m-d H:i:s', strtotime("-$dias days"));
        
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono,
                     e.nombre as etapa, l.fecha_registro,
                     CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL')
            ->where('l.fecha_registro <=', $fechaLimite)
            ->where('l.idlead NOT IN', function($builder) use ($fechaLimite) {
                return $builder->select('s.idlead')
                    ->from('seguimiento s')
                    ->where('s.fecha >=', $fechaLimite);
            })
            ->orderBy('l.fecha_registro', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene información completa de un lead
     */
    public function getLeadCompleto($leadId)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('origenes o', 'l.idorigen = o.idorigen')
            ->join('distritos d', 'p.iddistrito = d.iddistrito', 'LEFT')
            ->join('provincias pr', 'd.idprovincia = pr.idprovincia', 'LEFT')
            ->join('campanias c', 'l.idcampania = c.idcampania', 'LEFT')
            ->select('l.*, p.nombres, p.apellidos, p.dni, p.telefono, p.correo, 
                     p.direccion, p.referencias, e.nombre as etapa_nombre,
                     o.nombre as origen_nombre, d.nombre as distrito_nombre,
                     pr.nombre as provincia_nombre, c.nombre as campania_nombre')
            ->where('l.idlead', $leadId)
            ->get()
            ->getRowArray();
    }

    /**
     * Obtiene estadísticas de leads por usuario
     */
    public function getEstadisticasPorUsuario($userId)
    {
        // Total de leads
        $total = $this->where('idusuario', $userId)
                     ->where('estado IS NULL')
                     ->countAllResults();

        // Leads convertidos este mes
        $convertidos = $this->where('idusuario', $userId)
                          ->where('estado', 'Convertido')
                          ->where('MONTH(fecha_conversion_contrato)', date('m'))
                          ->where('YEAR(fecha_conversion_contrato)', date('Y'))
                          ->countAllResults();

        // Leads por etapa
        $porEtapa = $this->db->table('leads l')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->select('e.nombre as etapa, COUNT(*) as total')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL')
            ->groupBy('e.idetapa, e.nombre')
            ->orderBy('e.orden')
            ->get()
            ->getResultArray();

        // Tasa de conversión
        $totalHistorico = $this->where('idusuario', $userId)->countAllResults();
        $convertidosHistorico = $this->where('idusuario', $userId)
                                   ->where('estado', 'Convertido')
                                   ->countAllResults();
        
        $tasaConversion = $totalHistorico > 0 ? 
            round(($convertidosHistorico / $totalHistorico) * 100, 1) : 0;

        return [
            'total_activos' => $total,
            'convertidos_mes' => $convertidos,
            'por_etapa' => $porEtapa,
            'tasa_conversion' => $tasaConversion,
            'total_historico' => $totalHistorico,
            'convertidos_historico' => $convertidosHistorico
        ];
    }

    /**
     * Obtiene leads próximos a vencer (sin actividad)
     */
    public function getLeadsProximosVencer($userId, $dias = 3, $limit = 5)
    {
        $fechaLimite = date('Y-m-d H:i:s', strtotime("-$dias days"));
        
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono,
                     e.nombre as etapa, l.fecha_registro,
                     DATEDIFF(NOW(), l.fecha_modificacion) as dias_sin_actividad,
                     CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL')
            ->where('l.fecha_modificacion <=', $fechaLimite)
            ->whereNotIn('e.nombre', ['VENTA', 'DESCARTADO'])
            ->orderBy('l.fecha_modificacion', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtiene el pipeline completo del usuario
     */
    public function getPipelineUsuario($userId)
    {
        return $this->db->table('etapas e')
            ->join('leads l', 'e.idetapa = l.idetapa AND l.idusuario = ' . $userId . ' AND l.estado IS NULL', 'LEFT')
            ->join('personas p', 'l.idpersona = p.idpersona', 'LEFT')
            ->select('e.idetapa, e.nombre as etapa, e.orden,
                     COUNT(l.idlead) as total_leads,
                     GROUP_CONCAT(
                         DISTINCT CONCAT(l.idlead, "|", p.nombres, " ", p.apellidos, "|", p.telefono)
                         SEPARATOR ";"
                     ) as leads_info')
            ->groupBy('e.idetapa, e.nombre, e.orden')
            ->orderBy('e.orden')
            ->get()
            ->getResultArray();
    }

    /**
     * Mueve un lead a la siguiente etapa
     */
    public function avanzarEtapa($leadId, $usuarioId)
    {
        $lead = $this->find($leadId);
        if (!$lead) return false;

        // Obtener siguiente etapa
        $siguienteEtapa = $this->db->table('etapas')
            ->where('idpipeline', 1) // Pipeline principal
            ->where('orden >', function($builder) use ($lead) {
                return $builder->select('e2.orden')
                    ->from('etapas e2')
                    ->where('e2.idetapa', $lead['idetapa']);
            })
            ->orderBy('orden', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$siguienteEtapa) return false;

        // Actualizar lead
        $this->update($leadId, [
            'idetapa' => $siguienteEtapa['idetapa']
        ]);

        // Registrar en historial
        $this->registrarCambioEtapa($leadId, $usuarioId, $lead['idetapa'], $siguienteEtapa['idetapa']);

        return true;
    }

    /**
     * Registra cambio de etapa en el historial
     */
    private function registrarCambioEtapa($leadId, $usuarioId, $etapaAnterior, $etapaNueva)
    {
        $this->db->table('leads_historial')->insert([
            'idlead' => $leadId,
            'idusuario' => $usuarioId,
            'accion' => 'cambio_etapa',
            'descripcion' => 'Lead avanzó de etapa',
            'etapa_anterior' => $etapaAnterior,
            'etapa_nueva' => $etapaNueva,
            'fecha' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca leads por criterios múltiples
     */
    public function buscarLeads($criterios, $userId = null)
    {
        $builder = $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('origenes o', 'l.idorigen = o.idorigen')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono, p.correo,
                     e.nombre as etapa, o.nombre as origen, l.fecha_registro,
                     CONCAT(p.nombres, " ", p.apellidos) as cliente_nombre');

        if ($userId) {
            $builder->where('l.idusuario', $userId);
        }

        if (!empty($criterios['texto'])) {
            $texto = $criterios['texto'];
            $builder->groupStart()
                ->like('p.nombres', $texto)
                ->orLike('p.apellidos', $texto)
                ->orLike('p.telefono', $texto)
                ->orLike('p.correo', $texto)
                ->groupEnd();
        }

        if (!empty($criterios['etapa'])) {
            $builder->where('l.idetapa', $criterios['etapa']);
        }

        if (!empty($criterios['origen'])) {
            $builder->where('l.idorigen', $criterios['origen']);
        }

        if (!empty($criterios['estado'])) {
            if ($criterios['estado'] === 'activo') {
                $builder->where('l.estado IS NULL');
            } else {
                $builder->where('l.estado', $criterios['estado']);
            }
        }

        if (!empty($criterios['fecha_desde'])) {
            $builder->where('l.fecha_registro >=', $criterios['fecha_desde']);
        }

        if (!empty($criterios['fecha_hasta'])) {
            $builder->where('l.fecha_registro <=', $criterios['fecha_hasta']);
        }

        return $builder->orderBy('l.fecha_registro', 'DESC')->get()->getResultArray();
    }

    /**
     * Obtiene leads para exportar
     */
    public function getLeadsParaExportar($userId = null, $filtros = [])
    {
        $builder = $this->db->table('vista_leads_completa');
        
        if ($userId) {
            $builder->where('vendedor_asignado LIKE', '%' . $userId . '%'); // Buscar en el nombre concatenado
        }

        // Aplicar filtros adicionales
        if (!empty($filtros['etapa'])) {
            $builder->where('etapa_actual', $filtros['etapa']);
        }

        if (!empty($filtros['estado'])) {
            $builder->where('estado', $filtros['estado']);
        }

        return $builder->orderBy('fecha_registro', 'DESC')->get()->getResultArray();
    }
}