<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;

class GroupController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('group.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'users' => 'required|array'
        ]);

        $conversation = Conversation::create([
            'type' => 'group',
            'name' => $request->name
        ]);

        $conversation->users()->attach($request->users);
        $conversation->users()->attach(auth()->id());

        return redirect()->route('chat')->with('success', 'Group berhasil dibuat');
    }
}