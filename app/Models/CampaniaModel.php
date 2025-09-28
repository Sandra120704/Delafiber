<?php

namespace App\Models;

use CodeIgniter\Model;

// app/Models/CampaniaModel.php
class CampaniaModel extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'idcampania';
    
    public function getCampaniasActivas()
    {
        return $this->where('estado', 'Activa')
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }
}
