<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function getNearestDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10;

        // استعلام البحث باستخدام Haversine Formula
        $deliveryAgents = User::select('id', 'username', 'mobile_number', 'latitude', 'longitude')
            ->where('type', 'delivery')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude])
            ->having("distance", "<", $radius)
            ->orderBy("distance", "asc")
            ->get();

        return response()->json([
            'message' => 'Nearest delivery representatives retrieved successfully.',
            'data' => $deliveryAgents
        ]);
    }
}
