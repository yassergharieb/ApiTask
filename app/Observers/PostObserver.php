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
      $this->observeForCache($chacheKeysAndModels);
    }


    public function updated(Post $post): void
    {

    }


    public function deleted(Post $post): void
    {

    }



    private function observeForCache(array $chacheKeysAndModels = null) {

        if(!$chacheKeysAndModels) {
            $chacheKeysAndModels = [
                'posts_count' =>"Post" ,
                "users_count" => "User" ,
                "users_with_no_posts_count" => "User"
            ];
        }

        $this->cacheService->cacheStats($chacheKeysAndModels , 60);
        $this->cacheService->cacheWithRelations([ "users_with_no_posts_count" => "User"]  , 60 , "whereDoesntHave:posts");

    }
}
