<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
    // トップページ(indexアクション)
    public function index()
    {
        // メモ：リレーション先のカラムの平均を算出したいときはwithAvgを使う。orderByの第一引数はリレーション先のテーブル名_avg_カラム名を指定する。
        $highly_rated_restaurants = Restaurant::withAvg('reviews', 'score')->orderBy('reviews_avg_score', 'desc')->take(6)->get();
        
        $categories = Category::all();

        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();

        return view('home', compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
    }
}
