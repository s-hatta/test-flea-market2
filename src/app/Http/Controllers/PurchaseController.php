<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();
        $address = $this->findAddress( $user, $item );
        return view('items/item_purchase', compact('user','item','address'));
    }

    public function execute(PurchaseRequest $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();

        /* 商品の在庫を0にする */
        $item->stock = 0;
        $item->save();

        /* 商品に紐づいている住所を複製して登録 */
        $userItem = $item->users()->where('user_id', $user->id)->first();
        $address = Address::find($userItem->address_id)->replicate();
        $address->save();

        /* 注文情報登録 */
        $address = $this->findAddress($user,$item);

        /* 注文情報作成 */
        $order = new Order();
        $order->user_id = $user->id;
        $order->item_id = $item->id;
        $order->price = $item->price;
        $order->address_id = $address->id;
        $order->save();

        /* 取引データ作成 */
        $transaction = new Transaction();
        $transaction->item_id = $item->id;
        $transaction->seller_id = $item->owner_id;
        $transaction->buyer_id = $user->id;
        $transaction->status = Transaction::STATUS_IN_PROGRESS;
        $transaction->save();

        return redirect('/');
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id',$request['id'])->first();
        $address = $this->findAddress( $user, $item );
        return view('items.address_edit', compact('item','address'));
    }

    public function update(AddressRequest $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();
        $address = $this->findAddress( $user, $item );
        $address->postal_code = $request->postal_code;
        $address->address = $request->address;
        $address->building = $request->building;
        $address->update();
        return view('items/item_purchase', compact('user','item','address'));
    }

    private function findAddress( $user, $item )
    {
        $userItem = $item->users()->where('user_id', $user->id)->first();
        if ($userItem && $userItem->address_id)
        {
            return Address::find($userItem->address_id);
        }

        $existingAddress = $user->address;
        $address = Address::create([
            'postal_code' => $existingAddress->postal_code,
            'address' => $existingAddress->address,
            'building' => $existingAddress->building,
        ]);

        if ($userItem)
        {
            $userItem->pivot->address_id = $address->id;
            $userItem->pivot->save();
        }
        else
        {
            $item->users()->attach($user->id, ['address_id' => $address->id]);
        }
        return $address;
    }
}
