<?php

namespace App\Http\Controllers\Api\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Response\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * se encarga de los proyectos de cada materia de el evento
     * 
     * crud de proyectos
     * actualizar el estado de un proyecto
     * agregar y quitar miembros de un equipo de proyecto
     * designar el lider de el proyecto
     * guardar y actualizar la url de el repositorio
     */

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $proyectos = Project::with([
                'eventSubject',
                'members',
                'files'
            ])->paginate(10);

            $paginationData = [
                'current_page' => $proyectos->currentPage(),
                'last_page' => $proyectos->lastPage(),
                'per_page' => $proyectos->perPage(),
                'total' => $proyectos->total(),
            ];

            return ApiResponse::success(
                'Listado de Proyectos',
                200,
                $proyectos->items(),
                $paginationData
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al obtener los proyectos',
                500
            );
        }
    } 

    /**
     * Store a newly created resource in storage.
     */
    
public function store(Request $request)
{
    try {
        $messages = [
            'event_subject_id.required' => 'La materia es requerida',
            'event_subject_id.exists' => 'La materia seleccionada no existe',
            'title.required' => 'El título del proyecto es requerido',
            'title.string' => 'El título debe ser texto',
            'repository_url.url' => 'La URL del repositorio no es válida',
        ];

        $validator = Validator::make($request->all(), [
            'event_subject_id' => 'required|exists:event_subjects,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'repository_url' => 'nullable|url|max:255',
        ], $messages);

        if ($validator->fails()) {
            return ApiResponse::error(
                $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        $proyecto = Project::create($request->only([
            'event_subject_id',
            'title',
            'description',
            'repository_url'
        ]));

        DB::commit();

        return ApiResponse::success(
            'Proyecto creado exitosamente',
            201,
            $proyecto
        );

    } catch (\Exception $e) {
        DB::rollBack();

        return ApiResponse::error(
            'Error al crear el proyecto',
            500
        );
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            $proyecto = Project::with([
                'eventSubject',
                'members',
                'files'
            ])->find($id);

            if (!$proyecto) {
                return ApiResponse::error(
                    'Proyecto no encontrado',
                    404
                );
            }

            return ApiResponse::success(
                'Proyecto encontrado',
                200,
                $proyecto
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al mostrar el proyecto',
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $proyecto = Project::find($id);

            if (!$proyecto) {
                return ApiResponse::error(
                    'Proyecto no encontrado',
                    404
                );
            }

            $validator = Validator::make($request->all(), [
                'event_subject_id' => 'sometimes|required|exists:event_subjects,id',
                'title' => 'sometimes|required|string|max:200',
                'description' => 'nullable|string',
                'repository_url' => 'nullable|string|max:255',
                'status' => 'sometimes|required|string|max:20',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    $validator->errors()->first(),
                    422
                );
            }

            DB::beginTransaction();

            $proyecto->update($request->only([
                'event_subject_id',
                'title',
                'description',
                'repository_url',
                'status'
            ]));

            DB::commit();

            return ApiResponse::success(
                'Proyecto actualizado exitosamente',
                200,
                $proyecto
            );
        } catch (\Exception $e) {
            DB::rollback();

            return ApiResponse::error(
                'Error al actualizar el proyecto',
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $proyecto = Project::find($id);

            if (!$proyecto) {
                return ApiResponse::error(
                    'Proyecto no encontrado',
                    404
                );
            }

            $proyecto->estado = false;
            $proyecto->save();

            return ApiResponse::success(
                'Proyecto desactivado exitosamente',
                200,
                $proyecto
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al desactivar el proyecto',
                500
            );
        }
    }
}
