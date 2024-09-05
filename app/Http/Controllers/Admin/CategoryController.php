<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // カテゴリ一覧ページ(indexアクション)
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $categories->total();
        } else {
            $categories = Category::paginate(15);
            $total = 0;
        }
        
        return view('admin.categories.index', compact('categories', 'total', 'keyword'));
    }

    // カテゴリ登録機能(storeアクション)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = new Category();
        $category->name = $request->input('name');
        $category->save();

        return to_route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }

    // カテゴリ更新機能(updateアクション)
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category->name = $request->input('name');
        $category->update();

        return to_route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    }

    // カテゴリ削除機能(destroyアクション)
    public function destroy(Category $category)
    {
        $category->delete();

        return to_route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
