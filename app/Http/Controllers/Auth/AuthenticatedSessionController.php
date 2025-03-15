<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Manejar una solicitud de autenticación entrante.
     * 
     * Intenta autenticar al usuario utilizando las credenciales proporcionadas (email y contraseña).
     * Si la autenticación es exitosa, se genera un token de Sanctum y se devuelve al usuario.
     * Si las credenciales no son válidas, devuelve un error 401 (no autorizado).
     * 
     * @param \Illuminate\Http\Request $request La solicitud entrante que contiene las credenciales del usuario.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con el token de acceso o un error de autenticación.
     */
    public function store(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validar las credenciales
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Crear un token de Sanctum
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Eliminar una sesión autenticada.
     * 
     * Elimina el token de acceso actual del usuario, cerrando la sesión de forma efectiva.
     * 
     * @param \Illuminate\Http\Request $request La solicitud entrante que contiene al usuario autenticado.
     * @return \Illuminate\Http\Response La respuesta sin contenido, indicando que la operación fue exitosa.
     */
    public function destroy(Request $request): Response
    {
        // Elimina el token actual del usuario
        $request->user()->currentAccessToken()->delete();

        // Devuelve una respuesta exitosa sin contenido
        return response()->noContent();
    }
}
