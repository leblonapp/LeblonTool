<?php

namespace App\Models;

class FirestoreProvider extends FirestoreModel
{
    protected static string $collection = 'providers';

    public static function rules(): array
    {
        return [
            'displayName' => 'required|string|max:255',
            'email'       => 'required|email',
            'phone'       => 'nullable|string|max:20',
            'cnpj'        => 'nullable|string|max:18',
            'status'      => 'required|in:active,inactive',
        ];
    }

    public static function getServices(string $providerId): array
    {
        return static::getSubCollection($providerId, 'services');
    }

    public static function saveService(string $providerId, array $data, ?string $serviceId = null): void
    {
        if ($serviceId) {
            static::updateSubDocument($providerId, 'services', $serviceId, $data);
        } else {
            static::createSubDocument($providerId, 'services', $data);
        }
    }

    public static function deleteService(string $providerId, string $serviceId): void
    {
        static::deleteSubDocument($providerId, 'services', $serviceId);
    }

    public static function getWeeklySchedule(string $providerId): array
    {
        return static::getSubCollection($providerId, 'weeklySchedule');
    }

    public static function saveWeeklySchedule(string $providerId, array $days): void
    {
        foreach ($days as $day) {
            static::updateSubDocument($providerId, 'weeklySchedule', $day['dayId'], $day);
        }
    }
}
