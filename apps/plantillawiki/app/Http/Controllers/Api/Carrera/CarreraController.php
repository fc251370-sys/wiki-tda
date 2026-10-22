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
        try {
            $carreras = Carrera::with(['users']);
                if($request->has('name')){
                $carreras->where('name','ilike','%'.$request->name.'%');
            }
            
            $carrerasPaginated = $carreras->paginate(10);

            $paginationData = [
                'current_page' => $carrerasPaginated->currentPage(),
                'last_page' => $carrerasPaginated->lastPage(),
                'per_page' => $carrerasPaginated->perPage(),
                'total' => $carrerasPaginated->total(),
            ];
            
            $carrerasFormated = $carrerasPaginated->map(function ($carrera) {
                return [
                    'id' => $carrera->id,
                    'name' => $carrera->name,
                    'code' => $carrera->code,
                    'estado' => $carrera->estado,
                    'users' => $carrera->users->map(fn ($user) => [ 
                        'id'=> $user->id,
                        'name' => $carrera->users->name,
                        'email' => $carrera->users->email,
                    ]),
                ];
            });
        return ApiResponse::success('listado de carreras',200, $carrerasFormated,$paginationData);

        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los carreras',500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $messages = [
                'name.required'=>'Los nombres son requeridos',
                'name.string'=>'Los nombres solo deben tener texto',
                'code.string'=>'El código solo debe tener texto',
                'code.unique'=>'El código ya existe'
            ];
            $validator = Validator::make($request->all(),[
                'name' => 'required|string|max:150,min:3',
                'code' => 'nullable|string|min:3|max:150|unique:carreras,code',
            ],$messages);
            
            if($validator->fails()){
                return ApiResponse::error($validator->errors()->first(),422);
            }

            //creacion carrera
            $carrera = Carrera::create([
                'name'=>$request->name,
                'code'=>$request->code,
            ]);

            return ApiResponse::success('Carrera creada correctamente',201);
        } catch (\Exception $e) {
            //throw $th;
            return ApiResponse::error('Error al crear la carrera '.$e->getMessage());
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
