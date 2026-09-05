<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $items = MenuItem::with('category')->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))->orderBy('id')->limit(3)->get();

        return view('public.home', compact('items'));
    }

    public function menu(Request $request): View
    {
        $filter = $request->validate(['category' => ['nullable', 'integer', 'exists:menu_categories,id']]);
        $categories = MenuCategory::where('is_active', true)
            ->whereHas('menuItems', fn ($query) => $query->where('is_active', true))->orderBy('id')->get();
        $items = MenuItem::with('category')->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->where('is_active', true))
            ->when($filter['category'] ?? null, fn ($query, $id) => $query->where('menu_category_id', $id))
            ->orderBy('id')->get();

        return view('public.menu', compact('items', 'categories'));
    }
}
