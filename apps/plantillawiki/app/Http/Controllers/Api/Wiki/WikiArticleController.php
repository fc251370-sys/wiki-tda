<?php

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Models\WikiArticle;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WikiArticleController extends Controller
{
    /**
     * se encarga de los artículos de la wiki
     *
     * crud de artículos
     * el autor es el usuario logueado
     * asigna tags al artículo (tabla article_tag)
     * filtrar por categoría, tag, título, publicado o estado
     * "eliminar" solo alterna el estado
     */

    public function index(Request $request)
    {
        //
        try {
            $articulos = WikiArticle::with('category:id,name', 'author:id,name', 'tags:id,name');

            if ($request->has('title')) {
                $articulos->where('title', 'ilike', '%' . $request->title . '%');
            }
            if ($request->filled('category_id')) {
                $articulos->where('category_id', $request->category_id);
            }
            //solo articulos que tengan el tag (se buscan por slug)
            if ($request->has('is_published')) {
                $articulos->where('is_published', $request->boolean('is_published'));
            }
            if ($request->has('estado')) {
                $articulos->where('estado', $request->boolean('estado'));
            }
            if ($request->filled('tag')) {
                $articulos->whereHas('tags', fn($q) => $q->where('slug', $request->tag));
            }

            $articulosPaginated = $articulos->latest()->paginate(10);

            $paginationData = [
                'current_page' => $articulosPaginated->currentPage(),
                'last_page' => $articulosPaginated->lastPage(),
                'per_page' => $articulosPaginated->perPage(),
                'total' => $articulosPaginated->total(),
            ];

            $articulosFormated = $articulosPaginated->map(function ($articulo) {
                return [
                    'id' => $articulo->id,
                    'title' => $articulo->title,
                    'slug' => $articulo->slug,
                    'is_published' => $articulo->is_published,
                    'estado' => $articulo->estado,
                    'category' => [
                        'id' => $articulo->category->id,
                        'name' => $articulo->name,
                    ],
                    'author' => [
                        'id' => $articulo->author->id,
                        'name' => $articulo->author->name,
                    ],
                    'tags' => $articulo->tags->map(fn($tag) => [
                        'id' => $tag->author->id,
                        'name' => $tag->name,
                    ]),
                ];
            });

            return ApiResponse::success('listado de articulos', 200, $articulosFormated, $paginationData);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener las articulos', 500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $messages = [
                'title.required' => 'El titulo es requerido',
            ];

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:3|max:255',
                'content' => 'required|string',
                //solo categorias y tags activos 
                'categoy_id' => []
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el artículo', 500);
        }
    }

    //obtener por id
    public function showById(Request $request, string $id)
    {
        try {
            $articulo = WikiArticle::with(['category:id,name', 'author:id,name', 'tags:id,name'])->find($id);

            if (!$articulo) {
                return ApiResponse::error('Artículo no encontrado', 404);
            }

            return ApiResponse::success('Artículo encontrado', 200, $articulo);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al mostrar el artículo', 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $articulo = WikiArticle::find($id);

            if (!$articulo) {
                return ApiResponse::error('Artículo no encontrado', 404);
            }

            $messages = [
                'title.required'       => 'El título es requerido',
                'title.string'         => 'El título solo debe tener texto',
                'title.min'            => 'El título debe tener al menos 3 caracteres',
                'content.required'     => 'El contenido es requerido',
                'category_id.required' => 'La categoría es requerida',
                'category_id.exists'   => 'La categoría no existe o está inactiva',
                'tags.array'           => 'Los tags deben enviarse como lista',
                'tags.*.exists'        => 'Algún tag no existe o está inactivo',
            ];

            $validator = Validator::make($request->all(), [
                'title'        => 'sometimes|required|string|min:3|max:255',
                'content'      => 'sometimes|required|string',
                'category_id'  => ['sometimes', 'required', Rule::exists('wiki_categories', 'id')->where('estado', true)],
                'is_published' => 'sometimes|boolean',
                'tags'         => 'sometimes|array',
                'tags.*'       => [Rule::exists('wiki_tags', 'id')->where('estado', true)],
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            DB::beginTransaction();

            // only() en vez de all(): solo se actualizan estos campos
            // (el slug no cambia al editar, así no se rompen los enlaces)
            $articulo->update($request->only(['title', 'content', 'category_id', 'is_published']));

            // si no mandan "tags", se dejan los que ya tenía
            if ($request->has('tags')) {
                $articulo->tags()->sync($request->tags);
            }

            DB::commit();

            $articulo->load(['category:id,name', 'author:id,name', 'tags:id,name']);

            return ApiResponse::success('Artículo actualizado exitosamente', 200, $articulo);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('Error al actualizar el artículo: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $articulo = WikiArticle::find($id);

            if (!$articulo) {
                return ApiResponse::error('Artículo no encontrado', 404);
            }

            $articulo->estado = !$articulo->estado;
            $articulo->save();

            return ApiResponse::success('Estado del artículo actualizado correctamente', 200, $articulo);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el estado del artículo', 500);
        }
    }
}
