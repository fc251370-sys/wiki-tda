<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /**
     * Roles
     *
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return ApiResponse::success('Roles obtenidos correctamente', 200, $roles);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los roles', 500);
        }
    }

    /**
     * Permisos
     *
     */
    public function getPermissions()
    {
        try {
            $permissions = Permission::all();
            return ApiResponse::success('Permisos obtenidos correctamente', 200, $permissions);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al obtener los permisos', 500);
        }
    }

    /**
     * Crear roles
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error al validar el rol',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'api'
            ]);

            if ($request->filled('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json($role, 201);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Error al crear el rol',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Editar roles
     *
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'permissions_to_add' => 'nullable|array',
                'permissions_to_add.*' => 'string',
                'permissions_to_remove' => 'nullable|array',
                'permissions_to_remove.*' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error al validar el rol',
                    'errors' => $validator->errors(),
                ], 400);
            }
            
            $role = Role::find($id);
            if($request->filled('name')){
                $role->name = $request->name;
            }
            $role->save();

            if ($request->filled('permissions_to_add')) {
                $role->givePermissionTo($request->permissions_to_add);
            }

            if ($request->filled('permissions_to_remove')) {
                $role->revokePermissionTo($request->permissions_to_remove);
            }

            return response()->json($role);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el rol',
            ], 500);
        }
    }

    /**
     * Eliminar roles
     *
     */
    public function destroy($id)
    {
        try {
            $role = Role::find($id);
            $role->delete();
            return response()->json($role);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el rol',
            ], 500);
        }
    }
}
