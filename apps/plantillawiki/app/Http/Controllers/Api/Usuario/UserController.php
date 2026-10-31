<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * se encarga de los usuarios
     * 
     * crud de usuarios 
     * asignar o camiar la carrera de un usuario
     * asiganar o cambiar el rol de un usuario
     * filtrar usuarios por rol o carrreras 
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $perPage = $validated['perPage'] ?? 1;
        $users = User::query()
            ->select(['id', 'name', 'email', 'carnet_or_code'])
            ->with([
                'roles:id,name,guard_name',
                'permissions:id,name,guard_name',
            ])
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('carnet_or_code', 'ilike', "%{$search}%");
                });
            });

        $userPaginate = $users->paginate($perPage);
        $pagination = [
            'perPage'=>$userPaginate->perPage(),
            'lastPage' => $userPaginate->lastPage(),
            'currentPage' => $userPaginate->currentPage(),
            'total' => $userPaginate->total()
        ];

        $userMap = $userPaginate->map(function ($user) {
            return [
                'nombre' => $user->name,
                'email' => $user->email,
                'carnet' => $user->carnet_or_code,

                // Nombres de los roles asignados
                'roles' => $user->getRoleNames()->values(),

                // Permisos asignados directamente al usuario
                'permisos_directos' => $user->getDirectPermissions()
                    ->pluck('name')
                    ->values()
            ];
        });
        return ApiResponse::success('Listado usuario',200,$userMap,$pagination);
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
