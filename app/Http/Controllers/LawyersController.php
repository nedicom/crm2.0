<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Tasks;
use Illuminate\Http\File;
use App\Scopes\UserActiveScope;
use Carbon\Carbon;

class LawyersController extends Controller
{
    public function Alllawyers()
    {
        return view('lawyers', [
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
        $user->avatar = '/avatars/' . $name;
        $user->save();

        return redirect()->route('home')->with('success', 'Все в порядке, теперь у Вас сногсшибательный аватар');
    }

    public function lawyertaskfetch(Request $req)
    {
        $user = Tasks::where('lawyer', $req->id)->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(14)])->orderBy('date')->get()
            ->map(function ($item) {
                $item->day = Carbon::parse($item->day)->format('d');;
                return $item;
            });
        return ($user);
    }
}
