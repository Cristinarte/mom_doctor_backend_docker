<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\MedicalLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalLetterController extends Controller
{
    /**
     * Obtener todas las cartas médicas del usuario autenticado.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $medicalLetters = MedicalLetter::where('user_id', auth()->id())->get();
        return response()->json($medicalLetters);
    }

    /**
     * Crear una nueva carta médica.
     *
     * Valida los datos de la solicitud y guarda la nueva carta médica
     * asociada al usuario autenticado. Si se incluye un archivo en la solicitud,
     * este se guarda en el sistema de almacenamiento público.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name_children' => 'required|string',
            'specialist_name' => 'nullable|string|max:255',
            'file_path' => 'required|file|mimes:jpg,jpeg,png,gif|max:2048',
            'visit_place' => 'required|string',
            'visit_date' => 'required|date',
        ]);

        if ($request->hasFile('file_path') && $request->file('file_path')->isValid()) {
            $filePath = $request->file('file_path')->store('medical_letters', 'public');
        } else {
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

        return response()->json($medicalLetter, 201);
    }

    /**
     * Obtener una carta médica específica por ID.
     * 
     * @param int $id El ID de la carta médica.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $medicalLetter = MedicalLetter::find($id);
        if (!$medicalLetter || $medicalLetter->user_id !== auth()->id()) {
            return response()->json(['message' => 'Volante no encontrado o no autorizado'], 404);
        }
        return response()->json($medicalLetter);
    }

    /**
     * Actualizar una carta médica existente.
     * 
     * Solo se actualizan los campos que son enviados en la solicitud.
     * Si se incluye un nuevo archivo de carta médica, se guarda en el almacenamiento público.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id El ID de la carta médica a actualizar.
     * @return \Illuminate\Http\JsonResponse
     */
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

        // Actualizar solo los campos enviados
        $dataToUpdate = array_filter($validated, function ($value) {
            return !is_null($value); // Excluir valores nulos
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

        // Si no hay datos para actualizar, devolver el volante sin cambios
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
            'volante' => $medicalLetter->fresh(), // Devuelve el modelo actualizado
        ]);
    }

    /**
     * Eliminar una carta médica existente.
     * 
     * @param int $id El ID de la carta médica a eliminar.
     * @return \Illuminate\Http\JsonResponse
     */
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
