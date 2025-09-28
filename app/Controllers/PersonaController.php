<?php

namespace App\Controllers;

use App\Models\PersonaModel;

class PersonaController extends BaseController
{
    protected $personaModel;

    public function __construct()
    {
        $this->personaModel = new PersonaModel();
    }

    public function index(): string
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $model = new PersonaModel();
        $datos['personas'] = $model->findAll();
        return view('Personas/index', $datos);
    }

    public function create()
    {
        $model = new PersonaModel();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        // Agrega la consulta de distritos
        $distritoModel = new \App\Models\DistritoModel();
        $datos['distritos'] = $distritoModel->findAll();
        if ($this->request->getMethod() === 'post') {
            $model->insert($this->request->getPost());
            return redirect()->to('/personas');
        }
        return view('Personas/create', $datos);
    }

    public function edit($id = null)
    {
        $model = new PersonaModel();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['persona'] = $model->find($id);
        if ($this->request->getMethod() === 'post') {
            $model->update($id, $this->request->getPost());
            return redirect()->to('/personas');
        }
        return view('Personas/edit', $datos);
    }

    public function delete($id = null)
    {
        $model = new PersonaModel();
        $model->delete($id);
        return redirect()->to('/personas');
    }

    public function buscarAjax()
    {
        $query = $this->request->getGet('q');
        $query = trim($query);

        try {
            if (empty($query)) {
                $personas = $this->personaModel
                    ->select('idpersona, nombres, apellidos, dni, telefono, correo, direccion')
                    ->orderBy('idpersona', 'DESC')
                    ->limit(20)
                    ->findAll();
            } else {
                $personas = $this->personaModel
                    ->select('idpersona, nombres, apellidos, dni, telefono, correo, direccion')
                    ->groupStart()
                    ->like('nombres', $query)
                    ->orLike('apellidos', $query)
                    ->orLike('dni', $query)
                    ->orLike('telefono', $query)
                    ->orLike('correo', $query)
                    ->groupEnd()
                    ->orderBy('idpersona', 'DESC')
                    ->limit(50)
                    ->findAll();
            }

            // Sanitizar datos de salida
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
            }, $personas);

            return $this->response->setJSON($personas);
        } catch (\Exception $e) {
            log_message('error', 'Error en buscarAjax: ' . $e->getMessage());
            return $this->response->setJSON([]);
        }
    }

    public function buscardni($dni = "")
    {
        $dni = $this->request->getGet('q') ?: $dni;
        $dni = preg_replace('/\D/', '', $dni); // Solo números

        if (strlen($dni) !== 8) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'El DNI debe tener exactamente 8 dígitos numéricos'
            ]);
        }

        try {
            $persona = $this->personaModel->where('dni', $dni)->first();
            if ($persona) {
                $apellidos = isset($persona['apellidos']) ? explode(' ', trim($persona['apellidos']), 2) : ['', ''];
                return $this->response->setJSON([
                    'success' => true,
                    'registrado' => true,
                    'DNI' => $persona['dni'],
                    'nombres' => $persona['nombres'] ?? '',
                    'apepaterno' => $apellidos[0] ?? '',
                    'apematerno' => $apellidos[1] ?? '',
                    'message' => 'Persona encontrada en la base de datos local'
                ]);
            }

            // API DE RENIEC (Decolecta)
            $api_token = env('API_DECOLECTA_TOKEN');
            if ($api_token) {
                $api_endpoint = "https://api.decolecta.com/v1/reniec/dni?numero=" . $dni;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_endpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $api_token,
                ]);
                $api_response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($api_response !== false && $http_code === 200) {
                    $decoded_response = json_decode($api_response, true);
                    if (isset($decoded_response['first_name'])) {
                        return $this->response->setJSON([
                            'success' => true,
                            'registrado' => false,
                            'apepaterno' => $decoded_response['first_last_name'] ?? '',
                            'apematerno' => $decoded_response['second_last_name'] ?? '',
                            'nombres' => $decoded_response['first_name'] ?? '',
                            'message' => 'Datos obtenidos de RENIEC'
                        ]);
                    }
                }
            }

            // Si no se encontró en API externa
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
}
