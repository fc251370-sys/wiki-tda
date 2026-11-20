<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

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
        try {
            //mensajes
            $messages = [
                'name.required' => 'El nombre es requerido',
                'email.required' => 'El correo es requerido',
                'email.unique' => 'El correo ya existe',
                'carnet_or_code.unique' => 'El carnet o el codigo ya esta registrado',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'La contraseña debe ser al menos de 8 caracteres',
                'carrera_id.exists'=> 'la carrera no existe o esta inactiva',
                'role.required'=> 'El rol es requerido',
                'role.in'=> 'El rol es invalido',
            ];

            $validator = Validator::make($request->all(),[

            //la carrera debe existir y ademas debe estar activa
                'carrera_id' => ['nullable', Rule::exists('carreras', 'id')->where('estado', true)],
                'carnet_or_code' => 'nullable|string|max:50|unique:users,carnet_or_code',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
                //role in solo acepta los roles que estan aca evita que se pongan otros
                'role' => ['required', Rule::in(['admin', 'coordinador', 'teacher', 'student'] )],
            ],$messages);   

            if($validator->fails()){
                return ApiResponse::error($validator->errors()->first(),422);
            }

            $user = DB::transaction(function () use ($request) {

                $user = new User();

                $user->name = $request->name;
                $user->email = $request->email;
                $user->carnet_or_code = $request->carnet_or_code;
                $user->carrera_id = $request->carrera_id;
                $user->password = Hash::make($request->password);

                $user->save();

                //con este se asignas roles al nuevo usuario
                
                $user->assignRole(Role::findByName($request->role, 'api'));

                return $user;
            });

            
            return ApiResponse::success('Usuario creado de forma correcta',201,[
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'carnet' => $user->carnet_or_code,
                'roles' => $user->getRoleNames()->values(),
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Error al crear el usuaio', 500, $e->getMessage());
        };
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
        try {

        //buscar el usuario por su id 
            $user = User::find($id);

            if(!$user){
                return ApiResponse::error('Usuario no encontrado', 404);
            }

            $messages = [
                'email.unique'          => 'Ese correo ya está registrado',
                'carnet_or_code.unique' => 'Ese carnet o código ya está registrado',
                'password.min'          => 'La contraseña debe tener al menos 8 caracteres',
                'carrera_id.exists'     => 'La carrera no existe o está inactiva',
                'role.in'               => 'El rol no es válido',
            ];


            $validator = Validator::make($request->all(), [
                
                'carrera_id' => ['sometimes','nullable', Rule::exists('carreras', 'id')->where('estado', true)],
                //no se puede repetir con otro usuario pero si se puede quedar en el usuario orginal 
                'carnet_or_code' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('users', 'carnet_or_code')->ignore($user->id)],
                'name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
                'password' => 'sometimes|nullable|string|min:8',
                'role' => ['sometimes','required', Rule::in(['admin', 'coordinador', 'teacher', 'student'] )],
            ], $messages);

            if($validator->fails()){
                return ApiResponse::error($validator->errors()->first(),422);
            }

            $user = DB::transaction(function () use ($request, $user) {


                //recorre cada campo de los usuarios que se pueden editar y solo se modifica lo que se envie y lo que no asi queda
                foreach(['name', 'email', 'carnet_or_code', 'carrera_id'] as $campo){
                    if($request->has($campo)){
                        $user->{$campo} = $request->input($campo);
                    }
                }

                //solo cambia si le mandan otra y la encripta si no no 
                if($request->filled('password')){
                    $user->password = Hash::make($request->password);
                }

                $user->save();

                //reemplaza el role anterior por el nuevo
                if($request->filled('role')) {
                    $user->syncRoles([Role::findByName($request->role, 'api')]);
                }
            });

            return ApiResponse::success('Usuario actualizado de forma correcta',200,[
                'id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'carnet' => $user->carnet_or_code,
                'roles' => $user->getRoleNames()->values(),
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Error al actualizar el usuario', 500, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
