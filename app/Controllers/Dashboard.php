<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function perfil()
    {
        // Devuelve datos de perfil (simulado)
        return $this->response->setJSON([
            'nombre' => 'Usuario',
            'email' => 'usuario@ejemplo.com'
        ]);
    }

    public function notificaciones()
    {
        // Devuelve notificaciones (simulado)
        return $this->response->setJSON([
            ['mensaje' => 'Bienvenido al sistema', 'fecha' => '2025-09-26']
        ]);
    }
}
