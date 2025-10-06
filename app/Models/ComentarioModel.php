<?php

namespace App\Models;

use CodeIgniter\Model;

class ComentarioModel extends Model
{
    protected $table = 'comentarios_lead';
    protected $primaryKey = 'idcomentario';
    protected $allowedFields = ['idlead', 'idusuario', 'comentario', 'tipo'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    
    /**
     * Obtener comentarios de un lead con información del usuario
     */
    public function getComentariosLead($idlead)
    {
        return $this->db->table('comentarios_lead c')
            ->select('c.*, u.usuario as usuario_nombre, u.avatar')
            ->join('usuarios u', 'c.idusuario = u.idusuario')
            ->where('c.idlead', $idlead)
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Agregar comentario a un lead
     */
    public function agregarComentario($idlead, $idusuario, $comentario, $tipo = 'Nota')
    {
        $data = [
            'idlead' => $idlead,
            'idusuario' => $idusuario,
            'comentario' => $comentario,
            'tipo' => $tipo
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Contar comentarios de un lead
     */
    public function contarComentarios($idlead)
    {
        return $this->where('idlead', $idlead)->countAllResults();
    }
    
    /**
     * Obtener últimos comentarios del usuario
     */
    public function getUltimosComentariosUsuario($idusuario, $limit = 10)
    {
        return $this->db->table('comentarios_lead c')
            ->select('c.*, l.idlead, CONCAT(p.nombres, " ", p.apellidos) as lead_nombre')
            ->join('leads l', 'c.idlead = l.idlead')
            ->join('personas p', 'l.idpersona = p.idpersona')
            ->where('c.idusuario', $idusuario)
            ->orderBy('c.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
