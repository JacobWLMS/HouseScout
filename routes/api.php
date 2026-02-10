<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/properties/{property}', function (\App\Models\Property $property) {
        return $property->load(['epcData', 'floodRiskData', 'crimeData', 'planningApplications', 'landRegistryData']);
    });

    Route::get('/saved-properties', function (Request $request) {
        return $request->user()->savedProperties()->with('property')->get();
    });
});
