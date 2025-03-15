<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $users = User::where('type', 'user')->get();
    //     return response()->json($users);
    // }

    // public function index()
    // {
    //     $users = User::all();
    //     return response()->json($users);
    // }

    public function index(Request $request)
    {
        $type = $request->query('type', 'user'); // Default to 'user' if no type is provided
        $users = User::where('type', $type)->get();
        return response()->json($users);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'mobile_number' =>  ['required', 'string', 'unique:users,mobile_number', 'regex:/^01\d{9}$/',],
            'password' => 'required|string|min:6',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
            'type' => 'required|string|in:user,admin,delivery',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'mobile_number' => $request->mobile_number,
            'password' => bcrypt($request->password),
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius ?? null,
            "is_verified" => 1,
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255',
            'mobile_number' => ['sometimes', 'string', 'unique:users,mobile_number,' . $id, 'regex:/^01\d{9}$/'],
            'password' => 'sometimes|string|min:6',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'radius' => 'sometimes|nullable|numeric|min:1|max:50',
            'image' => 'sometimes|nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['username', 'password', 'mobile_number', 'latitude', 'longitude', 'radius', 'type']));
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
