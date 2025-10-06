<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['time_helper'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;
    
    /**
     * Data to pass to views
     */
    protected $data = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        
        // Cargar notificaciones para el header
        $this->cargarNotificaciones();
    }
    
    /**
     * Cargar notificaciones del usuario actual
     */
    protected function cargarNotificaciones()
    {
        $idusuario = session()->get('idusuario');
        
        if ($idusuario) {
            try {
                // Verificar si la tabla existe
                $db = \Config\Database::connect();
                if ($db->tableExists('notificaciones')) {
                    $notificacionModel = new \App\Models\NotificacionModel();
                    
                    // Obtener notificaciones no leídas
                    $notificaciones = $notificacionModel->getNoLeidas($idusuario);
                    $notification_count = count($notificaciones);
                    
                    // Pasar a las vistas
                    $this->data['notifications'] = $notificaciones;
                    $this->data['notification_count'] = $notification_count;
                    
                    // También en sesión para acceso rápido
                    session()->set('notification_count', $notification_count);
                }
            } catch (\Exception $e) {
                // Si hay error (tabla no existe), simplemente no cargar notificaciones
                $this->data['notifications'] = [];
                $this->data['notification_count'] = 0;
            }
        }
    }
}
