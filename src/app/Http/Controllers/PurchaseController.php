<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id',$request['id'])->first();
        return view('items/item_purchase', compact('user','item'));
    }
    
    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('items/address_edit', compact('user'));
    }
}
