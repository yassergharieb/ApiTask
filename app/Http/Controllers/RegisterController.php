<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request, JwtService $jwtService)
    {
       $data              =  $request->validated();
       $data['password']  =  Hash::make($data['password']);
       $data['is_verified'] = now();
       $user              =  User::create($data);
       $token             =  $jwtService->createToken($user);
       $response['user']  =  $user;
       $response['token'] =  $token;
       return $this->sucsessResponse($response);
     }
}
