<?php

namespace App\Repository;

use App\Interface\IMunicipioRepository;
use App\Models\Municipio;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MunicipioRepository implements IMunicipioRepository
{
    public function GetByLongLat($joins = [], $columns = ['*'], $longitude, $latitude)
    {
        $query = Municipio::query();

        if($joins)
            foreach($joins as $join)
                $query->join($join['table'], $join['condition'], $join['on']);


        $result = $query->whereRaw("public.ST_Intersects(municipios_geometria.geom::geometry, public.ST_SetSRID(public.ST_MakePoint($longitude, $latitude), 4326))")
            ->select($columns)->get();

        if ($result->count() === 0) 
            throw new ModelNotFoundException("Not found municipio with these lat/lon.");
        
        return $result;
    }
}