<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $exhibitedItems = Item::where('user_id', $user->id)->get();
        $purchasedItems = User::whereHas('orders', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        return view('mypage/profile', compact('user', 'exhibitedItems', 'purchasedItems'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        return view('mypage/profile_edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();
        return redirect('mypage');
    }
}
