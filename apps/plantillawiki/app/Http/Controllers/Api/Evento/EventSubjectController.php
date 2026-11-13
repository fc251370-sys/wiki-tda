<?php

namespace App\Http\Controllers\Api\Evento;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSubject;
use App\Models\User;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventSubjectController extends Controller
{
    /**
     * se encarga de las materias que estan dentro de un evento 
     * 
     * crud de materias de unevento
     * asiganar el docente y la carrera de cada materia
     * validar que el usuario tenga el rol de docemte
     * listar los proyectos de cada materia
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $eventId)
    {
        //
        try {
            $event = Event::find($eventId);
            if (!$event) {
                return ApiResponse::error('Evento no encontrado', 404);
            }

            $subjects = EventSubject::with(['teacher', 'carrera'])->withCount('projects')->where('event_id', $eventId);

            if ($request->has('subject_name')) {
                $subjects->where('subject_name', 'like', '%' . $request->subject_name . '%');
            }

            if ($request->has('carrera_id')) {
                $subjects->where('carrera_id', $request->carrera_id);
            }

            if ($request->has('teacher_id')) {
                $subjects->where('teacher_id', $request->teacher_id);
            }

            $subjectsPaginated = $subjects->paginate(10);

            $paginationData = [
                'current_page' => $subjectsPaginated->currentPage(),
                'last_page' => $subjectsPaginated->lastPage(),
                'per_page' => $subjectsPaginated->perPage(),
                'total' => $subjectsPaginated->total(),
            ];

            $subjectsFormated = $subjectsPaginated->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_name' => $subject->subject_name,
                    'estado' => $subject->estado,
                    'projects_count' => $subject->projects_count,
                    'teacher' => [
                        'id' => $subject->teacher->id,
                        'name' => $subject->teacher->name,
                        'email' => $subject->teacher->email,
                    ],
                    'carrera' => [
                        'id' => $subject->carrera->id,
                        'name' => $subject->carrera->name,
                        'code' => $subject->carrera->code,
                    ],
                ];
            });

            return ApiResponse::success('listado de materias del evento', 200, $subjectsFormated, $paginationData);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener las materias del evento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $eventId)
    {
        try {
            $event = Event::find($eventId);
            if (!$event) {
                return ApiResponse::error('Evento no encontrado', 404);
            }

            $messages = [
                'subject_name.required' => 'El nombre de la materia es requerido',
                'subject_name.string' => 'El nombre de la materia solo debe tener texto',
                'teacher_id.required' => 'El docente es requerido',
                'teacher_id.exists' => 'El docente no existe',
                'carrera_id.required' => 'La carrera es requerida',
                'carrera_id.exists' => 'La carrera no existe',
            ];
            $validator = Validator::make($request->all(), [
                'subject_name' => 'required|string|min:3|max:150',
                'teacher_id' => 'required|integer|exists:users,id',
                'carrera_id' => 'required|integer|exists:carreras,id'
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            // se valida que el usuario tenga el rol de docente
            $teacher = User::find($request->teacher_id);
            if (!$teacher->hasRole('teacher')) {
                return ApiResponse::error('El usuario seleccionado no tiene el rol de docente', 422);
            }

            DB::beginTransaction();
            $subject = EventSubject::create([
                'event_id' => $eventId,
                'teacher_id' => $request->teacher_id,
                'carrera_id' => $request->carrera_id,
                'subject_name' => $request->subject_name,
            ]);
            DB::commit();

            return ApiResponse::success('Materia creada correctamente', 201, $subject);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al crear la materia ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showById(Request $request, $eventId, $id)
    {
        try {
            $subject = EventSubject::with(['teacher', 'carrera'])->where('event_id', $eventId)->find($id);

            if (!$subject) {
                return ApiResponse::error('Materia no encontrada', 404);
            }

            return ApiResponse::success('Materia encontrada', 200, $subject);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al mostrar la materia: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $eventId, $id)
    {
        $messages = [
            'subject_name.required' => 'El nombre de la materia es requerido',
            'teacher_id.required' => 'El docente es requerido',
            'teacher_id.exists' => 'EL docente no existe',
            'carreara_id.required' => 'La carrera es requerida',
            'carrera_id.exists' => 'La carrera no existe',
        ];
        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|min:3|max:150',
            'teacher_id' => 'required|integer|exists:users,id',
            'carrera_id' => 'required|integer|exists:carreras,id',
        ], $messages);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        try {
            $subject = EventSubject::where('event_id', $eventId)->find($id);
            if (!$subject) {
                return ApiResponse::error('Materia no encontrada', 404);
            }

            // se valida que el usuario tenga el rol de docente
            $teacher = User::find($request->teacher_id);
            if (!$teacher->hasRole('teacher')) {
                return ApiResponse::error('El usuario seleccionado no tiene el rol de docente', 422);
            }

            DB::beginTransaction();
            $subject->update($request->only(['subject_name', 'teacher_id', 'carrera_id']));
            DB::commit();

            return ApiResponse::success('Materia actualizada exitosamente', 200, $subject);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al actualizar la materia: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $eventId, $id)
    {
        try {
            DB::beginTransaction();
            $subject = EventSubject::where('event_id', $eventId)->find($id);
            if (!$subject) {
                DB::rollBack();
                return ApiResponse::error('Materia no encontrada', 404);
            }

            $subject->estado = !$subject->estado;
            $subject->save();
            DB::commit();

            return ApiResponse::success('Estado de la materia actualizado correctamente', 200, $subject);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al actualizar el estado de la materia: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Listar los proyectos de una materia
     */
    public function projects(Request $request, $eventId, $id)
    {
        try {
            $subject = EventSubject::where('event_id', $eventId)->find($id);
            if (!$subject) {
                return ApiResponse::error('Materia no encontrada', 404);
            }

            $projects = $subject->projects()->withCount('members');

            if ($request->has('title')) {
                $projects->where('title', 'ilike', '%' . $request->title . '%');
            }

            if ($request->has('status')) {
                $projects->where('status', $request->status);
            }

            $projectsPaginated = $projects->paginate(10);

            $paginationData = [
                'current_page' => $projectsPaginated->currentPage(),
                'last_page' => $projectsPaginated->lastPage(),
                'per_page' => $projectsPaginated->perPage(),
                'total' => $projectsPaginated->total(),
            ];

            $projectsFormated = $projectsPaginated->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->description,
                    'repository_url' => $project->repository_url,
                    'status' => $project->status,
                    'members_count' => $project->members_count,
                ];
            });

            return ApiResponse::success('listado de proyectos de la materia', 200, $projectsFormated, $paginationData);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los proyectos de la materia: ' . $e->getMessage(), 500);
        }
    }
}
