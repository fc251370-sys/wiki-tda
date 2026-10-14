<?php

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
