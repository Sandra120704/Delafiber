<?php
namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    public function login()
    {
        return view('usuarios/login');
    }

    public function login_action()
    {
        $usuarioModel = new UsuarioModel();

        $username = $this->request->getPost('nombreusuario');
        $password = $this->request->getPost('claveacceso');

        $usuario = $usuarioModel
            ->where('nombreusuario', $username)
            ->where('estado', 1)
            ->first();

        if ($usuario && password_verify($password, $usuario['claveacceso'])) {
            session()->set([
                'idusuario' => $usuario['idusuario'],
                'nombreusuario' => $usuario['nombreusuario'],
                'rol' => $usuario['rol'],
                'isLoggedIn' => true,
            ]);
            return redirect()->to('/dashboard');
        } else {
            return redirect()->back()->with('error', 'Credenciales inválidas o usuario inactivo');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function crearAdmin()
    {
        $usuarioModel = new UsuarioModel();

        // Para evitar duplicados, solo crea si no existe
        if (!$usuarioModel->where('nombreusuario', 'admin')->first()) {
            $usuarioModel->insert([
                'idpersona' => 8,
                'nombreusuario' => 'admin',
                'claveacceso' => password_hash('admin123', PASSWORD_DEFAULT),
                'rol' => 'admin',
                'estado' => 1
            ]);
            echo "Usuario admin creado.";
        } else {
            echo "Usuario admin ya existe.";
        }
    }
}
