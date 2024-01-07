<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Municipio extends Model
{
    use HasUuids;
    protected $table = 'municipios_geometria';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $connection = 'pgsql';
    protected $fillable = ['id', 'nome_municipio', 'geom', 'id_estado'];
    public $timestamps = false;
}