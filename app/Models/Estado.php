<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use HasUuids;
    protected $table = 'estados_geometria';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $connection = 'pgsql';
    public $timestamps = false;

    public function municipio() : HasMany
    {
        return $this->hasMany(Municipio::class);
    }
}