<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use DataBase\Seeders\ConditionsTableSeeder;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $otherUser;
    private $condition_id;

    protected function setUp(): void
    {
        parent::setUp();

        /* ユーザーを作成 */
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();

        /* 商品の状態を作成 */
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $this->condition_id = Condition::first()->id;
    }

    /*
        1. ユーザーにログインをする
        2. マイリストページを開く
        
        いいねをした商品が表示される
    */
    public function test_liked_items_are_displayed()
    {
        $likedItem = $this->makeLikedItem($this->condition_id);
        $likedSoldItem = $this->makeLikedSoldItem($this->condition_id);
        $notLikedItem = $this->makeNotLikedItem($this->condition_id);
        $notLikedSoldItem = $this->makeNotLikedSoldItem($this->condition_id);
        
        $response = $this->actingAs($this->user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($likedItem));
        $this->assertTrue($items->contains($likedSoldItem));
        $this->assertFalse($items->contains($notLikedItem));
        $this->assertFalse($items->contains($notLikedSoldItem));
    }

    /*
        1. ユーザーにログインをする
        2. マイリストページを開く
        3. 購入済み商品を確認する
        
        購入済み商品に「Sold」のラベルが表示される
    */
    public function test_sold_items_show_sold_label1()
    {
        $likedSoldItem = $this->makeLikedSoldItem($this->condition_id);
        
        $response = $this->actingAs($this->user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('class="sold-text"', false);
    }
    public function test_sold_items_show_sold_label2()
    {
        $likedItem = $this->makeLikedItem($this->condition_id);
        
        $response = $this->actingAs($this->user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee('class="sold-text"', false);
    }

    /*
        1. ユーザーにログインをする
        2. マイリストページを開く
        
        自分が出品した商品が一覧に表示されない
    */
    public function test_own_items_are_not_displayed()
    {
        $likedItem = $this->makeLikedItem($this->condition_id);
        $likedSoldItem = $this->makeLikedSoldItem($this->condition_id);
        $notLikedItem = $this->makeNotLikedItem($this->condition_id);
        $notLikedSoldItem = $this->makeNotLikedSoldItem($this->condition_id);
        $ownItem = $this->makeOwnItem($this->condition_id);
        
        $response = $this->actingAs($this->user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($likedItem));
        $this->assertTrue($items->contains($likedSoldItem));
        $this->assertFalse($items->contains($notLikedItem));
        $this->assertFalse($items->contains($notLikedSoldItem));
        $this->assertFalse($items->contains($ownItem));
    }

    /*
        1. マイリストページを開く
        
        何も表示されない
    */
    public function test_unauthenticated_user_sees_nothing()
    {
        $likedItem = $this->makeLikedItem($this->condition_id);
        $likedSoldItem = $this->makeLikedSoldItem($this->condition_id);
        $notLikedItem = $this->makeNotLikedItem($this->condition_id);
        $notLikedSoldItem = $this->makeNotLikedSoldItem($this->condition_id);
        $ownItem = $this->makeOwnItem($this->condition_id);
        
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertNull($items);
    }
    
    /* いいねした商品を作成 */
    private function makeLikedItem($condition_id): Item
    {
    $item = Item::factory()->create([
        'owner_id' => $this->otherUser->id,
        'condition_id' => $this->condition_id,
        'stock' => 1
    ]);
    $item->users()->attach($this->user->id, ['is_like' => true]);
    return $item;
    }
    
    /* いいねした商品（購入済み）を作成 */
    private function makeLikedSoldItem($condition_id): Item
    {
        $item = Item::factory()->create([
            'owner_id' => $this->otherUser->id,
            'condition_id' => $condition_id,
            'stock' => 0
        ]);
        $item->users()->attach($this->user->id, ['is_like' => true]);
        return $item;
    }
    
    /* いいねしてない商品を作成 */
    private function makeNotLikedItem($condition_id): Item
    {
        $item = Item::factory()->create([
            'owner_id' => $this->otherUser->id,
            'condition_id' => $condition_id,
            'stock' => 1
        ]);
        return $item;
    }
        
    /* いいねしてない商品（購入済み） */
    private function makeNotLikedSoldItem($condition_id): Item
    {
        $item = Item::factory()->create([
            'owner_id' => $this->otherUser->id,
            'condition_id' => $condition_id,
            'stock' => 0
        ]);
        return $item;
    }

    /* 自分が出品した商品を作成 */
    private function makeOwnItem($condition_id): Item
    {
        $item = Item::factory()->create([
            'owner_id' => $this->user->id,
            'condition_id' => $condition_id,
            'stock' => 1
        ]);
        return $item;
    }
}