<?php

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Models\WikiArticle;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Pest\Profanity\Validator;

class WikiArticleController extends Controller
{
    /**
     * se encarga de los articulos de la wiki 
     * 
     * crud de articulos
     * generar el sslug apartir de un nombre y mantenerlo unico
     * publicar y despublicar un articulo
     * asisiar o quitar etiquetas de un articulo
     * filtrar articulos por categoria o etiqueta
     * sigue este 
     * hacer hoy si o si 
     */

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try{
            $articulos=WikiArticle::with('category:id,name' , 'author:id,name', 'tags:id,name');

        if ($request->has('title')) {
            $articulos->where('title', 'ilike', '%' .$request->title . '%');
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
            $articulos->whereHas('tags',fn($q)=>$q->where('slug',$request->tag));
        }

        $articulosPaginated= $articulos->latest()->paginate(10);

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
                'tags' => $articulo->tags->map(fn ($tag) => [
                    'id' => $tag->author->id,
                    'name' => $tag->name,
                ]),                
            ];

        });

            return ApiResponse::success('listado de articulos', 200, $articulosFormated, $paginationData);
        }catch(\Exception $e){
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

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
