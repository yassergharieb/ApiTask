<?php

namespace App\Observers;

use App\Http\Services\CacheService;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserObserver
{
    public function __construct(
        public CacheService $cacheService ,
     )

    {

    }

    public function created(User $user): void
    {

        $data['code']                 =  rand(100000 , 2000000 );
        $data['user_id']              =  $user->id;
        $data['token']                =  Str::random(32) . now()->toDateString() . $user->id;
        $data['code_expiration_date'] =  Carbon::now()->addHour()->format('Y-m-d H:i:s');
        $verification_code            =   VerificationCode::create($data)->toArray();
        Log::channel( "verification_codes")->info("user verification_code with user_id" . $user->id ,  $verification_code);
        $chacheKeysAndModels = [
            'posts_count' =>"Post" ,
            "users_count" => "User" ,
            "users_with_no_posts_count" => "User"
        ];
        $cacheService =  new CacheService();
        $cacheService->cacheStats($chacheKeysAndModels , 60);
        $cacheService->cacheWithRelations([ "users_with_no_posts_count" => "User"]  , 60 , "whereDoesntHave:posts") ;

    }


    public function updated(User $user): void
    {
        if($user->is_verified) {
            VerificationCode::where('user_id' ,  $user->id )->delete();
        }
    }


    public function deleted(User $user): void
    {

      $this->cacheService->CacheDurationMinutes = 60;
      $this->cacheService->cacheStats(['']);
    }


}
