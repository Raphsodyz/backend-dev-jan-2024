<?php

namespace App\Http\Controllers;
use App\Models\PontoUsuario;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PontosController extends Controller
{
    /** 
     * @OA\Get(
     *      path="/api/pontos/{id}",
     *      summary="Method for find ponto by his uuid.",
     *      tags={"Pontos Controller"},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
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
            $validator = validator(['id' => $id], ['id' => ['required', 'uuid'],]);
            if ($validator->fails()) 
                return response()->json(['error' => $validator->errors()], 400);

            $ponto = PontoUsuario::with('municipio')->findOrFail($id);
            $result = [
                'id' => $ponto->id,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude,
                'municipio_id' => $ponto->municipio_id,
                'municipio' =>  $ponto->municipio ? $ponto->municipio->nome_municipio : null,
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
     *          name="id",
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
            $validator = validator(['id' => $id], ['id' => ['required', 'uuid'],]);
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
     *          name="id",
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
                    'id' => ['required', 'uuid'],
                    'latitude' => 'required|numeric|between:-90,90',
                    'longitude' => 'required|numeric|between:-180,180',
                ]
            );
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $ponto = PontoUsuario::findOrFail($id);
            $ponto->update($request->only(['latitude', 'longitude']));

            $ponto->update(['geom' => DB::raw("public.ST_SetSRID(public.ST_MakePoint($request->longitude, $request->latitude), 4326)")]);
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
            $validator = Validator::make($request->all(), [
                'latitude' => ['required', 'numeric', 'between:-90, 90'],
                'longitude' => ['required', 'numeric', 'between:-180, 180'],
                'municipio_id' => ['required', 'uuid'],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $ponto = new PontoUsuario();
            DB::transaction(function() use ($request, $ponto){
                $ponto->id = uuid_create(UUID_TYPE_RANDOM);
                $ponto->longitude = $request->input('longitude');
                $ponto->latitude = $request->input('latitude');
                $ponto->municipio_id = $request->input('municipio_id');
                $ponto->save();
                
                $ponto->update(['geom' => DB::raw("public.ST_SetSRID(public.ST_MakePoint($ponto->longitude, $ponto->latitude), 4326)")]);
            });

            $result = [
                'id' => $ponto->id,
                'latitude' => $ponto->latitude,
                'longitude' => $ponto->longitude,
                'municipio_id' => $ponto->municipio_id,
            ];

            $location = route('pontos.show', ['id' => $ponto->id]);
            return response()->json($result, 201)->header('Location', $location);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
