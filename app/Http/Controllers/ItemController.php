<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Tag;
use App\Services\LinkPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:link,image,video,screenshot,clip',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'url' => 'nullable|url|max:2048',
            'file' => 'nullable|file|max:102400', // 100MB max
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = [
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'is_pinned' => false,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = 'uploads/' . $validated['type'] . 's';
            $data['file_path'] = $file->store($folder, 'public');
        }

        // Fetch link preview metadata
        if ($validated['type'] === 'link' && !empty($validated['url'])) {
            $preview = app(LinkPreviewService::class)->getPreview($validated['url']);
            $data['metadata'] = $preview;

            // Use OG title if user didn't provide a custom one
            if ($data['title'] === $validated['url'] && !empty($preview['title'])) {
                $data['title'] = $preview['title'];
            }
        }

        $item = Item::create($data);

        // Sync tags
        if (!empty($validated['tags'])) {
            $item->tags()->sync($validated['tags']);
        }

        return redirect()->route('dashboard')->with('flash', [
            'type' => 'success',
            'message' => 'Item saved to your Keepsake!',
        ]);
    }

    public function show(Item $item)
    {
        // Authorize
        if ($item->user_id !== auth()->id()) {
            abort(403);
        }

        $item->load(['tags', 'category']);

        return Inertia::render('Items/Show', [
            'item' => $item,
            'allTags' => Tag::orderBy('name')->get(),
            'allCategories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'url' => 'nullable|url|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $item->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? $item->url,
            'category_id' => $validated['category_id'] ?? null,
        ]);

        if (isset($validated['tags'])) {
            $item->tags()->sync($validated['tags']);
        }

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => 'Item updated!',
        ]);
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete files from storage
        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }
        if ($item->thumbnail_path) {
            Storage::disk('public')->delete($item->thumbnail_path);
        }

        $item->delete();

        return redirect()->route('dashboard')->with('flash', [
            'type' => 'success',
            'message' => 'Item deleted.',
        ]);
    }

    public function togglePin(Item $item)
    {
        if ($item->user_id !== auth()->id()) {
            abort(403);
        }

        $item->update(['is_pinned' => !$item->is_pinned]);

        return redirect()->back()->with('flash', [
            'type' => 'success',
            'message' => $item->is_pinned ? 'Item pinned!' : 'Item unpinned.',
        ]);
    }
}
