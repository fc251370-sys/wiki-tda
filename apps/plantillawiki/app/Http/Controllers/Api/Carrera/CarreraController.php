<?php

namespace App\Http\Controllers\Api\Carrera;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarreraController extends Controller
{
    /**
     * se encarga de todas las carreras 
     * 
     * crud de carreras 
     * verifica que el id de la carrera sea unico 
     * impide eliminar una carrera si tiene materias asoaciadas
     */

    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $carreras = Carrera::all();
        return ApiResponse::success('Listado carreras', 200, $carreras);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string',
            ]);

            $carrera = Carrera::create($request->all());

            return ApiResponse::success('Carrera creada exitosamente', 201, $carrera);
        } catch (\Exception $e) {
            return ApiResponse::error('Error no se creo la carrera: ' . $e->getMessage(), 500);
        }
    }

    //obtener por id
    public function showById(Request $request, $id)
    {
        try{
            $carrera = Carrera::find($id);

            return ApiResponse::success('Carrera encontrada',200,$carrera);
        }catch (\Exception $e) {
            return ApiResponse::error('Error al mostrar la carrera',500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string',
        ], [
            'name.required' => 'El nombre es requerido',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }
        try {
            \DB::beginTransaction();
            $carrera = Carrera::find($id);
            if (!$carrera) {
                return ApiResponse::error('Carrera no encontrada', 404);
            }

            $carrera->update($request->all());
            \DB::commit();

            return ApiResponse::success('Carrera actualizada exitosamente', 200, $carrera);
        } catch (\Exception $e) {
            \DB::rollBack();
            return ApiResponse::error('Error al actualizar la carrera: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        //
        try{
            \DB::beginTransaction();
            $carrera = Carrera::find($id);
            $carrera->estado = !$carrera->estado;
            \DB::commit();
            $carrera->save();
            return ApiResponse::success('Estado del carrera actualizado correctamente',200,$carrera);
        }catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el estado del carrera',500);
        }

    }
}
