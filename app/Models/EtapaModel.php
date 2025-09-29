<?php

namespace App\Models;

use CodeIgniter\Model;

class EtapaModel extends Model
{
    protected $table = 'etapas';
    protected $primaryKey = 'idetapa';
    protected $allowedFields = ['idpipeline', 'nombre', 'orden'];

    // Obtener etapas activas ordenadas
    public function getEtapasActivas()
    {
        return $this->orderBy('orden', 'ASC')->findAll();
    }

    // Obtener primera etapa (para leads nuevos)
    public function getPrimeraEtapa()
    {
        return $this->orderBy('orden', 'ASC')->first();
    }

    public function getEtapasPipeline($idpipeline = 1)
    {
        return $this->where('idpipeline', $idpipeline)
            ->orderBy('orden', 'ASC')
            ->findAll();
    }
}
