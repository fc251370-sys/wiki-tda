<?php

namespace App\Http\Controllers\Api\Carrera;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CarreraController extends Controller
{
    /**
     * se encarga de todas las carreras
     *
     * crud de carreras
     * verifica que el codigo de la carrera sea unico
     * eliminar = cambiar el estado (activa / inactiva)
     */

    /**
     * Listado de carreras (con filtros opcionales: name, estado)
     */
    public function index(Request $request)
    {
        try {
            $carreras = Carrera::with(['users']);

            if ($request->has('name')) {
                $carreras->where('name', 'ilike', '%' . $request->name . '%');
            }
            if ($request->has('estado')) {
                $carreras->where('estado', $request->boolean('estado'));
            }

            $carrerasPaginated = $carreras->paginate(10);

            $paginationData = [
                'current_page' => $carrerasPaginated->currentPage(),
                'last_page'    => $carrerasPaginated->lastPage(),
                'per_page'     => $carrerasPaginated->perPage(),
                'total'        => $carrerasPaginated->total(),
            ];

            $carrerasFormated = $carrerasPaginated->map(function ($carrera) {
                return [
                    'id'     => $carrera->id,
                    'name'   => $carrera->name,
                    'code'   => $carrera->code,
                    'estado' => $carrera->estado,
                    // $carrera->users es una colección: cada $user es un usuario
                    'users'  => $carrera->users->map(fn ($user) => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                    ]),
                ];
            });

            return ApiResponse::success('listado de carreras', 200, $carrerasFormated, $paginationData);
        } catch (\Throwable $e) {
            Log::error('Carreras index: ' . $e->getMessage());
            return ApiResponse::error('Error al obtener los carreras', 500, $e->getMessage());
        }
    }

    /**
     * Crear carrera
     */
    public function store(Request $request)
    {
        try {
            $messages = [
                'name.required' => 'Los nombres son requeridos',
                'name.string'   => 'Los nombres solo deben tener texto',
                'code.string'   => 'El código solo debe tener texto',
                'code.unique'   => 'El código ya existe',
            ];

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:150',
                'code' => 'nullable|string|min:3|max:150|unique:carreras,code',
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            Carrera::create([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return ApiResponse::success('Carrera creada correctamente', 201);
        } catch (\Throwable $e) {
            Log::error('Carreras store: ' . $e->getMessage());
            return ApiResponse::error('Error al crear la carrera: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Obtener carrera por id
     */
    public function showById(Request $request, $id)
    {
        try {
            $carrera = Carrera::find($id);

            if (!$carrera) {
                return ApiResponse::error('Carrera no encontrada', 404);
            }

            return ApiResponse::success('Carrera encontrada', 200, $carrera);
        } catch (\Throwable $e) {
            Log::error('Carreras show: ' . $e->getMessage());
            return ApiResponse::error('Error al mostrar la carrera', 500);
        }
    }

    /**
     * Actualizar carrera
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
            $carrera = Carrera::find($id);

            if (!$carrera) {
                return ApiResponse::error('Carrera no encontrada', 404);
            }

            DB::beginTransaction();
            // solo se permiten estos campos (evita que cambien el estado por aquí)
            $carrera->update($request->only(['name', 'code']));
            DB::commit();

            return ApiResponse::success('Carrera actualizada exitosamente', 200, $carrera);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Carreras update: ' . $e->getMessage());
            return ApiResponse::error('Error al actualizar la carrera: ' . $e->getMessage(), 500);
        }
    }

    /**
     * "Eliminar" = activar/desactivar la carrera (cambia el estado)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $carrera = Carrera::find($id);

            if (!$carrera) {
                return ApiResponse::error('Carrera no encontrada', 404);
            }

            DB::beginTransaction();
            $carrera->estado = !$carrera->estado;
            $carrera->save();
            DB::commit();

            return ApiResponse::success('Estado de la carrera actualizado correctamente', 200, $carrera);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Carreras destroy: ' . $e->getMessage());
            return ApiResponse::error('Error al actualizar el estado de la carrera', 500);
        }
    }
}