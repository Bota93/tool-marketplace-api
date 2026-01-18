<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Registro de nuevos usuarios.
 * 
 * Valida los datos de entrada, crea el usuario y genera un token
 * de acceso que será usado por el cliente para autenticarse.
 */

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validamos los datos de entrada para asegurar la consistencia
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Creamos el usuario.
        // La contraseña se almacena hasheada por seguridad.
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generamos un token personal para autenticación API
        $token = $user->createToken('api')->plainTextToken;

        // Devolvemos el usuario y el token al cliente
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login de usuario.
     * 
     * Verifica las credenciales y genera un nuevo token de acceso.
     * No utiliza sesiones ni cookies.
     */
    public function login(Request $request)
    {
        // Validamos credenciales básicas
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Buscamos el usuario por email
        $user = User::query()->where('email', $validated['email'])->first();

        // Verificamos que el usuario exista y que la contraseña sea correcta
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales inválidas.'],
            ]);
        }

        // Eliminar tokens anteriores para forzar una sola sesión
        $user->tokens()->delete();

        // Generamos un nuevo token
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Devuelve el usuario autenticado actualmente.
     * 
     * Requiere un token válido enviado en el header Authorization
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Cierra la sesión del usuario.
     * 
     * Revoca el token actual para que no pueda seguir utilizándose.
     */
    public function logout(Request $request)
    {
        // Eliminamos el token que se está usando en esta petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada.',
        ]);
    }
}