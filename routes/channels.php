<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

//Private chat//
Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Group //
Broadcast::channel('group.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);
    if (!$conversation) return false;
    return $conversation->users->contains($user->id);
});
Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];

});