<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\MenuCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', ['categories' => MenuCategory::withCount('menuItems')->orderBy('id')->paginate(10)]);
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new MenuCategory]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        MenuCategory::create($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(MenuCategory $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(CategoryRequest $request, MenuCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(MenuCategory $category): RedirectResponse
    {
        try {
            DB::transaction(function () use ($category) {
                $locked = MenuCategory::whereKey($category->id)->lockForUpdate()->firstOrFail();
                if ($locked->menuItems()->exists()) {
                    throw ValidationException::withMessages(['name' => 'Kategori masih memiliki menu. Pindahkan atau hapus menu dahulu.']);
                }
                $locked->delete();
            });
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }
            throw ValidationException::withMessages(['name' => 'Kategori sedang dipakai menu dan tidak dapat dihapus.']);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
