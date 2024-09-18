<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // 会社概要ページ(indexアクション)
    public function index()
    {
        $company = Company::first();

        return view('admin.company.index', compact('company'));
    }

    // 会社概要編集ページ(editアクション)
    public function edit(Company $company)
    {
        return view('admin.company.edit', compact('company'));
    }

    // 会社概要更新機能(updateアクション)
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|max:255',
            'postal_code' => 'required|digits:7',
            'address' => 'required|max:255',
            'representative' => 'required|max:255',
            'establishment_date' => 'required|max:255',
            'capital' => 'required|max:255',
            'business' => 'required|max:255',
            'number_of_employees' => 'required|max:255'
        ]);

        $company->name = $request->input('name');
        $company->postal_code = $request->input('postal_code');
        $company->address = $request->input('address');
        $company->representative = $request->input('representative');
        $company->establishment_date = $request->input('establishment_date');
        $company->capital = $request->input('capital');
        $company->business = $request->input('business');
        $company->number_of_employees = $request->input('number_of_employees');
        $company->update();

        return to_route('admin.company.index')->with('flash_message', '会社概要を編集しました。');
    }
}
