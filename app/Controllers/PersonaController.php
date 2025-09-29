<?php

namespace App\Controllers;

use App\Models\PersonaModel;
use App\Models\DistritoModel;

class PersonaController extends BaseController
{
    protected $personaModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
    }

    // ======================================
    // Método para mostrar todas las personas
    // ======================================
    public function index(): string
    {
        $datos = $this->getHeaderFooter();
        $datos['personas'] = $this->personaModel->findAll();
        return view('Personas/index', $datos);
    }

    // ======================================
    // Crear nueva persona
    // ======================================
    public function create()
    {
        $datos = $this->getHeaderFooter();

        $distritoModel = new DistritoModel();
        $datos['distritos'] = $distritoModel->findAll();

        if ($this->request->getMethod() === 'post') {
            $this->personaModel->insert($this->request->getPost());
            return redirect()->to('/personas');
        }

        return view('Personas/create', $datos);
    }

    // ======================================
    // Editar persona
    // ======================================
    public function edit($id = null)
    {
        $datos = $this->getHeaderFooter();
        $datos['persona'] = $this->personaModel->find($id);

        if ($this->request->getMethod() === 'post') {
            $this->personaModel->update($id, $this->request->getPost());
            return redirect()->to('/personas');
        }

        return view('Personas/edit', $datos);
    }

    // ======================================
    // Eliminar persona
    // ======================================
    public function delete($id = null)
    {
        $this->personaModel->delete($id);
        return redirect()->to('/personas');
    }

    // ======================================
    // Buscar personas vía AJAX
    // ======================================
    public function buscarAjax()
    {
        $query = trim($this->request->getGet('q'));

        try {
            $builder = $this->personaModel->select('idpersona, nombres, apellidos, dni, telefono, correo, direccion')
                                           ->orderBy('idpersona', 'DESC');

            if (!empty($query)) {
                $builder->groupStart()
                        ->like('nombres', $query)
                        ->orLike('apellidos', $query)
                        ->orLike('dni', $query)
                        ->orLike('telefono', $query)
                        ->orLike('correo', $query)
                        ->groupEnd()
                        ->limit(50);
            } else {
                $builder->limit(20);
            }

            $personas = array_map(function ($persona) {
                return [
                    'idpersona' => (int)$persona['idpersona'],
                    'nombres' => htmlspecialchars($persona['nombres'], ENT_QUOTES, 'UTF-8'),
                    'apellidos' => htmlspecialchars($persona['apellidos'], ENT_QUOTES, 'UTF-8'),
                    'dni' => htmlspecialchars($persona['dni'], ENT_QUOTES, 'UTF-8'),
                    'telefono' => htmlspecialchars($persona['telefono'], ENT_QUOTES, 'UTF-8'),
                    'correo' => htmlspecialchars($persona['correo'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'direccion' => htmlspecialchars($persona['direccion'] ?? '', ENT_QUOTES, 'UTF-8')
                ];
            }, $builder->findAll());

            return $this->response->setJSON($personas);

        } catch (\Exception $e) {
            log_message('error', 'Error en buscarAjax: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }

    // ======================================
    // Buscar persona por DNI (local o API RENIEC)
    // ======================================
    public function buscardni()
    {
        $dni = preg_replace('/\D/', '', $this->request->getGet('q')); // Solo números

        if (strlen($dni) !== 8) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'El DNI debe tener exactamente 8 dígitos numéricos'
            ]);
        }

        try {
            // 1️⃣ Revisar base local
            $persona = $this->personaModel->where('dni', $dni)->first();
            if ($persona) {
                return $this->response->setJSON([
                    'success' => true,
                    'nombres' => $persona['nombres'],
                    'apepaterno' => $persona['apepaterno'] ?? '',
                    'apematerno' => $persona['apematerno'] ?? '',
                    'registrado' => true
                ]);
            }

            // 2️⃣ Si no está local, llamar a RENIEC
            $api_token = 'TU_API_TOKEN_AQUI'; // <- reemplazar
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.reniec.example/dni/$dni");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $api_token,
            ]);
            $api_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($api_response !== false && $http_code === 200) {
                $decoded = json_decode($api_response, true);
                return $this->response->setJSON([
                    'success' => true,
                    'registrado' => false,
                    'nombres' => $decoded['first_name'] ?? '',
                    'apepaterno' => $decoded['first_last_name'] ?? '',
                    'apematerno' => $decoded['second_last_name'] ?? '',
                    'message' => 'Datos obtenidos de RENIEC'
                ]);
            }

            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'No se encontró información para este DNI'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en buscardni: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }

    private function getHeaderFooter(): array
    {
        return [
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];
    }
}
