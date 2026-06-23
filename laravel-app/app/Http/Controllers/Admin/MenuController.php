<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $items = MenuItem::orderBy('location')->orderBy('sort_order')->get()->groupBy('location');

        return view('admin.menu.index', compact('items'));
    }

    public function store(Request $request)
    {
        MenuItem::create($request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'location' => ['required', 'in:header,footer'],
            'sort_order' => ['nullable', 'integer'],
        ]));

        return back()->with('status', 'Menü öğesi eklendi.');
    }

    public function update(Request $request, MenuItem $menu)
    {
        $menu->update($request->validate([
            'label' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'location' => ['required', 'in:header,footer'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]));

        return back()->with('status', 'Menü öğesi güncellendi.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return back()->with('status', 'Menü öğesi silindi.');
    }
}
