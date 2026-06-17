<?php

namespace App\Http\Controllers;

use App\Models\FirestoreBooking;
use App\Models\FirestorePayment;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = FirestoreBooking::all();

        usort($bookings, fn($a, $b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function show(string $id)
    {
        $booking = FirestoreBooking::find($id);

        if (!$booking) {
            return redirect()->route('bookings.index')
                ->with('error', 'Agendamento não encontrado.');
        }

        $items = FirestoreBooking::getItems($id);
        $payments = $this->findPaymentsForBooking($id);
        $payment = $this->resolvePrimaryPayment($booking, $payments);

        return Inertia::render('Bookings/Show', [
            'booking' => $booking,
            'items' => $items,
            'payment' => $payment,
            'payments' => $payments,
            'paymentSummary' => [
                'hasPayment' => $payment !== null,
                'count' => count($payments),
                'status' => $payment['status'] ?? $booking['paymentStatus'] ?? null,
                'amount' => $payment['amount'] ?? $booking['totalPrice'] ?? null,
            ],
        ]);
    }

    private function resolvePrimaryPayment(array $booking, array &$payments): ?array
    {
        if (!empty($booking['paymentId'])) {
            $payment = FirestorePayment::find($booking['paymentId']);

            if ($payment) {
                if (!in_array($payment['id'], array_column($payments, 'id'), true)) {
                    $payments[] = $payment;
                }

                return $payment;
            }
        }

        return $payments[0] ?? null;
    }

    private function findPaymentsForBooking(string $bookingId): array
    {
        $payments = array_values(array_filter(
            FirestorePayment::all(),
            fn($payment) => ($payment['bookingId'] ?? null) === $bookingId
        ));

        usort($payments, fn($a, $b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

        return $payments;
    }
}
