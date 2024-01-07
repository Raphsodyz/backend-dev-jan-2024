<?php

namespace App\Http\Controllers;
use App\Models\PontoUsuario;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PontosController extends Controller
{
    /** 
     * @OA\Get(
     *      path="/api/pontos",
     *      summary="Method for find ponto by his uuid.",
     *      tags={"Pontos Controller"},
     *      @OA\Parameter(
     *          in="query",
     *          name="ID",
     *          @OA\Schema(type="uuid"),
     *      ),
     *      @OA\Response(response="200", description="Data found!"),
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function ShowById(Request $request)
    {
        try
        {
            $idPonto = $request->input('ID');

            $result = PontoUsuario::where('id', '=', $idPonto)->first();
            if(!$result)
                return response()->json(['Error' => 'Not found ponto with mentioned id'], 404);
            
            return response()->json([$result], 200);
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
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function RemoveById($id)
    {
        try
        {
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
     *      @OA\Response(response="404", description="Resource not found with the informed ID."),
     *      @OA\Response(response="500", description="Unavailable service.")
     * )
    */
    public function Update(Request $request, $id)
    {
        try
        {
            $ponto = PontoUsuario::findOrFail($id);

            DB::transaction(function() use ($ponto, $request)
            {
                $ponto->update($request->all());
                $result = $ponto->withoutRelations();
            });

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
}
