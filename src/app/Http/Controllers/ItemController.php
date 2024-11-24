<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
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
