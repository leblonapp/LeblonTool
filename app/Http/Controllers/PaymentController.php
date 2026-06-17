<?php

namespace App\Http\Controllers;

use App\Models\FirestorePayment;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = FirestorePayment::all();

        usort($payments, fn($a, $b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
        ]);
    }

    public function show(string $id)
    {
        $payment = FirestorePayment::find($id);

        if (!$payment) {
            return redirect()->route('payments.index')
                ->with('error', 'Pagamento não encontrado.');
        }

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }
}
