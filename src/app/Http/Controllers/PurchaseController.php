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

    public function edit($id)
    {
        $user = Auth::user();

        return view('items.address_edit', compact('user', 'id'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->save();

        return redirect()->route('purchase.index', ['id' => $request->item_id])->with('success', '住所が更新されました');
    }
}
