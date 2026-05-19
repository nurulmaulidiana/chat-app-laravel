<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($id = null)
    {
        $users = User::where('id', '!=', Auth::id())->get();


        $isGroup = request()->segment(2) === 'group';

        $groups = Conversation::where('type', 'group')
            ->whereHas('users', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->get();

        $messages = collect();

        if ($id && !$isGroup) {
            // PRIVATE CHAT
            $messages = Message::where(function ($q) use ($id) {
                $q->where('user_id', Auth::id())
                  ->where('receiver_id', $id);
            })->orWhere(function ($q) use ($id) {
                $q->where('user_id', $id)
                  ->where('receiver_id', Auth::id());
            })->with('user')->orderBy('created_at')->get();
        }

        if ($id && $isGroup) {
            // GROUP CHAT
            $messages = Message::where('conversation_id', $id)
                ->with('user')
                ->orderBy('created_at')
                ->get();
        }

        return view('chat', compact('users', 'messages', 'id', 'groups', 'isGroup'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if ($request->conversation_id) {
            // GROUP CHAT
            $message = Message::create([
                'user_id'         => Auth::id(),
                'conversation_id' => $request->conversation_id,
                'message'         => $request->message,
            ]);
        } else {
            // PRIVATE CHAT
            $message = Message::create([
                'user_id'     => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'message'     => $request->message,
            ]);
        }

        broadcast(new MessageSent($message))->toOthers();

        return back();
    }
}