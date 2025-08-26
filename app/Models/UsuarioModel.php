<?php
namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'idusuario';

    protected $allowedFields = [
        'idpersona',
        'nombreusuario',
        'claveacceso',
        'rol',
        'estado',
        'creado',
        'modificado'
    ];
}
