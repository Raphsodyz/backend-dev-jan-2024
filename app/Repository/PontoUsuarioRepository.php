<?php

namespace App\Repository;

use App\Interface\IPontoUsuarioRepository;
use App\Models\PontoUsuario;
use Illuminate\Support\Facades\DB;

class PontoUsuarioRepository implements IPontoUsuarioRepository
{
    public function Get($id, $joins = [], $columns = ['*'])
    {
        $query = PontoUsuario::query();

        if($joins)
            foreach($joins as $join)
                $query->join($join['table'], $join['condition'], $join['on']);

        return $query->select($columns)->findOrFail($id);
    }
    
    public function Delete($id)
    {
        $ponto = PontoUsuario::findOrFail($id);
        return PontoUsuario::destroy($ponto->id);
    }

    public function Update($id, array $newData)
    {
        return DB::transaction(function () use ($id, $newData) {
            PontoUsuario::whereId($id)->update([
                'longitude' => $newData['longitude'],
                'latitude' => $newData['latitude'],
                'geom' => DB::raw("public.ST_SetSRID(public.ST_MakePoint({$newData['longitude']}, {$newData['latitude']}), 4326)")
            ]);
    
            $ponto = PontoUsuario::findOrFail($id);
            return $ponto;
        });
    }
    
    public function Create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $ponto = PontoUsuario::create($data);
            $ponto->update([
                'geom' => DB::raw("public.ST_SetSRID(public.ST_MakePoint($ponto->longitude, $ponto->latitude), 4326)")
            ]);
    
            $ponto->refresh();
            return $ponto;
        });
    }
}