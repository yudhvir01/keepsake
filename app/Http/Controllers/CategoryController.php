<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
            'icon' => 'nullable|string|max:10',
        ]);

        Category::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? '📁',
        ]);

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Category created!',
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:10',
        ]);

        $category->update($validated);

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Category updated!',
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Category deleted.',
        ]);
    }
}
