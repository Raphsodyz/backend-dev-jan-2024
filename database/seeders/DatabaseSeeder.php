<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Municipio;
use App\Models\Estado;
use App\Models\GeojsonStates;
use Illuminate\Support\Facades\DB;
use Exception;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        try
        {
            GeojsonStates::insert([
                ['id' => uuid_create(UUID_TYPE_RANDOM), 'state_acronym' => 'MG', 'geojson_link' => 'https://raw.githubusercontent.com/tbrugz/geodata-br/master/geojson/geojs-31-mun.json'],
                ['id' => uuid_create(UUID_TYPE_RANDOM), 'state_acronym' => 'SP', 'geojson_link' => 'https://raw.githubusercontent.com/tbrugz/geodata-br/master/geojson/geojs-35-mun.json']
            ]);

            $entries = GeojsonStates::all();
            foreach($entries as $entry)
            {
                $estado = Estado::create(['id' => uuid_create(UUID_TYPE_RANDOM), 'nome_estado' => $entry->state_acronym, 'geom' => null ]);
                $response = Http::get($entry->geojson_link);
                self::populateDatabase($response, $estado->id);
                self::generateStateGeometry($estado->id);
            }
        }
        catch(Exception $ex)
        {
            echo "An exception was throw: " . $ex->getMessage();
        }
    }

    private static function populateDatabase($requisicion, $idState)
    {
        DB::transaction(function() use ($requisicion, $idState){
            if(!$requisicion->successful())
            throw new Exception("The json file for seed the database is unavailable.");
        
            $data = $requisicion->json();
            if(!isset($data['features']) && !is_array($data['features']))
                throw new Exception("The json schema has changed.");
                
            foreach($data['features'] as $value){
                $geometryData = json_encode($value['geometry']);
                $municipio = [
                    'id' => Str::uuid()->toString(),
                    'nome_municipio' => $value['properties']['name'],
                    'geom' => DB::raw("public.ST_SetSRID(public.ST_GeomFromGeoJSON('$geometryData'), 4326)"),
                    'id_state' => $idState
                ];
                $model = new Municipio($municipio);
                $model->save();
            }
        });
    }

    private static function generateStateGeometry($idState)
    {
        $aggregateCities = Municipio::where('id_state', '=', $idState)
            ->select('id_state', DB::raw('ST_Union(ST_MakeValid(geom::geometry)) as state_geometry'))
            ->groupBy('id_state')
            ->first();

        DB::table('estados_geometria')->updateOrInsert(
            ['id' => $idState],
            ['geom' => $aggregateCities->state_geometry]
        );
    }
}
