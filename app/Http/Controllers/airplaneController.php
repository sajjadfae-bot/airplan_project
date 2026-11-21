<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirplaneStoreValidation;
use App\Http\Resources\AirplaneStoreResource;
use App\Models\airplane;

class airplaneController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Airplanes fetched successfully',
            'data'    => AirplaneStoreResource::collection(airplane::all()),
        ]);
    }

    public function store(AirplaneStoreValidation $request)
    {
        $Airplane = airplane::create($request->validated());

        return response()->json([
            'message' => 'Airplane created successfully',
            'data'    => new AirplaneStoreResource($Airplane),
        ], 201);
    }

    public function show(airplane $airplane)
    {
        return response()->json([
            'message' => 'Airplane has been fetched',
            'data'    => new AirplaneStoreResource($airplane),
        ]);
    }

    public function update(AirplaneStoreValidation $request, airplane $airplane)
    {
        $airplane->update($request->validated());

        return response()->json([
            'message' => 'Airplane updated successfully',
            'data'    => new AirplaneStoreResource($airplane),
        ]);
    }

    public function destroy(airplane $airplane)
    {
        $airplane->delete();

        return response()->json([
            'message' => 'Airplane deleted successfully',
            'data'    => new AirplaneStoreResource($airplane),
        ]);
    }
}
