<?php

namespace App\Http\Controllers\Api\Evento;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * se encarga de los eventos de la wiki
     * 
     * crud de ventos
     * que verifique que la fecha de inicio sea antes de la fecha de fin
     * activar o desactivar un evento
     * listar el evento activo y las materias que tenga 
     */


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $events = Event::with(['coordinador'])->withCount('eventSubjects');

            if ($request->has('cicle_name')) {
                $events->where('cicle_name', 'ilike', '%'. $request->cicle_name . '%');
            }

            if ($request->has('is_active')) {
                $events->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }

            $eventsPaginated = $events->orderByDesc('start_date')->paginate(10);

            $paginationData = [
                'current_page' => $eventsPaginated->currentPage(),
                'last_page' => $eventsPaginated->lastPage(),
                'per_page' => $eventsPaginated->perPage(),
                'total' => $eventsPaginated->total(),
            ];

            $eventsFormated = $eventsPaginated->map(function ($event) {
                return [
                    'id' => $event->id,
                    'cicle_name' => $event->cicle_name,
                    'start_date' => $event->start_date->format('Y-m-d'),
                    'is_active' => $event->is_active,
                    'estado' => $event->estado,
                    'subjects_count' => $event->event_subjects_count,
                    'coordinador' => [
                        'id' => $event->coordinador->id,
                        'name' => $event->coordinador->name,
                        'email' => $event->coordinador->email,
                    ],
                ];
            });

            return ApiResponse::success('listado de eventos', 200, $eventsFormated, $paginationData);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los eventos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $messages = [
                'coordinador_id.required' => 'El coordinador es requerido',
                'coordinador_id.exixts' => 'El coordinador nos existe',
                'cicle_name.required' => 'El nombre del ciclo es requerido',
                'cicle_name.string' => 'EL nombre del ciclo solo debe tener texto',
                'cicle_name.unique' => 'El nombre del ciclo ya exixte',
                'start_date.required' => 'La fecha de inicio es requeridad',
                'start_date.date' => 'La fecha de inicio no es valida',
                'is_active.boolean' => 'EL campo activo debe ser verdadero o falso',
            ];
            $validator = Validator::make($request->all(), [
                'coordinador_id' => 'required|integer|exists:users,id',
                'cicle_name' => 'required|string|min:3|max:50|unique:events,cicle_name',
                'start_date' => 'required|date',
                'is_active' => 'sometimes|boolen',
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            DB::beginTransaction();

            // por defecto un evento nuevo queda activo (defaul de la migracion)
            $isActive = $request->boolean('is_active', true);

            // solo puede haber un evento activo a la vez
            if ($isActive) {
                Event::where('is_active', true)->update(['is_active' => false]);
            }

            $event = Event::create([
                'coordinador_id' => $request->coordinador_id,
                'cicle_name' => $request->cicle_name,
                'start_date' => $request->start_date,
                'is_active' => $isActive,
            ]);
            DB::commit();
            
            return ApiResponse::success('Evento creado correctamente', 201, $event);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al crear el evento ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showById(Request $request, $id)
    {
        // Obtener por id
        try {
            $event = Event::with(['coordinador', 'eventSubjects.teacher', 'eventSubjects.carrera'])->find($id);

            if (!$event) {
                return ApiResponse::error('Evento no encontrado', 404);
            }

            return ApiResponse::success('Evento encontrado', 200, $event);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al mostrar el evento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'coordinador_id.required' => 'El coordinadorno es requerido',
            'coordinador_id.exists' => 'El coordinador no exixte',
            'cicle_name.required' => 'El nombre del ciclo es requerido',
            'cicle_name.unique' => 'El nombre del ciclo ya existe',
            'start_date.required' => 'La fecha de inicio es requerida',
            'start_date.date' => 'La fecha de inicio no es válida',
        ];
        $validator = Validator::make($request->all(), [
            'coordinador_id' => 'required|integer|exists:users,id',
            'cicle_name' => 'required|string|min:3|max:50|unique:events,cicle_name,' . $id,
            'start_date' => 'required|date',
        ], $messages);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors()->first(), 422);
        }

        try {
            DB::beginTransaction();
            $event = Event::find($id);
            if (!$event) {
                DB::rollBack();
                return ApiResponse::error('Evento no encontrado', 404);
            }

            $event->update($request->only(['coordinador_id', 'cicle_name', 'start_date']));
            DB::commit();

            return ApiResponse::success('Evento actualizado exitosamente', 200, $event);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al actualizar el evento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //cambie el esatado igual que en carreras, no borra el registro
        try {
            DB::beginTransaction();
            $event = Event::find($id);
            if (!$event) {
                DB::rollBack();
                return ApiResponse::error('Evento no encontrado', 404);
            }

            $event->estado = !$event->estado;

            //un evento deshabilitado no puede seguir activo

            if (!$event->estado) {
                $event->is_active = false;
            }

            $event->save();
            DB::commit();

            return ApiResponse::success('Estado del evento actualizado correctamente', 200, $event);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al actualizar el estado del evento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Activar o desactivar un evento (solo uno activo a la vez)
     */

    public function toggleActive(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $event = Event::find($id);
            if (!$event) {
                DB::rollBack();
                return ApiResponse::error('Evento no encontrado', 404);
            }

            if (!$event->estado) {
                DB::rollBack();
                return ApiResponse::error('No se puede activar un evento deshabilitado', 422);
            }

            if (!$event->is_active) {
                // al activar este se desactivan los demas
                Event::where('is_active', true)->update(['is_active' => false]);
                $event->is_active = true;
            } else {
                $event->is_active = false;
            }

            $event->save();
            DB::commit();

            return ApiResponse::success(
                $event->is_active ? 'Evento activado correctamente' : 'Evento desactivado correctamente',
                200,
                $event
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al cambiar el estado activo del evento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Listar el evento activo y las materias que tenga
     */
    public function active(Request $request)
    {
        try {
            $event = Event::with(['coordinador', 'eventSubjects.teacher', 'eventSubjects.carrera'])
            ->where('is_active', true)
            ->where('estado', true)
            ->first();

            if (!$event) {
                return ApiResponse::error('No hay un evento activo', 404);
            }

            $eventFormated = [
                'id' => $event->id,
                'cicle_name' => $event->cicle_name,
                'start_date' => $event->start_date->format('Y-m-d'),
                'coordinador' => [
                    'id' => $event->coordinador->id,
                    'name' => $event->coordinador->name,
                ],
                'subjects' => $event->eventSubjects->where('estado', true)->values()->map(fn ($subject) => [
                    'id' => $subject->id,
                    'subject_name' => $subject->subject_name,
                    'teacher' => [
                        'id' => $subject->teacher->id,
                        'name' => $subject->teacher->name,
                    ],
                    'carrera' => [
                        'id' => $subject->carrera->id,
                        'name' => $subject->carrera->name,
                    ],
                ]),
            ];

            return ApiResponse::success('Evento activo', 200, $eventFormated);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener el evento activo: ' . $e->getMessage(), 500);
        }
    }
}
