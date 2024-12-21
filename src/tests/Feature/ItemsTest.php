<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Database\Seeders\ConditionsTableSeeder;

class ItemsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Condition::truncate();
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->seed(ConditionsTableSeeder::class);
    }
    
    /*
        1. 商品ページを開く
        
        すべての商品が表示される
    */
    public function test_can_get_all_items(): void
    {
        $items = Item::factory(10)->create([
            'owner_id' => User::factory()->create()->id,
        ]);
        $response = $this->get('/');
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
        $response->assertViewHas('items');
        $this->assertEquals($items->count(), count($response->viewData('items')));
    }
    
    /*
        1. 商品ページを開く
        2. 購入済み商品を表示する
        
        購入済み商品に「Sold」のラベルが表示される
    */
    public function test_sold_items_show_sold_label1(): void
    {
        /* 購入済みの商品を作成 */
        $soldItem = Item::factory()->create([
            'owner_id' => User::factory()->create()->id,
            'stock' => 0,
        ]);
        
        /* 購入済み商品に「Sold」のラベルが表示されるか確認 */
        $response = $this->get('/');
        $response->assertSee('class="sold-text"', false);

        /* Viewの表示確認 */
        $response->assertViewHas('items');
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($soldItem));
    }
    public function test_sold_items_show_sold_label2(): void
    {
        /* 在庫ありの商品を作成 */
        $item = Item::factory()->create();
        
        /* 購入済み商品に「Sold」のラベルが表示されていないことを確認 */
        $response = $this->get('/');
        $response->assertDontSee('class="sold-text', false);
        
        /* Viewの表示確認 */
        $response->assertViewHas('items');
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($item));
    }

    /*
        1. ユーザーにログインをする
        2. 商品ページを開く
        
        自分が出品した商品が一覧に表示されない
    */
    public function test_own_items_are_not_displayed(): void
    {
        /* テストユーザーを作成 */
        $user = User::factory()->create();
        
        /* ユーザーが出品した商品を作成 */
        $ownItem = Item::factory()->create([
            'owner_id' => $user->id,
            'img_url' => 'test-image.jpg'
        ]);
        
        /* 他のユーザーの商品を作成 */
        $otherItem = Item::factory()->create([
            'owner_id' => User::factory()->create()->id,
            'stock' => 1,
            'img_url' => 'test-image.jpg'
        ]);
        
        /* ログインして商品一覧画面を表示 */
        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertDontSee($ownItem->name);   //自分が出品した商品
        $response->assertSee($otherItem->name);     //他のユーザーが出品した商品
        
        /* Viewの表示確認 */
        $response->assertViewHas('items');
        $items = $response->viewData('items');
        $this->assertFalse($items->contains($ownItem));     //自分が出品した商品が表示されない
        $this->assertTrue($items->contains($otherItem));    //他のユーザーが出品した商品が表示される
    }
}
