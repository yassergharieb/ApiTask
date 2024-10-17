<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{


    public function cacheStats(array $keys , $CacheDurationMinutes )
    {
        foreach ($keys as $key => $model) {
            Cache::forget($key);
            Cache::remember($key , $CacheDurationMinutes , function () use ($model) {
                $model =   "App\\Models\\" .$model;
                return  $model::all()->count();
            } );
        }
    }

    public function cacheWithRelations(array $keys , $CacheDurationMinutes , $methodName )
    {
        foreach ($keys as $key => $model) {
            Cache::forget($key);
            Cache::remember($key , $CacheDurationMinutes , function () use ($model , $methodName) {
                $extractParams =  explode(':' , $methodName);
                $methodName =  $extractParams[0];
                $model =   "App\\Models\\" .$model;
                return  $model::$methodName($extractParams[1])->count();
            } );
        }
    }
}
