<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    // 利用規約ページ(indexアクション)
    public function index()
    {
        $term = Term::first();

        return view('admin.terms.index', compact('term'));
    }

    // 利用規約編集ページ(editアクション)
    public function edit(Term $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    // 利用規約更新機能(updateアクション)
    public function update(Request $request, Term $term)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $term->content = $request->input('content');
        $term->update();

        return to_route('admin.terms.index')->with('flash_message', '利用規約を編集しました。');
    }
}
