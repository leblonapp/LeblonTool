<?php

namespace App\Http\Controllers;

use App\Models\FirestoreUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = FirestoreUser::all();

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Form', [
            'user' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(FirestoreUser::rules());

        FirestoreUser::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(string $id)
    {
        $user = FirestoreUser::find($id);

        if (!$user) {
            return redirect()->route('users.index')
                ->with('error', 'Usuário não encontrado.');
        }

        return Inertia::render('Users/Form', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate(FirestoreUser::rules());

        FirestoreUser::update($id, $validated);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        FirestoreUser::delete($id);

        return redirect()->route('users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }
}
