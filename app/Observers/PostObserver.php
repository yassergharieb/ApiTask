<?php

namespace App\Observers;

use App\Http\Services\CacheService;
use App\Models\Post;

class PostObserver
{

    public function __construct(
        public CacheService $cacheService ,

    )

    {

    }

    public function created(Post $post): void
    {
        $chacheKeysAndModels = [
            'posts_count' =>"Post" ,
            "users_count" => "User" ,

        ];
        $cacheService =  new CacheService();
        $cacheService->cacheStats($chacheKeysAndModels , 60);
        $cacheService->cacheWithRelations([ "users_with_no_posts_count" => "User"]  , 60 , "whereDoesntHave:posts") ;
    }


    public function updated(Post $post): void
    {

    }


    public function deleted(Post $post): void
    {
        $this->chacheKeysAndModels = [
            'posts_count' =>"Post"
        ];
        $cacheService =  new CacheService(60);
        $cacheService->cacheStats($this->chacheKeysAndModels);
    }


    public function restored(Post $post): void
    {

    }

    public function forceDeleted(Post $post): void
    {

    }
}
