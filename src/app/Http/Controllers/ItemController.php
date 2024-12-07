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
        $isMylist = $request->query('tab') === 'mylist';
        $items = $this->findItems( $isMylist, $request['item_name'] );
        return view('items/items', compact('items'));
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
            return ($isMylist)? null:Item::all();
        }
    }
}
