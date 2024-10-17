<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Tag extends Model
{
    use HasFactory , SoftDeletes;

    protected  $fillable =  ['name'];

    public function posts()
    {
        return  $this->belongsToMany(Post::class , "posts_tags");
    }
}
