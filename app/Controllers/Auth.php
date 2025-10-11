<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Auth extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Página de login
     */
    public function index()
    {
        // Si ya está logueado, redirigir al dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Iniciar Sesión - Delafiber CRM',
        ];

        return view('auth/login', $data);
    }

    /**
     * Procesar login
     */
    public function login()
    {
        if ($this->request->getMethod() === 'get') {
            if (session()->get('logged_in')) {
                return redirect()->to('/dashboard');
            }
            $data = [
                'title' => 'Iniciar Sesión - Delafiber CRM',
            ];
            // Verifica si la vista existe antes de mostrarla
            if (!is_file(APPPATH . 'Views/auth/login.php')) {
                return 'La vista auth/login.php no existe.';
            }
            return view('auth/login', $data);
        }

        $rules = [
            'usuario' => 'required|min_length[3]',
            'password' => 'required|min_length[3]'
        ];

        $messages = [
            'usuario' => [
                'required' => 'El usuario/email es obligatorio',
                'min_length' => 'Debe tener al menos 3 caracteres'
            ],
            'password' => [
                'required' => 'La contraseña es obligatoria',
                'min_length' => 'La contraseña debe tener al menos 3 caracteres'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', 'Error al validar credenciales');
        }

        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');

        // Validar credenciales (acepta email o nombre)
        $user = $this->usuarioModel->validarCredenciales($usuario, $password);
        
        if ($user) {
            // Obtener información del rol
            $db = \Config\Database::connect();
            $rol = $db->table('roles')
                ->select('idrol, nombre, nivel, permisos')
                ->where('idrol', $user['idrol'] ?? 3)
                ->get()
                ->getRowArray();
            
            // Crear sesión (variables estandarizadas)
            $sessionData = [
                'idusuario' => $user['idusuario'],
                'nombre' => $user['nombre_completo'],
                'email' => $user['correo'],
                'nombreRol' => $rol['nombre'] ?? 'Vendedor',
                'idrol' => $rol['idrol'] ?? 3,
                'nivel' => $rol['nivel'] ?? 3,
                'logged_in' => true
            ];

            session()->set($sessionData);

            // Registrar último login
            $this->usuarioModel->actualizarUltimoLogin($user['idusuario']);
            session()->setFlashdata('success', 'Bienvenido, ' . $user['nombre_completo']);

            // Redirigir al dashboard (página principal)
            return redirect()->to('/dashboard');

        } else {
            // Credenciales incorrectas
            return redirect()->back()
                ->withInput()
                ->with('error', 'Usuario o contraseña incorrectos');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth')->with('success', 'Has cerrado sesión correctamente');
    }

    /**
     * Verificar si está autenticado (para AJAX)
     */
    public function checkAuth()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        return $this->response->setJSON([
            'authenticated' => (bool)session()->get('logged_in'),
            'idusuario' => session()->get('idusuario'),
            'nombre' => session()->get('nombre')
        ]);
    }

    public function requireAuth()
    {
        if (!session()->get('logged_in')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'No autenticado'], 401);
            }
            return redirect()->to('/auth')->with('error', 'Debes iniciar sesión para acceder');
        }
        return true;
    }
}