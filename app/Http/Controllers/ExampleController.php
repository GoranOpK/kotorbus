<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0"
 * )
 */
class ExampleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/examples",
     *     summary="Get examples",
     *     @OA\Response(
     *         response=200,
     *         description="List of examples"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(['message' => 'Examples list']);
    }
}
