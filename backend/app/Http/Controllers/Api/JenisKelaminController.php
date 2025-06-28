<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisKelamin;
use Illuminate\Http\Request;

class JenisKelaminController extends Controller
{
    public function index()
    {
        return response()->json(JenisKelamin::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_kelamin' => 'required|string',
        ]);

        $jenisKelamin = JenisKelamin::create($validated);

        return response()->json($jenisKelamin, 201);
    }

    public function show($id)
    {
        $jenisKelamin = JenisKelamin::findOrFail($id);
        return response()->json($jenisKelamin);
    }

    public function update(Request $request, $id)
    {
        $jenisKelamin = JenisKelamin::findOrFail($id);

        $validated = $request->validate([
            'jenis_kelamin' => 'required|string',
        ]);

        $jenisKelamin->update($validated);

        return response()->json($jenisKelamin);
    }

    public function destroy($id)
    {
        $jenisKelamin = JenisKelamin::findOrFail($id);
        $jenisKelamin->delete();

        return response()->json(null, 204);
    }
}
