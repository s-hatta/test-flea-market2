<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Order;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $exhibitedItems = Item::where('owner_id', '=', Auth::id())->get();
        $purchasedItems = Order::where('payment_status','paid')->where('user_id', Auth::id())->with('item')->get()->pluck('item');
        return view('mypage/profile', compact('user', 'exhibitedItems', 'purchasedItems'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        
         /* ユーザーに住所情報がない場合は追加する */
        if( is_null($user->address_id) ) {
            $address = Address::create([
                'postal_code' => '',
                'address' => '',
                'building' => '',
            ]);
            $user->address_id = $address->id;
            $user->save();
            }
        return view('mypage/profile_edit', compact('user'));
    }
    
    public function update(ProfileRequest $request)
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
