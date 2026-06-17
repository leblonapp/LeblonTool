<?php

namespace App\Models;

class FirestoreCategory extends FirestoreModel
{
    protected static string $collection = 'categories';

    public static function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'icon'               => 'required|string|max:100',
            'defaultBookingType' => 'required|in:flexivel,fixo',
            'sortOrder'          => 'required|integer|min:0',
            'allowsInstant'      => 'boolean',
            'isActive'           => 'boolean',
        ];
    }
}
