<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
use App\Models\RestaurantTable;
use App\Services\TableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TableController extends Controller
{
    public function __construct(private TableService $service) {}

    public function index(): View
    {
        return view('admin.tables.index', ['tables' => RestaurantTable::withCount('reservations')->orderBy('code')->paginate(10)]);
    }

    public function create(): View
    {
        return view('admin.tables.form', ['table' => new RestaurantTable]);
    }

    public function store(TableRequest $request): RedirectResponse
    {
        RestaurantTable::create($request->validated());

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(RestaurantTable $table): View
    {
        return view('admin.tables.form', compact('table'));
    }

    public function update(TableRequest $request, RestaurantTable $table): RedirectResponse
    {
        $this->service->update($table, $request->validated());

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(RestaurantTable $table): RedirectResponse
    {
        $this->service->delete($table);

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}
