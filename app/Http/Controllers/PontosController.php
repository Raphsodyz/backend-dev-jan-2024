<?php

namespace App\Http\Controllers;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Interface\IPontoUsuarioRepository;

class PontosController extends Controller
{
    private IPontoUsuarioRepository $_pontoUsuarioRepository;
    public function __construct(IPontoUsuarioRepository $_pontoUsuarioRepository)
    {
        $this->_pontoUsuarioRepository = $_pontoUsuarioRepository;
    }

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

            $join = [['table' => 'municipios_geometria', 'condition' => 'pontos_usuario.municipio_id', 'on' => 'municipios_geometria.id']];
            $result = $this->_pontoUsuarioRepository->Get($id, $join, ['pontos_usuario.id', 'longitude', 'latitude', 'municipio_id', 'municipios_geometria.nome_municipio']);
            
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
            
            $this->_pontoUsuarioRepository->Delete($id);

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
     *                      property="longitude",
     *                      type="double"),
     *                  @OA\Property(
     *                      property="latitude",
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
                    'longitude' => 'required|numeric|between:-180,180',
                    'latitude' => 'required|numeric|between:-90,90'
                ]
            );
    
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $result = $this->_pontoUsuarioRepository->Update($id, $request->only(['longitude', 'latitude']));
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
     *                      property="longitude",
     *                      type="double"),
     *                  @OA\Property(
     *                      property="latitude",
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
                'longitude' => ['required', 'numeric', 'between:-180, 180'],
                'latitude' => ['required', 'numeric', 'between:-90, 90'],
                'municipio_id' => ['required', 'uuid', Rule::exists('municipios_geometria', 'id')],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $result = $this->_pontoUsuarioRepository->Create([
                'id' => uuid_create(UUID_TYPE_RANDOM),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'municipio_id' => $request->input('municipio_id')
            ]);

            $location = route('pontos.show', ['id' => $result->id]);
            return response()->json(['data' => $result], 201)->header('Location', $location);
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
