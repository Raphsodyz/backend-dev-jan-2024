<?php

namespace App\Http\Controllers;

use App\Interface\IMunicipioRepository;
use Illuminate\Http\Request;
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
    private IMunicipioRepository $_municipioRepository;
    public function __construct(IMunicipioRepository $_municipioRepository)
    {
        $this->_municipioRepository = $_municipioRepository;
    }

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

            $join = [['table' => 'estados_geometria', 'condition' => 'municipios_geometria.id_state', 'on' => 'estados_geometria.id']];
            $result = $this->_municipioRepository->GetByLongLat($join, ['municipios_geometria.id', 'municipios_geometria.nome_municipio', 'municipios_geometria.id_state', 'estados_geometria.nome_estado'], $request->input('longitude'), $request->input('latitude'));
            
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
