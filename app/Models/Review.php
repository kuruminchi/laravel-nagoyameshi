<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // 1つのレビューは1人のユーザーに属する（1対多）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 1つのレビューは1つの店舗に属する（1対多）
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
