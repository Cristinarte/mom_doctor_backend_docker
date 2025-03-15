<?php

use App\Http\Controllers\Api\MedicalLetterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para registro y login:

// Registro de usuario
Route::post('/register', [RegisteredUserController::class, 'store']);

// Login de usuario
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Logout (requiere estar autenticado)
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');


// Rutas para los volantes médicos, solo accesibles para usuarios autenticados
Route::middleware('auth:sanctum')->controller(MedicalLetterController::class)->group(function() {
    Route::get('/medical', 'index');
    Route::post('/medical', 'store');
    Route::get('/medical/{id}', 'show');
    Route::put('/medical/{id}', 'update');
    Route::delete('/medical/{id}', 'destroy');
});
