<?php

namespace App\Http\Controllers;

use App\Http\Requests\PilotLoginValidation;
use App\Http\Requests\PilotStoreValidation;
use App\Http\Resources\PilotStoreResource;
use App\Models\pilot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class pilotController extends Controller
{
    // ثبت خلبان جدید + صدور token (عمومی)
    public function store(PilotStoreValidation $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $Pilot = pilot::create($data);
        $token = $Pilot->createToken('pilot-token')->plainTextToken;

        return response()->json([
            'message' => 'Pilot registered successfully',
            'token'   => $token,
            'data'    => new PilotStoreResource($Pilot),
        ], 201);
    }

    // ورود خلبان + صدور token (عمومی)
    public function login(PilotLoginValidation $request)
    {
        $Pilot = pilot::where('license_number', $request->license_number)->first();

        if (! $Pilot || ! Hash::check($request->password, $Pilot->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $Pilot->tokens()->delete();
        $token = $Pilot->createToken('pilot-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'data'    => new PilotStoreResource($Pilot),
        ]);
    }

    // خلبان فعلی (برای تست token در Postman)
    public function me(Request $request)
    {
        return response()->json([
            'message' => 'Authenticated pilot',
            'data'    => new PilotStoreResource($request->user()),
        ]);
    }

    public function index()
    {
        return response()->json([
            'message' => 'Pilots fetched successfully',
            'data'    => PilotStoreResource::collection(pilot::all()),
        ]);
    }

    public function show(pilot $pilot)
    {
        return response()->json([
            'message' => 'Pilot has been fetched',
            'data'    => new PilotStoreResource($pilot),
        ]);
    }

    public function update(PilotStoreValidation $request, pilot $pilot)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $pilot->update($data);

        return response()->json([
            'message' => 'Pilot updated successfully',
            'data'    => new PilotStoreResource($pilot),
        ]);
    }

    public function destroy(pilot $pilot)
    {
        $pilot->delete();

        return response()->json([
            'message' => 'Pilot deleted successfully',
            'data'    => new PilotStoreResource($pilot),
        ]);
    }
}
