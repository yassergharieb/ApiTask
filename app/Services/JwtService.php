<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;

class JwtService
{
    public function createToken(User $user): string
    {
        $now = Carbon::now();
        $payload = [
            'iss' => config('jwt.issuer'),
            'sub' => $user->id,
            'iat' => $now->timestamp,
            'exp' => $now->addMinutes(config('jwt.ttl'))->timestamp,
        ];

        return JWT::encode($payload, config('jwt.secret'), 'HS256');
    }

    public function parseToken(string $token): array
    {
        $decoded = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));

        return (array) $decoded;
    }
}
