<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

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
            return ApiResponse::success('Sesión cerrada');
        } catch (JWTException $e) {
            return ApiResponse::error('La sesión no pudo invalidarse', 401);
        } catch (\Throwable $e) {
            report($e);
            return ApiResponse::error('Error al cerrar sesión', 500);
        }
    }

    /**
     * Refresh
     *
     */
    public function refresh()
    {
        try {
            $guard = auth('api');
            $token = $guard->refresh();
            $user = $guard->setToken($token)->user();

            if (! $user) {
                return ApiResponse::error('La sesión ya no es válida', 401);
            }

            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');

            return ApiResponse::success('Token actualizado', 200, [
                'token' => $token,
                'user' => $user,
            ]);
        } catch (JWTException $e) {
            return ApiResponse::error('La sesión expiró. Inicia sesión nuevamente.', 401);
        } catch (\Throwable $e) {
            report($e);
            return ApiResponse::error('Error al renovar la sesión', 500);
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
