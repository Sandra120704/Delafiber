<?php
namespace App\Controllers;

class Configuracion extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Configuración - Delafiber CRM',
            'usuario' => [
                'nombre' => session()->get('nombre_completo') ?? session()->get('usuario'),
                'email' => session()->get('correo') ?? session()->get('email'),
                'rol' => session()->get('nombreRol') ?? 'Usuario'
            ]
        ];

        return view('configuracion/index', $data);
    }

    public function guardar()
    {
        // Validar y guardar configuración
        $data = [
            'tema' => $this->request->getPost('tema'),
            'notificaciones' => $this->request->getPost('notificaciones'),
            'idioma' => $this->request->getPost('idioma')
        ];

        // Aquí guardarías en la base de datos
        // Por ahora solo guardamos en sesión
        session()->set('configuracion', $data);

        return redirect()->to('configuracion')
            ->with('success', 'Configuración guardada correctamente');
    }

    public function obtenerPreferencias()
    {
        // Devuelve preferencias de usuario (simulado)
        return $this->response->setJSON([
            'tema' => session()->get('configuracion.tema') ?? 'claro',
            'notificaciones' => session()->get('configuracion.notificaciones') ?? true,
            'idioma' => session()->get('configuracion.idioma') ?? 'es'
        ]);
    }
}
