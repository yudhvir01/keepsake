<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        return Inertia::render('Tags/Index', [
            'tags' => Tag::withCount('items')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'color' => 'nullable|string|max:7',
        ]);

        Tag::create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#6366f1',
        ]);

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Tag created!',
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'nullable|string|max:7',
        ]);

        $tag->update($validated);

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Tag updated!',
        ]);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Tag deleted.',
        ]);
    }
}
