<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        $user =  User::where('phone_number' ,  $request->phone_number)->first();
        $user->makeVisible('password');
        if (!$user || !Hash::check($request->password , $user->password)) {
          return  $this->errorResponse(['msg' =>  'your credentials is not correct , please try again with correct credentials']);
        } else {
             $user->makeHidden('password');
             $token = $user->createToken('auth_token')->plainTextToken;
             return  $this->sucsessResponse(['user' => $user , 'token' => $token]);
        }
    }


    public function destroy()
    {
        $user =  User::where("id" , auth()->user()->id)->first();
        $user->tokens()->delete();
        $user->delete();
        return  $this->sucsessResponse(["msg" => "User Has been deleted successfully"]);

    }
}
