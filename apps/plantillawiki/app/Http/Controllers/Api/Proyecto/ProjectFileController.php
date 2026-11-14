<?php

namespace App\Http\Controllers\Api\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectFileController extends Controller
{
    /**
     * Se encarga de los archivos de cada proyecto.
     *
     * Permite subir, listar, descargar, actualizar y eliminar archivos.
     */

    // Listar archivos de un proyecto
    public function index(string $projectId)
    {
        try {
            $proyecto = Project::find($projectId);

            if (!$proyecto) {
                return ApiResponse::error('Proyecto no encontrado', 404);
            }

            $archivos = ProjectFile::where('project_id', $projectId)
                ->where('estado', true)
                ->get();

            return ApiResponse::success(
                'Listado de archivos del proyecto',
                200,
                $archivos
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al obtener los archivos',
                500
            );
        }
    }

    // Subir un archivo
    public function store(Request $request, string $projectId)
    {
        $ruta = null;

        try {
            $proyecto = Project::find($projectId);

            if (!$proyecto) {
                return ApiResponse::error('Proyecto no encontrado', 404);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    $validator->errors()->first(),
                    422
                );
            }

            $archivo = $request->file('file');
            $extension = strtolower($archivo->getClientOriginalExtension());

            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();

            $ruta = $archivo->storeAs(
                'projects/' . $projectId,
                $nombreArchivo
            );

            if (!$ruta) {
                return ApiResponse::error(
                    'No se pudo guardar el archivo',
                    500
                );
            }

            $tipo = in_array($extension, ['jpg', 'jpeg', 'png'])
                ? 'image'
                : 'document';

            $archivoGuardado = ProjectFile::create([
                'project_id' => $projectId,
                'file_type' => $tipo,
                'original_name' => $archivo->getClientOriginalName(),
                'file_path' => $ruta,
                'file_size_kb' => (int) ceil($archivo->getSize() / 1024),
                'extension' => $extension,
            ]);

            return ApiResponse::success(
                'Archivo subido correctamente',
                201,
                $archivoGuardado
            );

        } catch (\Exception $e) {
            // Si ocurre un error, elimina el archivo que se alcanzó a guardar
            if ($ruta) {
                Storage::delete($ruta);
            }

            return ApiResponse::error(
                'Error al subir el archivo',
                500
            );
        }
    }

    // Descargar un archivo por su ID
    public function show(string $id)
    {
        try {
            $archivo = ProjectFile::where('estado', true)->find($id);

            if (!$archivo) {
                return ApiResponse::error('Archivo no encontrado', 404);
            }

            if (!Storage::exists($archivo->file_path)) {
                return ApiResponse::error(
                    'El archivo no existe en el almacenamiento',
                    404
                );
            }

            return Storage::download(
                $archivo->file_path,
                $archivo->original_name
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al descargar el archivo',
                500
            );
        }
    }

    // Actualizar un archivo
    public function update(Request $request, string $id)
    {
        $rutaNueva = null;

        try {
            $archivoGuardado = ProjectFile::where('estado', true)->find($id);

            if (!$archivoGuardado) {
                return ApiResponse::error('Archivo no encontrado', 404);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error(
                    $validator->errors()->first(),
                    422
                );
            }

            $archivo = $request->file('file');
            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();

            $rutaNueva = $archivo->storeAs(
                'projects/' . $archivoGuardado->project_id,
                $nombreArchivo
            );

            if (!$rutaNueva) {
                return ApiResponse::error(
                    'No se pudo guardar el nuevo archivo',
                    500
                );
            }

            $rutaAnterior = $archivoGuardado->file_path;

            $archivoGuardado->update([
                'file_type' => in_array($extension, ['jpg', 'jpeg', 'png'])
                    ? 'image'
                    : 'document',
                'original_name' => $archivo->getClientOriginalName(),
                'file_path' => $rutaNueva,
                'file_size_kb' => (int) ceil($archivo->getSize() / 1024),
                'extension' => $extension,
            ]);

            // Elimina el archivo anterior después de actualizar sus datos
            Storage::delete($rutaAnterior);

            return ApiResponse::success(
                'Archivo actualizado correctamente',
                200,
                $archivoGuardado
            );

        } catch (\Exception $e) {
            // Si falla la actualización, elimina el archivo nuevo
            if ($rutaNueva) {
                Storage::delete($rutaNueva);
            }

            return ApiResponse::error(
                'Error al actualizar el archivo',
                500
            );
        }
    }

    // Eliminar un archivo
    public function destroy(string $id)
    {
        try {
            $archivo = ProjectFile::where('estado', true)->find($id);

            if (!$archivo) {
                return ApiResponse::error('Archivo no encontrado', 404);
            }

            $archivo->estado = false;
            $archivo->save();

            // Elimina el archivo físico del almacenamiento
            Storage::delete($archivo->file_path);

            return ApiResponse::success(
                'Archivo eliminado correctamente',
                200,
                $archivo
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al eliminar el archivo',
                500
            );
        }
    }
}
