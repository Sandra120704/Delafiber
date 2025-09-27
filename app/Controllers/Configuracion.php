<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Configuracion extends Controller
{
    public function obtenerPreferencias()
    {
        // Devuelve preferencias de usuario (simulado)
        return $this->response->setJSON([
            'tema' => 'claro',
            'notificaciones' => true
        ]);
    }
}
