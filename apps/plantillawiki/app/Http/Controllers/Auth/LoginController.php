<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Response\ApiResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Login
     *
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (! $token = auth('api')->attempt($credentials)) {
                return response()->json([
                    'message' => 'Credenciales incorrectas',
                ], 401);
            }

            $user = auth('api')->user();
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');
            $data['token'] = $token;
            $data['user'] = $user;

            return ApiResponse::success('Inicio de sesión exitoso', 200, $data);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al iniciar sesión', 500);
        }
    }

    /**
     * Logout
     *
     */
    public function logout(){
        try {
            auth('api')->logout();
           return ApiResponse::success("Sesión cerrada");
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al desconectarse',
            ], 500);
        }
    }

    /**
     * Refresh
     *
     */
    public function refresh()
    {
        try {
            $token = auth('api')->refresh();
            $user = auth('api')->user();
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');

            return response()->json([
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al refrescar el token',
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = auth('api')->user();
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');

            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el usuario',
            ], 500);
        }
    }
}
