<?php

namespace App\Controllers;

use App\Models\ComentarioModel;
use App\Models\NotificacionModel;

class Comentarios extends BaseController
{
    protected $comentarioModel;
    protected $notificacionModel;

    public function __construct()
    {
        $this->comentarioModel = new ComentarioModel();
        $this->notificacionModel = new NotificacionModel();
    }

    /**
     * Agregar comentario (AJAX)
     */
    public function agregar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $idlead = $this->request->getPost('idlead');
        $comentario = $this->request->getPost('comentario');
        $tipo = $this->request->getPost('tipo') ?? 'Nota';
        $idusuario = session()->get('idusuario');

        if (!$idlead || !$comentario || !$idusuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        try {
            $id = $this->comentarioModel->agregarComentario($idlead, $idusuario, $comentario, $tipo);
            
            if ($id) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Comentario agregado',
                    'comentario' => [
                        'idcomentario' => $id,
                        'usuario_nombre' => session()->get('nombre'),
                        'comentario' => $comentario,
                        'tipo' => $tipo,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al agregar comentario: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error desconocido'
        ]);
    }

    /**
     * Obtener comentarios de un lead (AJAX)
     */
    public function obtener($idlead)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $comentarios = $this->comentarioModel->getComentariosLead($idlead);
            
            return $this->response->setJSON([
                'success' => true,
                'comentarios' => $comentarios
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener comentarios'
            ]);
        }
    }
}
