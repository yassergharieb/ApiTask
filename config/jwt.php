<?php

return [
    'secret' => env('JWT_SECRET', 'change-me'),
    'ttl' => env('JWT_TTL', 60),
    'issuer' => env('APP_URL', 'http://localhost'),
];
