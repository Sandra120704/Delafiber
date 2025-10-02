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
            ->join('personas p', 'u.idpersona = p.idpersona', 'left')
            ->join('roles r', 'u.idrol = r.idrol', 'left')
            ->select('u.idusuario, u.usuario, u.clave, u.activo,
                     COALESCE(CONCAT(p.nombres, " ", p.apellidos), u.usuario) as nombre_completo,
                     p.correo, 
                     COALESCE(r.nombre, "Usuario") as rol')
            ->where('u.usuario', $usuario)
            ->where('u.activo', 1);
        
        $user = $builder->get()->getRowArray();
        
        if (!$user) {
            log_message('error', 'Usuario no encontrado o inactivo: ' . $usuario);
            return false;
        }
        
        // Verificar contraseña (comparación directa - en producción usar password_verify)
        if ($user['clave'] === $password || password_verify($password, $user['clave'])) {
            // No devolver la contraseña en el resultado
            unset($user['clave']);
            log_message('info', 'Login exitoso para usuario: ' . $usuario);
            return $user;
        }
        
        log_message('warning', 'Contraseña incorrecta para usuario: ' . $usuario);
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
    public function getUsuariosConDetalle()
    {
        try {
            // Usar Query Builder del modelo para la consulta completa
            return $this->select('
                usuarios.idusuario,
                usuarios.usuario as nombreUsuario,
                usuarios.clave,
                COALESCE(usuarios.activo, 1) as estadoActivo,
                usuarios.idrol,
                usuarios.idpersona,
                COALESCE(CONCAT(personas.nombres, " ", personas.apellidos), "Sin asignar") as nombrePersona,
                personas.correo as emailPersona,
                personas.telefono,
                roles.nombre as nombreRol,
                roles.descripcion as descripcionRol,
                0 as totalLeads,
                0 as totalTareas,
                0 as tasaConversion
            ')
            ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')
            ->join('roles', 'usuarios.idrol = roles.idrol', 'left')
            ->orderBy('usuarios.idusuario')
            ->findAll();
            
        } catch (\Exception $error) {
            // Si hay error en la consulta compleja, usar método simple
            return $this->getUsuariosBasico();
        }
    }
    public function getUsuariosBasico()
    {
        // Obtener todos los usuarios de forma simple
        $listaUsuarios = $this->findAll();
        
        // Agregar campos faltantes con valores por defecto
        foreach ($listaUsuarios as &$datosUsuario) {
            $datosUsuario['nombreUsuario'] = $datosUsuario['usuario'] ?? '';
            $datosUsuario['nombrePersona'] = 'Usuario ID: ' . $datosUsuario['idusuario'];
            $datosUsuario['nombreRol'] = 'Sin rol asignado';
            $datosUsuario['estadoActivo'] = $datosUsuario['activo'] ?? 1;
            $datosUsuario['emailPersona'] = '';
            $datosUsuario['telefono'] = '';
            $datosUsuario['totalLeads'] = 0;
            $datosUsuario['totalTareas'] = 0;
            $datosUsuario['tasaConversion'] = 0;
        }
        
        return $listaUsuarios;
    }
    public function obtenerUsuariosConNombres()
    {
        // Usar el Query Builder del modelo directamente
        return $this->select('usuarios.*, CONCAT(personas.nombres, " ", personas.apellidos) as nombreCompleto')
                    ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')
                    ->findAll();
    }
    
    /**
     * Obtener usuario completo por ID
     */
    public function obtenerUsuarioCompleto($idUsuario)
    {
        // Usar Query Builder del modelo directamente
        return $this->select('
            usuarios.*,
            personas.nombres,
            personas.apellidos,
            CONCAT(personas.nombres, " ", personas.apellidos) as nombrePersona,  
            personas.correo, personas.telefono, personas.direccion,                
            roles.nombre as nombreRol                             
        ')
        ->join('personas', 'usuarios.idpersona = personas.idpersona', 'left')    
        ->join('roles', 'usuarios.idrol = roles.idrol', 'left')               
        ->find($idUsuario);                                          
    }
    
}