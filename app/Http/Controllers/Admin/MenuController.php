<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private MenuService $service) {}

    public function index(): View
    {
        return view('admin.menus.index', ['menus' => MenuItem::with('category')->orderBy('id')->paginate(10)]);
    }

    public function create(): View
    {
        return $this->form(new MenuItem);
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $this->service->save($request->validated(), $request->file('photo'));

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menu): View
    {
        return $this->form($menu);
    }

    public function update(MenuRequest $request, MenuItem $menu): RedirectResponse
    {
        $this->service->save($request->validated(), $request->file('photo'), $menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        $this->service->delete($menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    private function form(MenuItem $menu): View
    {
        return view('admin.menus.form', ['menu' => $menu, 'categories' => MenuCategory::orderBy('name')->get()]);
    }
}
