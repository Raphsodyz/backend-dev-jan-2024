<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PontoUsuario extends Model
{
    use HasUuids;
    protected $table = 'pontos_usuario';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $connection = 'pgsql';
    public $timestamps = false;
    protected $fillable = ['id', 'longitude', 'latitude', 'municipio_id', 'geom'];

    public function municipio() : BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }
}