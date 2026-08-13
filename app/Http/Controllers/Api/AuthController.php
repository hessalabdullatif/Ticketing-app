<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //same idea as a Form Request, just inline in the controller (fine for simple files like this; 
    // can extract to a separate Form Request later if it grows)
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);
   
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            //converts it into an irreversible encrypted format — even if someone steals the database, they can't see actual passwords
            'password' => Hash::make($data['password']),
        ]);
         //Sanctum generates a unique random token, links it to this user in a dedicated table (personal_access_tokens), 
         //and returns it to us as plain text we can hand to the user exactly once (it's never retrievable again)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();
         //
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}