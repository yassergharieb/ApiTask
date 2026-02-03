<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{

    public function login(LoginRequest $request, JwtService $jwtService)
    {
        $user =  User::where('phone_number' ,  $request->phone_number)->first();
        if (!$user || !Hash::check($request->password , $user->password)) {
          return  $this->errorResponse(['msg' =>  'your credentials is not correct , please try again with correct credentials']);
        } else {
             $token = $jwtService->createToken($user);
             return  $this->sucsessResponse(['user' => $user , 'token' => $token]);
        }
    }


    public function destroy()
    {
        $user =  User::where("id" , auth()->user()->id)->first();
        $user->delete();
        return  $this->sucsessResponse(["msg" => "User Has been deleted successfully"]);

    }
}
