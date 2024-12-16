<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use App\Models\User;
use App\Models\Order;
use App\Http\Requests\AddressRequest;
use App\Services\StripePaymentService;

class PurchaseController extends Controller
{
    protected $stripeService;

    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();
        $address = $this->findAddress( $user, $item );
        return view('items/item_purchase', compact('user','item','address'));
    }
    
    public function execute(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();
        
        /* 商品に紐づいている住所を複製して登録 */
        $address = $this->findAddress($user,$item);

        /* 注文情報作成 */
        $order = new Order();
        $order->user_id = $user->id;
        $order->item_id = $item->id;
        $order->price = $item->price;
        $order->address_id = $address->id;
        $order->payment_status = 'pending';
        $order->save();

        /* Stripe checkout sessionの作成 */
        $session = $this->stripeService->createCheckoutSession(
            $item,
            $request->payment_method,
            route('purchase.success', ['order' => $order->id]),
            route('purchase.cancel', ['order' => $order->id])
        );
        
        /* StripeのセッションIDを記憶して保存 */
        $order->stripe_session_id = $session->id;
        $order->save();
        
        /* Stripeの決済画面へ遷移 */
        return redirect($session->url);
    }

    public function success(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        return redirect('/');
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->delete();
        return redirect()->route('items.show', $order->item_id);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $result = $this->stripeService->handleWebhook($payload, $sigHeader);
        if (!$result) {
            return response()->json(['error' => 'Webhook handling failed'], 400);
        }
        return response()->json(['success' => true]);
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
