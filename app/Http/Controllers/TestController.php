<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testUrl()
    {
        return response()->json([
            'url' => config('app.url'), // La URL definida en config/app.php y .env
            'current' => url('/'),      // La URL actual generada por Laravel
            'request' => request()->fullUrl(), // La URL completa de la solicitud entrante
        ]);
    }
}