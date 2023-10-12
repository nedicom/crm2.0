<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\File;
use App\Scopes\UserActiveScope;

class LawyersController extends Controller
{
    public function Alllawyers()
    {
        return view ('lawyers', [
            'data' => User::withoutGlobalScope(UserActiveScope::class)->get()
        ]);
    }

    public function addavatar(Request $req)
    {
        $id = Auth::id();
        $user = User::find($id);
        $file = $req->file('avatar');
        $name = $file->hashName();
        $file->move(public_path('/avatars'), $name);
        $user->avatar = '/avatars/'.$name;
        $user->save();

        return redirect() -> route('home') -> with('success', 'Все в порядке, теперь у Вас сногсшибательный аватар');
    }
}
