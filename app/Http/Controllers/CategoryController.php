<?php

namespace App\Http\Controllers;

use App\Models\FirestoreCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = FirestoreCategory::all();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Form', [
            'category' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(FirestoreCategory::rules());
        $validated['allowsInstant'] = $request->boolean('allowsInstant');
        $validated['isActive']      = $request->boolean('isActive');

        FirestoreCategory::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(string $id)
    {
        $category = FirestoreCategory::find($id);

        if (!$category) {
            return redirect()->route('categories.index')
                ->with('error', 'Categoria não encontrada.');
        }

        return Inertia::render('Categories/Form', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate(FirestoreCategory::rules());
        $validated['allowsInstant'] = $request->boolean('allowsInstant');
        $validated['isActive']      = $request->boolean('isActive');

        FirestoreCategory::update($id, $validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(string $id)
    {
        FirestoreCategory::delete($id);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
