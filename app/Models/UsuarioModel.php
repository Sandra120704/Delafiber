<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';
    protected $allowedFields = ['usuario', 'clave', 'idrol', 'idpersona', 'activo', 'ultimo_login'];
    protected $useTimestamps = false;
    
    /**
     * Validar credenciales de usuario
     */
    public function validarCredenciales($usuario, $password)
    {
        $builder = $this->db->table('usuarios u')
            ->join('personas p', 'u.idpersona = p.idpersona')
            ->join('roles r', 'u.idrol = r.idrol')
            ->select('u.idusuario, u.usuario, u.clave, u.activo,
                     CONCAT(p.nombres, " ", p.apellidos) as nombre_completo,
                     p.correo, r.nombre as rol')
            ->where('u.usuario', $usuario)
            ->where('u.activo', 1);
        
        $user = $builder->get()->getRowArray();
        
        if (!$user) {
            return false;
        }
        
        // Verificar contraseña
        // NOTA: En producción deberías usar password_verify() con hashes
        if ($user['clave'] === $password) {
            // No devolver la contraseña en el resultado
            unset($user['clave']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario completo por ID
     */
    public function getUsuarioCompleto($userId)
    {
        return $this->db->table('vista_usuarios_completa')
            ->where('idusuario', $userId)
            ->get()
            ->getRowArray();
    }
    
    /**
     * Actualizar último login
     */
    public function actualizarUltimoLogin($userId)
    {
        return $this->update($userId, [
            'ultimo_login' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($userId, $nuevaPassword)
    {
        // En producción, usar password_hash()
        return $this->update($userId, [
            'clave' => $nuevaPassword
        ]);
    }
    
    /**
     * Obtener usuarios activos
     */
    public function getUsuariosActivos()
    {
        return $this->db->table('vista_usuarios_completa')
            ->where('activo', 1)
            ->orderBy('nombre_completo')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Verificar si el usuario tiene permisos
     */
    public function tienePermiso($userId, $permiso)
    {
        $user = $this->getUsuarioCompleto($userId);
        
        if (!$user) return false;
        
        // Lógica simple de permisos por rol
        $permisos = [
            'admin' => ['todo'],
            'supervisor' => ['leads', 'reportes', 'usuarios'],
            'vendedor' => ['leads', 'seguimientos', 'cotizaciones']
        ];
        
        $rolPermisos = $permisos[$user['rol']] ?? [];
        
        return in_array('todo', $rolPermisos) || in_array($permiso, $rolPermisos);
    }
}