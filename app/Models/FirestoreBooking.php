<?php

namespace App\Models;

class FirestoreBooking extends FirestoreModel
{
    protected static string $collection = 'bookings';

    public static function getItems(string $bookingId): array
    {
        return static::getSubCollection($bookingId, 'bookingItems');
    }
}
