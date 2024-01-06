<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    protected $table = 'municipios_geometria';
    public $incrementing = false;
    protected $primaryKey = 'id';
}