<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estados extends Model
{
    protected $table = 'estados_geometria';
    public $incrementing = false;
    protected $primaryKey = 'id';
}