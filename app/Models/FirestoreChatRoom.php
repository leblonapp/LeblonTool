<?php

namespace App\Models;

class FirestoreChatRoom extends FirestoreModel
{
    protected static string $collection = 'chatRooms';

    public static function getMessages(string $roomId): array
    {
        $docs = static::db()
            ->collection('chatRooms')
            ->document($roomId)
            ->collection('chatMessages')
            ->orderBy('createdAt')
            ->documents();

        $items = [];
        foreach ($docs as $doc) {
            if ($doc->exists()) {
                $items[] = array_merge(['id' => $doc->id()], static::castData($doc->data()));
            }
        }
        return $items;
    }
}
