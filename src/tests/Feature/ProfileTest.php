<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use App\Models\Condition;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        /* ユーザー情報 */
        $address = new Address([
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2',
            'building' => 'サンプルマンション202',
        ]);
        $address->save();
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'img_url' => 'test_user.jpg',
            'address_id' => $address->id,
        ]);

        /* 商品状態 */
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        
        /* 出品した商品 */
        for( $i = 0; $i < 3; $i++ )
        {
            Item::factory()->create([
                'owner_id' => $this->user->id,
                'condition_id' => Condition::inRandomOrder()->first()->id,
                'name' => "出品した商品". $i,
                'img_url' => 'test_item.jpg',
                'stock' => 1,
            ]);
        }
        
        /* 購入した商品 */
        for( $i = 0; $i < 2; $i++ )
        {
            /* 商品を作成 */
            $item = Item::factory()->createOne([
                'owner_id' => User::factory()->create()->id,
                'condition_id' => Condition::inRandomOrder()->first()->id,
                'name' => "購入した商品". $i,
                'img_url' => 'test_item.jpg',
                'stock' => 0,
            ]);
            
            /* 注文情報と結び付け */
            $order = new Order([
                'user_id' => $this->user->id,
                'item_id' => $item->id,
                'price' => $item->price,
                'address_id' => $this->user->address_id,
                'payment_status' => 'paid',
            ]);
            $order->save();
        }
    }

    /*
        1. ユーザーにログインする
        2. プロフィールページを開く
        
        プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
    */
    public function test_profile_001()
    {
        $exhibitedItems = Item::where('owner_id', '=', $this->user->id)->get();
        $purchasedItems = Order::where('user_id',  $this->user->id)->with('item')->get()->pluck('item');
        
        /* ユーザーにログインする */
        $this->actingAs($this->user);
        
        /* プロフィールページを開く */
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        
        /* プロフィール画像 */
        $response->assertSee('storage/images/users/' . $this->user->img_url, false);
        
        /* ユーザー名 */
        $response->assertSee($this->user->name);
        
        /* 出品した商品が表示されているか */
        foreach ($exhibitedItems as $item) {
            $response->assertSee($item->name);
            $response->assertSee('storage/images/items/' . $item->img_url, false);
        }
        foreach ($purchasedItems as $item) {
            $response->assertDontSee($item->name);
        }
        
        /* プロフィールページを開く */
        $response = $this->get('/mypage/?tab=buy');
        $response->assertStatus(200);
        
        /* プロフィール画像 */
        $response->assertSee('storage/images/users/' . $this->user->img_url, false);
        
        /* ユーザー名 */
        $response->assertSee($this->user->name);
        
        /* 購入した商品が表示されているか */
        foreach ($exhibitedItems as $item) {
            $response->assertDontSee($item->name);
        }
        foreach ($purchasedItems as $item) {
            $response->assertSee($item->name);
            $response->assertSee('storage/images/items/' . $item->img_url, false);
        }
    }
}