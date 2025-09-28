<?php

namespace App\Models;

use CodeIgniter\Model;

class EtapaModel extends Model
{
    protected $table = 'etapas';
    protected $primaryKey = 'idetapa';

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
}
