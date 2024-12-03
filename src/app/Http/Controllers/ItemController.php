<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $isMylist = $request->query('tab') === 'mylist';
        
        if (Auth::check())
        {
            $itemQuery = Item::query();
            $itemQuery = $itemQuery->where('user_id', '!=', Auth::id());
            if( $isMylist )
            {
                /* いいねした商品 */
                $itemQuery = $itemQuery->whereHas('likes', function ($q) {
                    $q->where('likes.user_id', '=', Auth::id());
                })->orderBy('id','asc');
            }
            $items = $itemQuery->get();
        }
        else
        {
            $items = ($isMylist)? null:Item::all();
        }
        return view('items/items', compact('items'));
    }
    
    public function sell()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items/item_exhibit', compact('categories','conditions'));
    }
    
    public function show(Request $request)
    {
        $item = Item::where('id',$request['id'])->first();
        $condition = $item->condition;
        $categories = $item->categories;
        $likes = $item->likes;
        $comments = $item->comments;
        return view('items/item_detail', compact('item','condition','categories','likes','comments'));
    }
}
