<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\ResponseHandler;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsVerifiedUser
{
    use ResponseHandler;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       $user  =   isset($request->phone_number) ?   User::where('phone_number' , $request->phone_number)->first() : auth()->user();
        if ( !$user->is_verified) {
            return  $this->errorResponse(['msg' =>  "your account is not verified, please verify it"]);
        }
        return $next($request);
    }
}
