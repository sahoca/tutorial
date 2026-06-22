<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('title')->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page()]);
    }

    public function store(Request $request)
    {
        $page = Page::create($this->validated($request));

        return redirect()->route('admin.pages.index')->with('status', 'Sayfa oluşturuldu.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));

        return redirect()->route('admin.pages.index')->with('status', 'Sayfa güncellendi.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('status', 'Sayfa silindi.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $unique = $page ? ',' . $page->id : '';

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug' . $unique],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
