<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Item::query()
            ->where('user_id', $request->user()->id)
            ->with(['tags', 'category']);

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by type
        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        // Filter by tag
        if ($tagSlug = $request->input('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        // Filter by category
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Filter pinned only
        if ($request->boolean('pinned')) {
            $query->pinned();
        }

        // Order: pinned first, then latest
        $query->orderByDesc('is_pinned')->latest();

        $items = $query->paginate(24)->withQueryString();

        return Inertia::render('Dashboard', [
            'items' => $items,
            'filters' => [
                'search' => $request->input('search', ''),
                'type' => $request->input('type', ''),
                'tag' => $request->input('tag', ''),
                'category' => $request->input('category', ''),
                'pinned' => $request->boolean('pinned'),
            ],
            'tags' => Tag::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'types' => ['link', 'image', 'video', 'screenshot', 'clip'],
        ]);
    }
}
