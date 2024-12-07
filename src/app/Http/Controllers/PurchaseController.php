<?
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use App\Models\User;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $item = Item::where('id', $request['id'])->first();
        $address = $this->findAddress();
        return view('items/item_purchase', compact('user','item','address'));
    }

    public function edit(Request $request)
    {
        return view('items.address_edit');
    }
    
    private function findAddress()
    {
        $userItem = $item->users()->where('user_id', $user->id)->first();
        if ($userItem && $userItem->pivot->address_id)
        {
            return Address::find($userItem->pivot->address_id);
        }
        
        if( is_null($user->address_id) )
            {
            return null;
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
