<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipio;
use Exception;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      title="TerraQ test endpoints",
 *      version="1.0.0",
 *      description="API for interation with data.",
 * )
 */
class MunicipioController extends Controller
{
    /** 
     * @OA\Get(
     *      path="/api/localizar-municipio",
     *      summary="Method for find municipio by his lat/lon.",
     *      tags={"Municipios Controller"},
     *      @OA\Parameter(
     *          in="query",
     *          name="longitude",
     *          @OA\Schema(type="double"),
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="latitude",
     *          @OA\Schema(type="double"),
     *      ),
     *      @OA\Response(response="200", description="Data found!"),
     *      @OA\Response(response="404", description="Resource not found with the informed lat/lon."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function ShowByLatLong(Request $request)
    {
        try
        {
            $validator = $request->validate([
                'longitude' => ['required', 'numeric', 'between:-180,180'],
                'latitude' => ['required', 'numeric', 'between:-90,90']
            ]);

            $longitude = $request->input('longitude');
            $latitude = $request->input('latitude');    

            $result = Municipio::join('estados_geometria', 'municipios_geometria.id_state', '=', 'estados_geometria.id')
                ->select('municipios_geometria.id', 'municipios_geometria.nome_municipio', 'municipios_geometria.id_state', 'estados_geometria.nome_estado')
                ->whereRaw("public.ST_Intersects(municipios_geometria.geom::geometry, public.ST_SetSRID(public.ST_MakePoint($longitude, $latitude), 4326))")
                ->get();

            if($result->count() === 0)
                return response()->json(['Error' => 'Not found municipio with these lat/lon.'], 404);
                
            return response()->json(["data" => $result], 200);
        }
        catch(ValidationException $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 400);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
