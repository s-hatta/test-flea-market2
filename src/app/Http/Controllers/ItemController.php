<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $find = $request->session()->get('find');
        if($request->method() == "POST") {
            $itemName = $request['item_name'];
        } else {
            $itemName = isset($find['item_name'])? $find['item_name']:null;
        }
        $isMylist = $request->query('tab') === 'mylist';
        $items = $this->findItems( $isMylist, $itemName );
        $request->session()->put('find',[
            'item_name'=> $itemName,
        ]);
        return view('items/items', compact('items','itemName'));
    }
    
    public function sell()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items/item_exhibit', compact('categories','conditions'));
    }
    
    public function update(Request $request)
    {
        $item = new Item();
        $item->name = $request['name'];
        $item->brand_name = $request['brand_name'];
        $item->detail = $request['detail'];
        $item->price = $request['price'];
        $item->stock = 1;
        $item->owner_id = Auth::id();
        $item->condition_id = $request['condition_id'];
        if ($request->hasFile('item_image'))
        {
            $file = $request->file('item_image');
            $path = Storage::disk('public')->putFile('images/items', $file);
            $item->img_url = basename($path);
        }
        $item->save();
        foreach( $request['categories'] as $category )
        {
            $item->categories()->attach($category);
        }
        return redirect('/');
    }
    
    public function show(Request $request)
    {
        $item = Item::where('id',$request['id'])->first();
        $condition = $item->condition;
        $categories = $item->categories;
        $likeNum = $item->users()->wherePivot('is_like', true)->count();
        $comments = $item->comments;
        return view('items/item_detail', compact('item','condition','categories','likeNum','comments'));
    }
    
    public function toggleLike(Request $request, $itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);
        $userItem = $item->users()->where('user_id', $user->id)->first();
        
        if( $userItem ) {
            $isLike = $userItem->pivot->is_like;
            $item->users()->updateExistingPivot($user->id, ['is_like' => !$isLike]);
        } else {
            $item->users()->attach($user->id, ['is_like' => true]);
        }
        $likeNum = $item->users()->wherePivot('is_like', true)->count();

        return response()->json(['likeNum' => $likeNum]);
    }
    
    private function findItems($isMylist, $itemName)
    {
        if (Auth::check())
        {
            $itemQuery = Item::query();
            $itemQuery = $itemQuery->where('owner_id', '!=', Auth::id());
            if( $isMylist )
            {
                /* いいねした商品 */
                return $itemQuery->whereHas('users', function ($q) {
                    $q->where('user_id', Auth::id())->where('is_like', true);
                })->orderBy('id', 'asc')->get();
            }
            
            if( isset($itemName) )
            {
                $itemQuery = $itemQuery->where('name', 'LIKE', '%'.$itemName.'%');  
            }
            return $itemQuery->get();
        }
        else
        {
            if( $isMylist )
            {
                return null;
            }
            $itemQuery = Item::query();
            if( isset($itemName) )
            {
                $itemQuery = $itemQuery->where('name', 'LIKE', '%'.$itemName.'%');  
            }
            return $itemQuery->get();
        }
    }
}
