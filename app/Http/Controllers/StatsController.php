<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function getInfo()
    {
        $data['all_users']  =  Cache::get("users_count");
        $data['all_posts']  =  Cache::get("posts_count");
        $data['users_with_no_posts_count'] =  Cache::get('users_with_no_posts_count');
        return $this->sucsessResponse($data);
    }
}
