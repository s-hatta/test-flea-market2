<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Rating;
use App\Http\Requests\ProfileRequest;
use DateTime;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $items = null;
        $transactions = null;
        $tab = $request->query('tab');
        switch($tab) {
            case 'sell':
            default:
                $items = Item::where('owner_id', '=', Auth::id())->get();
                break;

            case 'buy':
                $items = Order::where('user_id', Auth::id())->with('item')->get()->pluck('item');
                break;

            case 'transaction':
                /* 取引中、または評価が完了していない取引 */
                $transactions = Transaction::where(function($query) use ($user) {
                    $query->where('seller_id', $user->id)
                        ->orWhere('buyer_id', $user->id);
                })->where(function($query) use ($user) {
                    $query->where('status', Transaction::STATUS_IN_PROGRESS)
                        ->orWhere(function($query) use ($user) {
                            $query->where('status', Transaction::STATUS_COMPLETED)
                                ->whereDoesntHave('ratings', function($rating) use ($user) {
                                    $rating->where('rater_id', $user->id);
                                });
                        });
                })->orderBy('updated_at', 'desc')->get();
                return view('mypage/profile', compact('transactions'));
        }
        return view('mypage/profile', compact('items'));
    }

    public function verify($request)
    {
        $id = $request;
        $user = User::where('id',$id)->first();
        if( is_null($user) )
            {
            return view('auth.login');
            }
        $user->email_verified_at = new DateTime();
        $user->save();
        return view('auth.login')->with('message','送られたメール本文内のURLをクリックして登録を完了してください');
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
