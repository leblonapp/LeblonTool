<?php

namespace App\Models;

class FirestoreUser extends FirestoreModel
{
    protected static string $collection = 'users';

    public static function rules(): array
    {
        return [
            'display_name'  => 'required|string|max:255',
            'email' => 'required|email',
            'role'  => 'required|string',
        ];
    }
}
