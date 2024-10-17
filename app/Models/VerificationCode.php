<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable =  [ 'code' , 'user_id'  , "code_expiration_date" ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
