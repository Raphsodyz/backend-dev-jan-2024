<?php

namespace App\Http\Controllers;
use App\Models\PontoUsuario;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PontosController extends Controller
{
    /** 
     * @OA\Get(
     *      path="/api/pontos/{id}",
     *      summary="Method for find ponto by his uuid.",
     *      tags={"Pontos Controller"},
     *      @OA\Parameter(
     *          in="path",
     *          name="ID",
     *          @OA\Schema(type="uuid"),
     *      ),
     *      @OA\Response(response="200", description="Data found!"),
     *      @OA\Response(response="400", description="The send request is invalid."),
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function ShowById($id)
    {
        try
        {
            $validator = validator(['id' => $id], ['id' => ['required', 'uuid', Rule::exists('pontos_usuario', 'id'),],]);
            if ($validator->fails()) 
                return response()->json(['error' => $validator->errors()], 400);

            if ($validator->fails()) {
                return response()->json(['Error' => $validator->errors()], 400);
            }

            $ponto = PontoUsuario::findOrFail($id);
            $result = [
                'id' => $ponto->id,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude,
                'municipio_id' => $ponto->municipio_id,
                'municipio' =>  $ponto->municipio->nome_municipio
            ];
            return response()->json(['data' => $result], 200);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json(['Error' => 'Not found ponto with mentioned id'], 404);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /** 
     * @OA\Delete(
     *      path="/api/pontos/{id}",
     *      summary="Method for delete ponto by his uuid.",
     *      tags={"Pontos Controller"},
     *      @OA\Parameter(
     *          in="path",
     *          name="ID",
     *          @OA\Schema(type="uuid"),
     *      ),
     *      @OA\Response(response="204", description="Deleted!"),
     *      @OA\Response(response="400", description="The send request is invalid."),
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function RemoveById($id)
    {
        try
        {
            $validator = validator(['id' => $id], ['id' => ['required', 'uuid', Rule::exists('pontos_usuario', 'id'),],]);
            if ($validator->fails()) 
                return response()->json(['error' => $validator->errors()], 400);
            
            $ponto = PontoUsuario::findOrFail($id);
            $ponto->delete();

            return response()->json(null, 204);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json(['Error' => 'Not found ponto with mentioned id'], 404);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /** 
     * @OA\Put(
     *      path="/api/pontos/{id}",
     *      summary="Method for update ponto by his uuid.",
     *      tags={"Pontos Controller"},
     *      @OA\Parameter(
     *          in="path",
     *          name="ID",
     *          @OA\Schema(type="uuid"),
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="latitude",
     *                      type="double"),
     *                  @OA\Property(
     *                      property="longitude",
     *                      type="double"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="200", description="Updated!"),
     *      @OA\Response(response="400", description="The send request is invalid."),
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function Update(Request $request, $id)
    {
        try
        {
            $validator = Validator::make(
                array_merge(['id' => $id], $request->all()),
                [
                    'id' => [
                        'required',
                        'uuid', // Assuming the id is a UUID; adjust as needed
                        Rule::exists('ponto_usuarios', 'id'),
                    ],
                    'latitude' => 'required|numeric',
                    'longitude' => 'required|numeric',
                ]
            );
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $ponto = PontoUsuario::findOrFail($id);
            $ponto->update($request->only(['latitude', 'longitude']));

            $newLongitude = $request->input('longitude');
            $newLatitude = $request->input('latitude');

            $polygonPoint = "POINT(?, ?)";
            $ponto->update(['geom' => DB::raw("ST_Buffer(ST_GeomFromText('$polygonPoint', 4326), 0.01)")], 
                [$newLongitude, $newLatitude]);
            
            $result = [
                'id' => $ponto->id,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude
            ];

            response()->json(['data' => $result], 200);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json(['Error' => 'Not found ponto with mentioned id'], 404);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /** 
     * @OA\Post(
     *      path="/api/pontos",
     *      summary="Method for create ponto.",
     *      tags={"Pontos Controller"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="latitude",
     *                      type="double"),
     *                  @OA\Property(
     *                      property="longitude",
     *                      type="double"),
     *                  @OA\Property(
     *                      property="municipio_id",
     *                      type="uuid"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="201", description="Created!"),
     *      @OA\Response(response="400", description="The send request is invalid."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function Create(Request $request)
    {
        try
        {
            
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
