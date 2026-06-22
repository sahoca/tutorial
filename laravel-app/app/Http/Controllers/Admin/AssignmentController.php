<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::orderBy('sort_order')->latest('assignment_date')->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        return view('admin.assignments.form', ['assignment' => new Assignment()]);
    }

    public function store(Request $request)
    {
        Assignment::create($this->validated($request));

        return redirect()->route('admin.assignments.index')->with('status', 'Görevlendirme eklendi.');
    }

    public function edit(Assignment $assignment)
    {
        return view('admin.assignments.form', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $assignment->update($this->validated($request, $assignment));

        return redirect()->route('admin.assignments.index')->with('status', 'Görevlendirme güncellendi.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return back()->with('status', 'Görevlendirme silindi.');
    }

    private function validated(Request $request, ?Assignment $a = null): array
    {
        $unique = $a ? ',' . $a->id : '';

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:assignments,slug' . $unique],
            'authority' => ['required', 'string', 'max:50'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'assignment_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'document_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,completed,pending'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
