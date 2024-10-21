<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
    public function login(Request $request){
        // return 'oke'; //bisa diakses di postman bagian Headers dengan key Accept value application/json
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        // return $user;
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        // return $user->createToken('sanctum')->plainTextToken; //value Bearer
        // unset($user->email_verified_at);
        // unset($user->created_at);
        // unset($user->updated_at);
        // unset($user->deleted_at);
        $user->tokens()->delete(); //hapus dulu token sebelumnya
        $token = $user->createToken('sanctum')->plainTextToken;
        $user->token = $token; //tokennya dimasukin
        $user->makeHidden(['email_verified_at', 'created_at', 'updated_at', 'deleted_at']);
        
        return response(['data'=>$user]);
    }

    function logout(){
        // return 'logout';
        $user = auth()->user();
        $user->tokens()->delete(); //revoking token
        return response(['message' => 'logout success']);
    }

    public function me(){
        return response(['data'=>auth()->user()]);
    }

    


}
