<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:admins|max:255',
            'password' => 'required|string|min:6',
        ]);

        // NEMA Hash::make! Mutator u modelu će odraditi hashovanje.
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
            'username' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:admins,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
        ]);

        // NEMA Hash::make! Mutator u modelu će odraditi hashovanje.
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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Pronađi admina po email-u
        $admin = Admin::where('email', $request->email)->first();

        \Log::info('LOGIN EMAIL: ' . $request->email);
        \Log::info('LOGIN PASS: ' . $request->password);
        \Log::info('HASH IZ BAZE: [' . $admin->password . ']');
        \Log::info('HASH LENGTH: ' . strlen($admin->password));

        // Provjeri lozinku
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Kreiraj i vrati token
        $token = $admin->createToken('admin-panel')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin,
            'message' => 'Login successful',
        ], 200);
    }

    /**
     * Logout admina (briše token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * Test za slanje izvještaja
     */
    public function testDnevniFinansijski()
    {
        // Testni odgovor, ili prava logika izvještaja
        return response()->json(['status' => 'ok', 'message' => 'Test dnevni finansijski izvještaj']);
    }
}