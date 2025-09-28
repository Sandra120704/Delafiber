<?php

namespace App\Models;

use CodeIgniter\Model;

class OrigenModel extends Model
{
    protected $table = 'origenes';
    protected $primaryKey = 'idorigen';

    // Obtener orígenes activos
    public function getOrigenesActivos()
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }
}
