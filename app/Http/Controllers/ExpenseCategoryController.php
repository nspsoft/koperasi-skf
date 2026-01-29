<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->latest()->get();
        return view('expenses.categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($request->all());

        return redirect()->back()->with('success', 'Kategori biaya berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', 'Kategori biaya berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $category)
    {
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan dalam transaksi biaya.');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }
}
