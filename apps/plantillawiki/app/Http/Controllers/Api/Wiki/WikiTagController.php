<?php

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Models\WikiTag;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WikiTagController extends Controller
{
    /**
     * se encarga de los tags de la wiki
     * 
     * crud de tags 
     * generar el sslug apartir de un nombre y mantenerlo unico
     * listar los articulos que pertenecen a una categoria
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $tags = wikiTag::withCount('articles');

            if ($request->has('name')) {
                $tags->where('name', 'ilike', '%' . $request->name . '%');
            }
            if ($request->has('estado')) {
                $tags->where('estado', $request->boolean('estado'));
            }

            $tagsPaginated = $tags->paginate(10);

            $paginationData = [
                'current_page' => $tagsPaginated->currentPage(),
                'last_page' => $tagsPaginated->lastPage(),
                'per_page' => $tagsPaginated->perPage(),
                'total' => $tagsPaginated->total(),
            ];

            $tagsFormated = $tagsPaginated->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'estado' => $tag->estado,
                    'articles_count' => $tag->articles_count,
                ];
            });

            return ApiResponse::success('listado de tags', 200, $tagsFormated, $paginationData);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los tags', 500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $messages = [
                'name.required' => 'El nombre es requerido',
                'name.string' => 'El nombre solo debe tener texto',
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'name.max' => 'Ya existe un tag con ese nombre',
            ];

            $data = $request->all();
            $data['slug'] = Str::slug($request->input('name', ''));

            $validator = Validator::make($data, [
                'name' => 'required|string|min:2|max:50',
                'slug' => 'required|string|max:60|unique:wiki_tags,slug',
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            //creacion tag
            $tag = WikiTag::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            return ApiResponse::success('Tag creado correctamente', 201, $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el tag: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showById(Request $request, $id)
    {
        //
        try {
            $tag = WikiTag::withCount('articles')->find($id);

            if (!$tag) {
                return ApiResponse::error('Tag no encontrado', 404);
            }

            return ApiResponse::success('Tag encontrado', 200, $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al encontrar el tag', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            $tag = WikiTag::find($id);

            if (!$tag) {
                return ApiResponse::error('Tag no encontrado', 404);
            }

            $messages = [
                'name.required' => 'El nombre es requerido',
                'name.string' => 'El nombre solo debe tener texto',
                'name.min' => 'El nombre debe tener al menos 2 caracteres',
                'name.max' => 'El nombre no debe superar los 50 caracteres',
                'slug.unique' => 'Ya exixte un tag con ese nombre',
            ];

            $data = $request->all();
            $data['slug'] = Str::slug($request->input('name', ''));

            $validator = Validator::make($data, [
                'name' => 'required|string|min:2|max:50',
                // ignore() permite conservar su propio slug sin error de duplicado
                'slug' => ['required', 'string', 'max:60', Rule::unique('wiki_tags', 'slug')->ignore($tag->id)],
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            $tag->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            return ApiResponse::success('Tag actualizado exitosamente', 200, $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el tag: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        //
        try {
            $tag = WikiTag::find($id);

            if (!$tag) {
                return ApiResponse::error('Tag no encontrado', 404);
            }

            $tag->estado = !$tag->estado;
            $tag->save();

            return ApiResponse::success('Estado del tag actualizado correcatmente', 200, $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el estado del tag', 500);
        }
    }
}
