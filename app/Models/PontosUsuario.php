<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PontosUsuario extends Model
{
    protected $table = 'pontos_usuario';
    public $incrementing = false;
    protected $primaryKey = 'id';
}