<?php

namespace App\Http\Controllers;

use App\Http\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{

    public function __construct(public CacheService $cacheService)
    {

    }
    public function getInfo()
    {
        $data['all_users']  =  Cache::get("users_count")  ??  $this->cacheService->forgetCacheKeysCore(['users_count'])
                                                                   ->cacheStats(['users_count' =>"User"] , 60 );

        $data['all_posts']  =  Cache::get("posts_count")    ??  $this->cacheService->forgetCacheKeysCore(['posts_count'])
                                                                 ->cacheStats(['posts_count' => "Post"] , 60 , ['Post'] );

        $data['users_with_no_posts_count'] =  Cache::get('users_with_no_posts_count') ?? $this->cacheService->forgetCacheKeysCore(['users_with_no_posts_count'])
                                                                 ->cacheWithRelations([ "users_with_no_posts_count" => "User"], 60 , "whereDoesntHave:posts");
        return $this->sucsessResponse($data);
    }
}
