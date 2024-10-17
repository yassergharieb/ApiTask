<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Post extends Model
{
    use HasFactory , SoftDeletes;

    protected   $fillable  =  ['title' , "body" , "cover_image" , "user_id", "is_pinned"];


    public function scopeUserPosts( $builder , $user_id)
    {
         $builder->where('user_id' , $user_id);
    }


    public function tags()
    {
       return $this->belongsToMany(Tag::class , "posts_tags");
    }


    public function setIsPinnedAttribute($value)
    {
        $this->attributes['is_pinned'] = (bool) $value;

    }

    public function scopeIsPinned( Builder $query)
    {
        $query->where('is_pined' , 1);
    }


}
