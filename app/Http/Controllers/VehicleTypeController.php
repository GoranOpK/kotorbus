<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function __construct()
    {
        // Samo pravi admin može raditi izmjene (store, update, destroy)
        $this->middleware(['auth:sanctum', 'admin'])->only(['store', 'update', 'destroy']);
        // Sve ostalo (index, show) je javno dostupno (nema middleware-a)
    }

    /**
     * Prikazuje sve tipove vozila.
     */
    public function index()
    {
        return response()->json(VehicleType::all());
    }

    /**
     * Prikazuje pojedinačni tip vozila na osnovu ID-a.
     */
    public function show($id)
    {
        return response()->json(VehicleType::findOrFail($id));
    }

    /**
     * Kreira novi tip vozila.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description_vehicle' => 'required|string',
            'price' => 'required|numeric',
        ]);
        $vehicleType = VehicleType::create($validated);
        return response()->json($vehicleType, 201);
    }

    /**
     * Ažurira postojeći tip vozila.
     */
    public function update(Request $request, $id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $validated = $request->validate([
            'description_vehicle' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
        ]);
        $vehicleType->update($validated);
        return response()->json($vehicleType);
    }

    /**
     * Briše tip vozila.
     */
    public function destroy($id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $vehicleType->delete();
        return response()->json(['message' => 'Deleted']);
    }
}