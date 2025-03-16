<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MedicalLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MedicalLetterController extends Controller
{
    public function index()
    {
        $medicalLetters = MedicalLetter::where('user_id', auth()->id())->get();
        return response()->json($medicalLetters);
    }

    public function store(Request $request)
    {
        Log::info('Datos recibidos para crear volante:', $request->all());
    
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name_children' => 'required|string',
            'specialist_name' => 'nullable|string|max:255',
            'file_path' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
            'visit_place' => 'required|string',
            'visit_date' => 'required|date',
        ]);
    
        Log::info('Datos validados correctamente:', $validated);
    
        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            try {
                $filePath = $request->file('file_path')->store('medical_letters', 'cloudinary');
                Log::info('Archivo subido a Cloudinary:', ['file_path' => $filePath]);
            } catch (\Exception $e) {
                Log::error('Error al subir el archivo a Cloudinary:', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Error al subir el archivo'], 500);
            }
        } else {
            Log::error('Archivo no válido o no proporcionado');
            return response()->json(['message' => 'Archivo no válido'], 400);
        }
    
        $userId = auth()->id();
        $medicalLetter = MedicalLetter::create([
            'user_id' => $userId,
            'name_children' => $validated['name_children'],
            'specialist_name' => $validated['specialist_name'],
            'file_path' => $filePath,
            'visit_place' => $validated['visit_place'],
            'visit_date' => $validated['visit_date'],
        ]);
    
        Log::info('Volante creado correctamente:', $medicalLetter->toArray());
    
        return response()->json($medicalLetter, 201);
    }
    

    public function show($id)
    {
        $medicalLetter = MedicalLetter::find($id);
        if (!$medicalLetter || $medicalLetter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Volante no encontrado o no autorizado'], 404);
        }
        return response()->json($medicalLetter);
    }

    public function update(Request $request, $id)
    {
        Log::info('Datos recibidos para actualizar:', $request->all());

        $medicalLetter = MedicalLetter::find($id);
        if (!$medicalLetter || $medicalLetter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Volante no encontrado o no autorizado'], 404);
        }

        $validated = $request->validate([
            'name_children' => 'sometimes|required|string',
            'specialist_name' => 'nullable|string|max:255',
            'visit_place' => 'sometimes|required|string',
            'visit_date' => 'sometimes|required|date',
            'file_path' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $dataToUpdate = array_filter($validated, function ($value) {
            return !is_null($value);
        });

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            // Subir el archivo a Cloudinary
            $uploadedFile = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'medical_letters', // Puedes especificar una carpeta
            ]);
            $filePath = $uploadedFile->getSecureUrl(); // Obtenemos la URL segura de Cloudinary
            $dataToUpdate['file_path'] = $filePath; // Actualizamos el campo file_path con la nueva URL
        }

        if (empty($dataToUpdate)) {
            Log::warning('No se enviaron datos para actualizar.');
            return response()->json([
                'message' => 'No se enviaron datos para actualizar',
                'volante' => $medicalLetter,
            ], 200);
        }

        $updated = $medicalLetter->update($dataToUpdate);

        if (!$updated) {
            Log::warning('No se realizaron cambios en la base de datos.');
            return response()->json([
                'message' => 'No se realizaron cambios',
                'volante' => $medicalLetter,
            ], 200);
        }

        Log::info('Volante actualizado correctamente:', $medicalLetter->toArray());

        return response()->json([
            'message' => 'Volante actualizado correctamente',
            'volante' => $medicalLetter->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $medicalLetter = MedicalLetter::find($id);
        if (!$medicalLetter || $medicalLetter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Volante no encontrado o no autorizado'], 404);
        }
        $medicalLetter->delete();
        return response()->json(['message' => 'Volante médico eliminado']);
    }
}
