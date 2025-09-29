<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table = 'leads';
    protected $primaryKey = 'idlead';
    protected $allowedFields = [
        'idpersona',
        'idetapa',
        'idusuario',
        'idorigen',
        'idcampania',
        'medio_comunicacion',
        'idmodalidad',
        'idusuario_registro',
        'referido_por',
        'estado',
        'numero_contrato_externo',
        'fecha_conversion_contrato',
        'fecha_registro'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = 'fecha_modificacion';
    
    protected $validationRules = [
        'idpersona' => 'required|numeric',
        'idetapa' => 'required|numeric',
        'idusuario' => 'required|numeric',
        'idorigen' => 'required|numeric'
    ];

    /**
     * Obtener leads con filtros - SIMPLE
     */
    public function getLeadsConFiltros($userId, $filtros = [])
    {
        $builder = $this->db->table('leads')
            ->select('leads.*, personas.nombres, personas.apellidos')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->where('leads.idusuario', $userId);

        if (!empty($filtros['etapa'])) {
            $builder->where('leads.idetapa', $filtros['etapa']);
        }
        if (!empty($filtros['origen'])) {
            $builder->where('leads.idorigen', $filtros['origen']);
        }
        if (!empty($filtros['busqueda'])) {
            $builder->groupStart()
                ->like('persona.nombres', $filtros['busqueda'])
                ->orLike('persona.apellidos', $filtros['busqueda'])
                ->orLike('persona.telefono', $filtros['busqueda'])
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Obtener lead completo por ID
     */
    public function getLeadCompleto($leadId, $userId = null)
    {
        $builder = $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('origenes o', 'l.idorigen = o.idorigen')
            ->join('distritos d', 'p.iddistrito = d.iddistrito', 'LEFT')
            ->join('provincias pr', 'd.idprovincia = pr.idprovincia', 'LEFT')
            ->select('l.*, p.nombres, p.apellidos, p.dni, p.telefono, p.correo,
                     p.direccion, p.referencias, e.nombre as etapa_nombre,
                     o.nombre as origen_nombre, d.nombre as distrito_nombre,
                     pr.nombre as provincia_nombre')
            ->where('l.idlead', $leadId);

        // Si se especifica userId, verificar que le pertenezca
        if ($userId) {
            $builder->where('l.idusuario', $userId);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Buscar lead por teléfono
     */
    public function buscarPorTelefono($telefono)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono,
                     e.nombre as etapa_nombre, l.fecha_registro')
            ->where('p.telefono', $telefono)
            ->where('l.estado IS NULL')
            ->orderBy('l.fecha_registro', 'DESC')
            ->get()
            ->getRowArray();
    }

    /**
     * Obtener historial de un lead
     */
    public function getHistorialLead($leadId)
    {
        return $this->db->table('seguimiento s')
            ->join('modalidades m', 's.idmodalidad = m.idmodalidad')
            ->join('usuarios u', 's.idusuario = u.idusuario')
            ->join('personas p', 'u.idpersona = p.idpersona')
            ->select('s.*, m.nombre as modalidad_nombre,
                     CONCAT(p.nombres, " ", p.apellidos) as usuario_nombre')
            ->where('s.idlead', $leadId)
            ->orderBy('s.fecha', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener tareas de un lead
     */
    public function getTareasLead($leadId)
    {
        return $this->db->table('tareas')
            ->where('idlead', $leadId)
            ->orderBy('fecha_vencimiento', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener pipeline del usuario - SIMPLE
     */
    public function getPipelineUsuario($userId)
    {
        // Obtener todas las etapas
        $etapas = $this->db->table('etapas')
            ->orderBy('orden')
            ->get()
            ->getResultArray();

        $pipeline = [];

        foreach ($etapas as $etapa) {
            // Contar leads en esta etapa
            $totalLeads = $this->db->table('leads l')
                ->where('l.idusuario', $userId)
                ->where('l.idetapa', $etapa['idetapa'])
                ->where('l.estado IS NULL')
                ->countAllResults();

            // Obtener algunos leads de muestra
            $leadsEjemplo = $this->db->table('leads l')
                ->join('personas p', 'l.idpersona = p.idpersona')
                ->select('l.idlead, p.nombres, p.apellidos, p.telefono')
                ->where('l.idusuario', $userId)
                ->where('l.idetapa', $etapa['idetapa'])
                ->where('l.estado IS NULL')
                ->limit(5)
                ->get()
                ->getResultArray();

            $pipeline[] = [
                'etapa_id' => $etapa['idetapa'],
                'etapa_nombre' => $etapa['nombre'],
                'total_leads' => $totalLeads,
                'leads' => $leadsEjemplo
            ];
        }

        return $pipeline;
    }

    /**
     * Contar leads por usuario
     */
    public function contarLeadsUsuario($userId)
    {
        return $this->where('idusuario', $userId)
                   ->where('estado IS NULL')
                   ->countAllResults();
    }

    /**
     * Obtener leads recientes del usuario
     */
    public function getLeadsRecientes($userId, $limite = 5)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono,
                     e.nombre as etapa_nombre, l.fecha_registro')
            ->where('l.idusuario', $userId)
            ->where('l.estado IS NULL')
            ->orderBy('l.fecha_registro', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener leads completos con filtros avanzados
     */
    public function getLeadsCompletos($filtros = [])
    {
        $builder = $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->join('etapas e', 'l.idetapa = e.idetapa')
            ->join('origenes o', 'l.idorigen = o.idorigen')
            ->join('distritos d', 'p.iddistrito = d.iddistrito', 'LEFT')
            ->join('provincias pr', 'd.idprovincia = pr.idprovincia', 'LEFT')
            ->select('l.*, p.nombres, p.apellidos, p.dni, p.telefono, p.correo,
                     p.direccion, p.referencias, e.nombre as etapa_nombre,
                     o.nombre as origen_nombre, d.nombre as distrito_nombre,
                     pr.nombre as provincia_nombre');

        // Aplicar filtros
        if (!empty($filtros['usuario'])) {
            $builder->where('l.idusuario', $filtros['usuario']);
        }
        if (!empty($filtros['etapa'])) {
            $builder->where('l.idetapa', $filtros['etapa']);
        }
        if (!empty($filtros['origen'])) {
            $builder->where('l.idorigen', $filtros['origen']);
        }
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $builder->where('l.fecha_registro >=', $filtros['fecha_inicio'])
                    ->where('l.fecha_registro <=', $filtros['fecha_fin']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Crear un nuevo lead
     */
    public function crearLead($data)
    {
        // Validar datos
        if (!$this->validate($data)) {
            return false;
        }

        // Crear lead
        $this->insert($data);

        return $this->getInsertID();
    }
    /**
     * Mover lead a otra etapa
     */
    public function moverEtapa($leadId, $nuevaEtapa, $usuarioId)
    {
        // Actualizar lead
        $this->update($leadId, [
            'idetapa' => $nuevaEtapa,
            'idusuario' => $usuarioId
        ]);

        // Registrar historial
        $this->registrarHistorial($leadId, $nuevaEtapa, $usuarioId);
    }

    /**
     * Convertir lead a cliente
     */
    public function convertirCliente($leadId, $dataCliente)
    {
        // Actualizar lead a estado "convertido"
        $this->update($leadId, [
            'estado' => 'convertido',
            'fecha_conversion_contrato' => date('Y-m-d H:i:s')
        ]);

        // Aquí se podría agregar lógica adicional para crear el cliente en la tabla correspondiente
    }

    /**
     * Descartar un lead
     */
    public function descartarLead($leadId)
    {
        // Actualizar lead a estado "descartado"
        $this->update($leadId, [
            'estado' => 'descartado'
        ]);
    }

    /**
     * Registrar historial de un lead
     */
    public function registrarHistorial($leadId, $etapaId, $usuarioId)
    {
        $this->db->table('seguimiento')->insert([
            'idlead' => $leadId,
            'idetapa' => $etapaId,
            'idusuario' => $usuarioId,
            'fecha' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Obtener estadísticas de leads
     */
    public function getEstadisticas($userId)
    {
        return $this->db->table('leads l')
            ->select('
                COUNT(*) as total_leads,
                SUM(CASE WHEN l.estado = "convertido" THEN 1 ELSE 0 END) as leads_convertidos,
                SUM(CASE WHEN l.estado = "descartado" THEN 1 ELSE 0 END) as leads_descartados
            ')
            ->where('l.idusuario', $userId)
            ->get()
            ->getRowArray();
    }

    /**
     * Obtener leads por etapa
     */
    public function getLeadsPorEtapa($etapaId)
    {
        return $this->db->table('leads l')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->select('l.idlead, p.nombres, p.apellidos, p.telefono, l.fecha_registro')
            ->where('l.idetapa', $etapaId)
            ->where('l.estado IS NULL')
            ->orderBy('l.fecha_registro', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Asignar usuario a un lead
     */
    public function asignarUsuario($leadId, $usuarioId)
    {
        $this->update($leadId, [
            'idusuario' => $usuarioId
        ]);
    }
}