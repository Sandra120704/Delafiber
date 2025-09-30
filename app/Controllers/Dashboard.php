<?php
namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Verifica si el usuario está autenticado
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('auth/login');
        }
        // ...prepara datos si es necesario...
        return view('dashboard/index');
    }

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
