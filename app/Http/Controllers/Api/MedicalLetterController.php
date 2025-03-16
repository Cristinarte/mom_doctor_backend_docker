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
        // Validación
        $request->validate([
            'name_children' => 'required|string',
            'specialist_name' => 'required|string',
            'visit_place' => 'required|string',
            'visit_date' => 'required|date',
            'file_path' => 'required|file|mimes:jpg,jpeg,png', // Asegúrate de permitir solo los tipos de archivo correctos
        ]);
    
        try {
            // Subir archivo a Cloudinary
            $uploadedFile = Cloudinary::upload($request->file('file_path')->getRealPath(), [
                'folder' => 'medical_letters',  // Especifica la carpeta
            ]);
    
            // Obtener la URL segura del archivo subido
            $filePath = $uploadedFile->getSecureUrl();
    
            // Crear el volante con la URL de Cloudinary
            $volante = new Volante([
                'name_children' => $request->input('name_children'),
                'specialist_name' => $request->input('specialist_name'),
                'visit_place' => $request->input('visit_place'),
                'visit_date' => $request->input('visit_date'),
                'file_path' => $filePath, // Guardamos la URL de Cloudinary
            ]);
    
            // Guardar el volante en la base de datos
            $volante->save();
    
            return response()->json(['message' => 'Volante creado con éxito', 'volante' => $volante], 201);
    
        } catch (\Exception $e) {
            // Log de error detallado para depuración
            Log::error('Error al subir el archivo a Cloudinary:', ['error' => $e->getMessage()]);
    
            return response()->json([
                'message' => 'Error al subir el archivo. Por favor, revisa los logs para más detalles.',
                'error' => $e->getMessage() // Incluir el error para depuración
            ], 500);
        }
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
            $path = $file->store('medical_letters', 'public');
            if (!$path) {
                Log::error('Error al guardar el archivo.');
                return response()->json(['message' => 'Error al guardar el archivo'], 500);
            }
            $dataToUpdate['file_path'] = $path;
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