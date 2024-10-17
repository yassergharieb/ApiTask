<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RigsteredUserResource;
use App\Jobs\CallExternalApiJob;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
       $data              =  $request->validated();
       $data['password']  =  Hash::make($data['password']);
       $user              =  User::create($data);
       $token             =  $user->createToken('auth_token')->plainTextToken;
       $response['user']  =  $user;
       $response['token'] =  $token;
       return $this->sucsessResponse($response);
     }
}
