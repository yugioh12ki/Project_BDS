<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseServices;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Exception\FirebaseException;

class ChatbotController extends Controller
{
    protected $firebase;
    protected $table = 'chats';

    public function __construct(FirebaseServices $firebase)
    {
        $this->firebase = $firebase->getDatabase();
    }

    // Gửi tin nhắn vào Firebase
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => 'required',
            'sender_id' => 'required',
            'content' => 'required',
        ]);

        $ref = "chats/{$data['conversation_id']}/messages";
        $newMsg = [
            'sender_id' => $data['sender_id'],
            'content' => $data['content'],
            'timestamp' => now()->timestamp,
        ];

        $this->firebase->getReference($ref)->push($newMsg);

        return response()->json(['success' => true]);
    }

    public function getMessages()
    {
        $messages = $this->firebase->getReference($this->table)->getValue();
        return response()->json($messages);
    }
}
