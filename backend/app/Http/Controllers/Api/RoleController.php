<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

    public function index()
    {
        return response()->json(Role::all());
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Role berhasil ditambahkan', 'data' => $role], 201);
    }


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json(['message' => 'Role berhasil diupdate', 'data' => $role]);
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role berhasil dihapus']);
    }
}
