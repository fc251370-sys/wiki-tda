<?php

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Models\WikiCategory;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WikiCategoryController extends Controller
{
    /**
     * se encarga de las categorias de la wiki
     * 
     * crud de categorias 
     * generar el sslug apartir de un nombre y mantenerlo unico 
     * impedir que se eliminen categorias que tengan articulos asociados
     */
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try{
            $categorias=WikiCategory::withCount('articles');

        if ($request->has('name')) {
            $categorias->where('name', 'ilike', '%' .$request->name . '%');
        }
        if ($request->has('estado')) {
            $categorias->where('estado', $request->boolean('estado'));
        }

        $categoriasPaginated= $categorias->paginate(10);

        $paginationData = [
            'current_page' => $categoriasPaginated->currentPage(),
            'last_page' => $categoriasPaginated->lastPage(),
            'per_page' => $categoriasPaginated->perPage(),
            'total' => $categoriasPaginated->total(),
        ];

        $categoriasFormated = $categoriasPaginated->map(function ($categoria) {
            return [
                'id' => $categoria->id,
                'name' => $categoria->name,
                'slug' => $categoria->slug,
                'description' => $categoria->description,
                'articles_count' => $categoria->articles_count,
            ];

        });

            return ApiResponse::success('listado de categorias', 200, $categoriasFormated, $paginationData);
        }catch(\Exception $e){
            return ApiResponse::error('Error al obtener las categorias', 500, $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $messages = [
                'name.required' => 'El nombre es requerdo',
                'name.string' => 'El nombre debe ser una cadena de texto',
                'name.min' => 'El nombre debe tener al menos 3 caracteres',
                'name.max' => 'El nombre no debe superar los 100 caracteres',
                'slug.unique' => 'Ya existe una categoria con ese nombre',
            ];

            //el slug se arma antes de validar porque es el campo unico en la BD
            $data = $request->all();
            $data['slug'] = Str::slug($request->input('name', ''));

            $validator = Validator::make($data, [
                'name' => 'required|string|min:3|max:100',
                'slug' => 'required|string|max:120|unique:wiki_categories,slug',
                'description' =>'nullable|string',
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
           }

           //crear la categoria 
           $categoria = WikiCategory::create([ 
            'name' => $request->input('name'),
            'slug' => $data['slug'],
            'description' => $request->input('description')
        ]);

           return ApiResponse::success('Categoria creada correctamente', 201, $categoria);

        }catch(\Exception $e){
            return ApiResponse::error('Error al crear la categoria', 500, $e->getMessage());

        }
        
    }

     //obtener por id
    public function showById(Request $request, $id)
    {
        try {
            $categoria = WikiCategory::withCount('articles')->find($id);

            if (!$categoria) {
                return ApiResponse::error('Categoría no encontrada', 404);
            }

            return ApiResponse::success('Categoría encontrada', 200, $categoria);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al mostrar la categoría', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $categoria = WikiCategory::find($id);

            if (!$categoria) {
                return ApiResponse::error('Categoría no encontrada', 404);
            }

            $messages = [
                'name.required' => 'El nombre es requerido',
                'name.string'   => 'El nombre solo debe tener texto',
                'name.min'      => 'El nombre debe tener al menos 3 caracteres',
                'name.max'      => 'El nombre no debe superar los 100 caracteres',
                'slug.unique'   => 'Ya existe una categoría con ese nombre',
            ];

            $data = $request->all();
            $data['slug'] = Str::slug($request->input('name', ''));

            $validator = Validator::make($data, [
                'name'        => 'required|string|min:3|max:100',
                // ignore() permite conservar su propio slug sin error de duplicado
                'slug'        => ['required', 'string', 'max:120', Rule::unique('wiki_categories', 'slug')->ignore($categoria->id)],
                'description' => 'nullable|string',
            ], $messages);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors()->first(), 422);
            }

            $categoria->update(array_merge(
                ['name' => $data['name'], 'slug' => $data['slug']],
                $request->only('description')
            ));

            return ApiResponse::success('Categoría actualizada exitosamente', 200, $categoria);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar la categoría: ' . $e->getMessage(), 500);
        }
    }

    /**
     * No borra: alterna el estado (activa / inactiva)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $categoria = WikiCategory::find($id);

            if (!$categoria) {
                return ApiResponse::error('Categoría no encontrada', 404);
            }

            // si está activa y aún tiene artículos activos, no se puede desactivar
            if ($categoria->estado && $categoria->articles()->where('estado', true)->exists()) {
                return ApiResponse::error('No se puede desactivar: la categoría tiene artículos activos', 409);
            }

            $categoria->estado = !$categoria->estado;
            $categoria->save();

            return ApiResponse::success('Estado de la categoría actualizado correctamente', 200, $categoria);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el estado de la categoría', 500);
        }
    }
}
