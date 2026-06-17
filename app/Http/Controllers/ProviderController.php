<?php

namespace App\Http\Controllers;

use App\Models\FirestoreProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = FirestoreProvider::all();

        return Inertia::render('Providers/Index', [
            'providers' => $providers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Providers/Form', [
            'provider'       => null,
            'services'       => [],
            'weeklySchedule' => [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(FirestoreProvider::rules());
        FirestoreProvider::create($validated);

        return redirect()->route('providers.index')
            ->with('success', 'Fornecedor criado com sucesso!');
    }

    public function edit(string $id)
    {
        $provider       = FirestoreProvider::find($id);
        $services       = FirestoreProvider::getServices($id);
        $weeklySchedule = FirestoreProvider::getWeeklySchedule($id);

        if (!$provider) {
            return redirect()->route('providers.index')
                ->with('error', 'Fornecedor não encontrado.');
        }

        return Inertia::render('Providers/Form', [
            'provider'       => $provider,
            'services'       => $services,
            'weeklySchedule' => $weeklySchedule,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate(FirestoreProvider::rules());
        FirestoreProvider::update($id, $validated);

        return redirect()->route('providers.edit', $id)
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
        FirestoreProvider::delete($id);

        return redirect()->route('providers.index')
            ->with('success', 'Fornecedor removido com sucesso!');
    }

    // Services
    public function storeService(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'durationMin' => 'required|integer|min:5',
            'priceType'   => 'required|numeric|min:0',
        ]);

        FirestoreProvider::saveService($id, $validated);

        return back()->with('success', 'Serviço adicionado com sucesso!');
    }

    public function updateService(Request $request, string $id, string $serviceId)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'durationMin' => 'required|integer|min:5',
            'priceType'   => 'required|numeric|min:0',
        ]);

        FirestoreProvider::saveService($id, $validated, $serviceId);

        return back()->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroyService(string $id, string $serviceId)
    {
        FirestoreProvider::deleteService($id, $serviceId);

        return back()->with('success', 'Serviço removido com sucesso!');
    }

    // Weekly Schedule
    public function updateSchedule(Request $request, string $id)
    {
        $request->validate([
            'days'                  => 'required|array',
            'days.*.dayId'          => 'required|string',
            'days.*.dayName'        => 'required|string',
            'days.*.isActive'       => 'boolean',
            'days.*.startTime'      => 'nullable|string',
            'days.*.endTime'        => 'nullable|string',
            'days.*.hasLunchBreak'  => 'boolean',
            'days.*.lunchStart'     => 'nullable|string',
            'days.*.lunchEnd'       => 'nullable|string',
            'days.*.order'          => 'integer',
        ]);

        FirestoreProvider::saveWeeklySchedule($id, $request->days);

        return back()->with('success', 'Horários atualizados com sucesso!');
    }
}
