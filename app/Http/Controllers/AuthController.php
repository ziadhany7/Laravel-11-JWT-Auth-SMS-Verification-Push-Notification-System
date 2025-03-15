<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    protected $TwilioService;
    public function __construct(){
        $this->TwilioService = new TwilioService();
    }

    public function register(Request $request)
    {
        $data = $request->only(['username','mobile_number','password','latitude','longitude','radius','image']);
        $validator = Validator::make($data, [
            'username' => 'required|string|max:255',
            'mobile_number' =>  ['required', 'string', 'unique:users,mobile_number', 'regex:/^01\d{9}$/',],
            'password' => 'required|string|min:6',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users/' . $request->file('image')->getClientOriginalName(), 'public');
        }


        // $verificationCode = rand(100000, 999999);

        $verificationCode = $this->TwilioService->sendVerificationCode($request->mobile_number);

        $user = User::create([
            'username' => $request->username,
            'mobile_number' => $request->mobile_number,
            'password' => Hash::make($request->password),
            'location' => $request->location,
            'image' => $imagePath,
            'verification_code' => $verificationCode,
        ]);

        // // إرسال كود التحقق عبر Twilio
        // $this->sendVerificationCode($request->mobile_number, $verificationCode);

        return response()->json([
            'message' => 'User registered successfully. Please verify your mobile number.',
            'code' => $verificationCode,
        ], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->only(['mobile_number', 'password']);
        // dd($credentials);
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        if (!$user->is_verified) {
            return response()->json(['error' => 'Please verify your mobile number first.'], 403);
        }
        return $this->respondWithToken($token);
    }


    public function verifyCode(Request $request)
    {
        $request->validate([
            'mobile_number' =>  ['required', 'string', 'regex:/^01\d{9}$/',],
            'verification_code' => 'required'
        ], [
            'mobile_number' => 'mobile should be 11 digits and starts with 01',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)->first();

        // if (!$user || $user->verification_code != $request->verification_code) {
        //     return response()->json(['error' => 'Invalid verification code.'], 400);
        // }
        if(!$check=$this->TwilioService->sendVerificationCode($user->mobile_number,$user->verificationCode)){
            return response()->json(['error' => 'Invalid verification code twilio.'], 400);
        }

        $user->is_verified = true;
        $user->verification_code = null;
        $user->save();

        return response()->json(['message' => 'Mobile number verified successfully.']);
    }


    // protected function sendVerificationCode($mobile_number, $code)
    // {
    //     $twilioSid = env('TWILIO_SID');
    //     $twilioAuthToken = env('TWILIO_AUTH_TOKEN');
    //     $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

    //     $client = new Client($twilioSid, $twilioAuthToken);
    //     $client->messages->create($mobile_number, [
    //         'from' => $twilioPhoneNumber,
    //         'body' => "Your verification code is: $code"
    //     ]);
    // }

    // استرجاع بيانات المستخدم
    public function profile()
    {
        return response()->json(auth('api')->user());
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }
}
