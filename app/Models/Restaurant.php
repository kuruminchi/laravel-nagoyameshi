<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

    // 店舗とカテゴリの関係（多対多）
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    // 店舗と定休日の関係（多対多）
    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    // 1つの店舗に対してレビューは複数ある（1対多）
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // 1つの店舗に対して予約は複数ある（1対多）
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }


    // 店舗の平均評価順(独自の並べ替え)で並べ替えするメゾットを定義【$query=クエリの実行対象 $direction=並べ方】
    public function ratingSortable($query, $direction)
    {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    // 店舗の予約が多い順(独自の並べ替え)で並べ替えするメゾットを定義
    public function popularSortable($query, $direction)
    {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
