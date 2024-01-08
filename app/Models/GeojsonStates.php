<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class GeojsonStates extends Model
{
    use HasUuids;
    protected $table = 'geojson_states';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $connection = 'pgsql';
    public $timestamps = false;
    protected $fillable = ['state_acronym', 'geojson_link'];
}
