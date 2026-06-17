<?php

namespace App\Http\Controllers;

use App\Models\FirestoreChatRoom;
use Inertia\Inertia;

class ChatRoomController extends Controller
{
    public function index()
    {
        $rooms = FirestoreChatRoom::all();

        // Ordena por updatedAt desc (já vem como string d/m/Y H:i)
        usort($rooms, fn($a, $b) => strcmp($b['updatedAt'] ?? '', $a['updatedAt'] ?? ''));

        return Inertia::render('ChatRooms/Index', [
            'rooms' => $rooms,
        ]);
    }

    public function show(string $id)
    {
        $room     = FirestoreChatRoom::find($id);
        $messages = FirestoreChatRoom::getMessages($id);

        if (!$room) {
            return redirect()->route('chat-rooms.index')
                ->with('error', 'Sala não encontrada.');
        }

        return Inertia::render('ChatRooms/Show', [
            'room'     => $room,
            'messages' => $messages,
        ]);
    }
}
