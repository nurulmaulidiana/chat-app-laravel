<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('user');
    }

    public function broadcastOn(): array
    {
        // GROUP CHAT
        if ($this->message->conversation_id) {
            return [
                new PrivateChannel('group.' . $this->message->conversation_id)
            ];
        }

        // PRIVATE CHAT
        return [
            new PrivateChannel('chat.' . $this->message->user_id),
            new PrivateChannel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'              => $this->message->id,
                'user_id'         => $this->message->user_id,
                'receiver_id'     => $this->message->receiver_id,
                'conversation_id' => $this->message->conversation_id,
                'message'         => $this->message->message,
                'user_name'       => $this->message->user->name ?? 'User',
            ]
        ];
    }
}