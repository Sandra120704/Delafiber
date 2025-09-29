<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\LeadModel;
use App\Models\TareaModel;
use App\Models\SeguimientoModel;

class Perfil extends BaseController
{
    protected $usuarioModel;
    protected $leadModel;
    protected $tareaModel;
    protected $seguimientoModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->leadModel = new LeadModel();
        $this->tareaModel = new TareaModel();
        $this->seguimientoModel = new SeguimientoModel();
    }

    /**
     * Mostrar perfil del usuario
     */
    public function index()
    {
        $idusuario = session()->get('idusuario');
        
        // Obtener información del usuario
        $usuario = $this->usuarioModel->find($idusuario);
        
        if (!$usuario) {
            return redirect()->to('auth/login')
                ->with('error', 'Sesión inválida');
        }

        // Calcular estadísticas personales
        $estadisticas = $this->calcularEstadisticas($idusuario);

        // Obtener actividad reciente
        $actividadReciente = $this->obtenerActividadReciente($idusuario);

        $data = [
            'title' => 'Mi Perfil',
            'usuario' => $usuario,
            'estadisticas' => $estadisticas,
            'actividad_reciente' => $actividadReciente
        ];

        return view('perfil/index', $data);
    }

    /**
     * Actualizar información personal
     */
    public function actualizar()
    {
        $idusuario = session()->get('idusuario');

        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombres' => 'required|min_length[2]|max_length[100]',
            'apellidos' => 'required|min_length[2]|max_length[100]',
            'correo' => 'required|valid_email',
            'telefono' => 'permit_empty|max_length[20]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Verificar si el correo ya existe en otro usuario
        $correoExiste = $this->usuarioModel
            ->where('correo', $this->request->getPost('correo'))
            ->where('idusuario !=', $idusuario)
            ->first();

        if ($correoExiste) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El correo electrónico ya está en uso');
        }

        // Preparar datos
        $data = [
            'nombres' => $this->request->getPost('nombres'),
            'apellidos' => $this->request->getPost('apellidos'),
            'correo' => $this->request->getPost('correo'),
            'telefono' => $this->request->getPost('telefono')
        ];

        // Actualizar
        if ($this->usuarioModel->update($idusuario, $data)) {
            // Actualizar sesión
            session()->set([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos']
            ]);

            return redirect()->to('perfil')
                ->with('success', 'Información actualizada exitosamente');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la información');
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword()
    {
        $idusuario = session()->get('idusuario');

        // Validación
        $validation = \Config\Services::validation();
        $validation->setRules([
            'password_actual' => 'required',
            'password_nueva' => 'required|min_length[6]',
            'password_confirmar' => 'required|matches[password_nueva]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('error', 'Por favor corrige los errores en el formulario');
        }

        // Obtener usuario
        $usuario = $this->usuarioModel->find($idusuario);

        // Verificar contraseña actual
        if (!password_verify($this->request->getPost('password_actual'), $usuario['password'])) {
            return redirect()->back()
                ->with('error', 'La contraseña actual es incorrecta');
        }

        // Actualizar contraseña
        $data = [
            'password' => password_hash($this->request->getPost('password_nueva'), PASSWORD_DEFAULT)
        ];

        if ($this->usuarioModel->update($idusuario, $data)) {
            return redirect()->to('perfil')
                ->with('success', 'Contraseña cambiada exitosamente');
        } else {
            return redirect()->back()
                ->with('error', 'Error al cambiar la contraseña');
        }
    }

    /**
     * Calcular estadísticas personales del usuario
     */
    private function calcularEstadisticas($idusuario)
    {
        // Leads asignados
        $leadsAsignados = $this->leadModel
            ->where('idusuario', $idusuario)
            ->countAllResults();

        // Conversiones
        $conversiones = $this->leadModel
            ->where('idusuario', $idusuario)
            ->where('estado', 'Convertido')
            ->countAllResults();

        // Tasa de conversión
        $tasaConversion = $leadsAsignados > 0 
            ? round(($conversiones / $leadsAsignados) * 100, 1) 
            : 0;

        // Tareas pendientes
        $tareasPendientes = $this->tareaModel
            ->where('idusuario', $idusuario)
            ->where('estado !=', 'Completada')
            ->where('estado !=', 'Cancelada')
            ->countAllResults();

        return [
            'leads_asignados' => $leadsAsignados,
            'conversiones' => $conversiones,
            'tasa_conversion' => $tasaConversion,
            'tareas_pendientes' => $tareasPendientes
        ];
    }

    /**
     * Obtener actividad reciente del usuario
     */
    private function obtenerActividadReciente($idusuario, $limite = 10)
    {
        $actividades = [];

        // Seguimientos recientes
        $seguimientos = $this->seguimientoModel
            ->select('seguimientos.*, leads.nombres, leads.apellidos, "seguimiento" as tipo_actividad')
            ->join('leads', 'leads.idlead = seguimientos.idlead')
            ->where('seguimientos.idusuario', $idusuario)
            ->orderBy('seguimientos.fecha', 'DESC')
            ->limit(5)
            ->findAll();

        foreach ($seguimientos as $seg) {
            $actividades[] = [
                'descripcion' => "Seguimiento a {$seg['nombres']} {$seg['apellidos']}: {$seg['tipo']}",
                'fecha' => $seg['fecha'],
                'tipo_badge' => 'info',
                'icono' => 'icon-activity'
            ];
        }

        // Tareas completadas recientes
        $tareasCompletadas = $this->tareaModel
            ->where('idusuario', $idusuario)
            ->where('estado', 'Completada')
            ->where('fecha_completado >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('fecha_completado', 'DESC')
            ->limit(5)
            ->findAll();

        foreach ($tareasCompletadas as $tarea) {
            $actividades[] = [
                'descripcion' => "Tarea completada: {$tarea['titulo']}",
                'fecha' => $tarea['fecha_completado'],
                'tipo_badge' => 'success',
                'icono' => 'icon-check-circle'
            ];
        }

        // Leads creados recientemente
        $leadsCreados = $this->leadModel
            ->select('leads.*, personas.nombres, personas.apellidos')
            ->join('personas', 'personas.idpersona = leads.idpersona')
            ->where('leads.idusuario', $idusuario)
            ->where('leads.fecha_registro >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->orderBy('leads.fecha_registro', 'DESC')
            ->limit(3)
            ->findAll();

        foreach ($leadsCreados as $lead) {
            $actividades[] = [
                'descripcion' => "Nuevo lead registrado: {$lead['nombres']} {$lead['apellidos']}",
                'fecha' => $lead['fecha_registro'],
                'tipo_badge' => 'primary',
                'icono' => 'icon-user-plus'
            ];
        }

        // Ordenar por fecha descendente
        usort($actividades, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        // Limitar resultados
        return array_slice($actividades, 0, $limite);
    }

    /**
     * Subir foto de perfil (opcional - futuro)
     */
    public function subirFoto()
    {
        $idusuario = session()->get('idusuario');

        // Validar archivo
        $validation = \Config\Services::validation();
        $validation->setRules([
            'foto' => 'uploaded[foto]|is_image[foto]|max_size[foto,2048]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('error', 'Archivo inválido. Debe ser una imagen menor a 2MB');
        }

        $file = $this->request->getFile('foto');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Generar nombre único
            $newName = 'perfil_' . $idusuario . '_' . time() . '.' . $file->getExtension();
            
            // Mover archivo
            $file->move(WRITEPATH . 'uploads/perfiles', $newName);
            
            // Actualizar base de datos
            $this->usuarioModel->update($idusuario, [
                'foto_perfil' => $newName
            ]);

            return redirect()->to('perfil')
                ->with('success', 'Foto de perfil actualizada');
        } else {
            return redirect()->back()
                ->with('error', 'Error al subir el archivo');
        }
    }
}