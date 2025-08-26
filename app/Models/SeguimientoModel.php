<?php
namespace App\Models;

use CodeIgniter\Model;

class SeguimientoModel extends Model
{
    protected $table = 'seguimientos';
    protected $primaryKey = 'idseguimiento';
    protected $allowedFields = [
        'idlead',
        'idetapa',
        'modalidadcontacto',
        'fecha',
        'hora',
        'comentarios',
        'idusuario',
        'estado'
    ];
}
