<?php
namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model{
  protected $table = 'leads';
  protected $primaryKey = 'idlead';
  protected $allowedFields = [
    'iddifunsion','idpersona','idusuarioregistro','idusuarioresponsable',
    'fechaasignacion', 'estado','fecharegistro','creado','modificado'
  ];
  protected $useTimestamps = false;
}