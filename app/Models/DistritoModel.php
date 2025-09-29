<?php

namespace App\Models;

use CodeIgniter\Model;

class DistritoModel extends Model
{
    protected $table = 'distritos';
    protected $primaryKey = 'iddistrito';
    protected $allowedFields = ['idprovincia', 'nombre'];

    // Obtener distritos donde opera Delafiber
    public function getDistritosDelafiber()
    {
        return $this->db->table('distritos d')
            ->join('provincias p', 'd.idprovincia = p.idprovincia')
            ->select('d.iddistrito, d.nombre as distrito_nombre, p.nombre as provincia_nombre') // Cambié aquí
            ->where('p.nombre', 'Chincha')
            ->orderBy('d.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getDistritosConProvincia()
    {
        // ...existing code...
    }

    public function getDistritosChincha()
    {
        return $this->whereIn('nombre', ['Chincha Alta', 'Sunampe', 'Grocio Prado'])
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }
}
