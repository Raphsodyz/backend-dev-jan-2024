<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Municipio;
use Exception;
use Illuminate\Support\Facades\DB;

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
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            $point = DB::raw("ST_SetSRID(ST_Point($longitude, $latitude), 4326)");
            $result = Municipio::whereRaw("ST_Contains(CAST(geom AS geometry), $point)")->first();

            if(!$result)
                return response()->json(['Error' => 'Not found municipio with these lat/lon.'], 404);
                
            return response()->json([$result], 200);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
