<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Ruta temporal para arreglar el acceso a las imágenes en Render
Route::get('/fix-storage', function () {
    Artisan::call('storage:link');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Storage linked and cache cleared!";
});
