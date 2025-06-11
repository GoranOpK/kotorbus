<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Prikazuje sve admine.
     */
    public function index()
    {
        $admins = Admin::all();
        return response()->json($admins, 200);
    }

    /**
     * Prikazuje pojedinačnog admina na osnovu ID-a.
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return response()->json($admin, 200);
    }

    /**
     * Kreira novog admina.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins|max:255',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $admin = Admin::create($validated);
        return response()->json($admin, 201);
    }

    /**
     * Ažurira postojeće podatke o adminu.
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:admins,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $admin->update($validated);
        return response()->json($admin, 200);
    }

    /**
     * Briše admina.
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return response()->json(['message' => 'Admin deleted successfully'], 200);
    }

    /**
     * Prijava administratora i generisanje tokena.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // NEMA provere role, jer svi su admini iz ove tabele
            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'message' => 'Login successful',
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}