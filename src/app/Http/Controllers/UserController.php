<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $exhibitedItems = Item::where('owner_id', '=', Auth::id())->get();
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
        
        if ($user->address_id)
        {
            $address = $user->address;
            $address->update([
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        } else {
            $address = Address::create([
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]);
            $user->address_id = $address->id;
        }
        
        if ($request->hasFile('profile_image'))
        {
            $file = $request->file('profile_image');
            $path = Storage::disk('public')->putFile('images/users', $file);
            $user->img_url = basename($path);
        }
        $user->save();
        return redirect('mypage');
    }
}
