<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\PontosController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/localizar-municipio', [MunicipioController::class, 'ShowByLatLong']);
Route::get('/pontos/{id}', [PontosController::class, 'ShowById']);
Route::delete('/pontos/{id}', [PontosController::class, 'RemoveById']);
Route::put('/pontos/{id}', [PontosController::class, 'Update']);
Route::post('/pontos', [PontosController::class, 'Create']);