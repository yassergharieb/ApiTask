<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected array $keysIDontToForget = [];

    public function cacheStats(array $keys, int $cacheDurationMinutes, array $modelsToGetWithTrashed = [])
    {
        foreach ($keys as $key => $model) {
            Cache::remember($key, $cacheDurationMinutes, function () use ($model, $modelsToGetWithTrashed) {
                $modelClass = "App\\Models\\" . $model;

                if (!class_exists($modelClass)) {
                    throw new \Exception("Model $modelClass does not exist.");
                }

                if (!empty($modelsToGetWithTrashed) && in_array($model, $modelsToGetWithTrashed)) {
                    return $modelClass::withTrashed()->count();
                }

                return $modelClass::count();
            });
        }
    }

    public function cacheWithRelations(array $keys, int $cacheDurationMinutes, string $methodName)
    {
        foreach ($keys as $key => $model) {
            Cache::forget($key);
            Cache::remember($key, $cacheDurationMinutes, function () use ($model, $methodName) {
                $modelClass = "App\\Models\\" . $model;

                if (!class_exists($modelClass)) {
                    throw new \Exception("Model $modelClass does not exist.");
                }

                if (strpos($methodName, ':') !== false) {
                    $extractParams = explode(':', $methodName);
                    $method = $extractParams[0];
                    $param = $extractParams[1];

                    return $modelClass::$method($param)->count();
                }

                return $modelClass::$methodName()->count();
            });
        }
    }

    public function forgetCacheKeysCore(array $keys)
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return $this;
    }
}
