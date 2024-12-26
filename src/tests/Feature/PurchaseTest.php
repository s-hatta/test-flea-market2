<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Condition;
use App\Models\Order;
use App\Services\StripePaymentService;
use Mockery;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;
    private $address;
    private $mockStripeService;

    protected function setUp(): void
    {
        parent::setUp();

        /* ユーザー情報 */
        $this->address = new Address([
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1',
            'building' => 'テストビル101'
        ]);
        $this->address->save();
        $this->user = User::factory()->create([
            'address_id' => $this->address->id
        ]);

        /* テスト用の商品情報 */
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $condition = Condition::first();
        $this->item = Item::factory()->create([
            'owner_id' => User::factory()->create()->id,
            'condition_id' => $condition->id,
            'stock' => 1,
            'price' => 1000
        ]);

        /* Mock Stripe service */
        $this->mockStripeService = Mockery::mock(StripePaymentService::class);
        $this->app->instance(StripePaymentService::class, $this->mockStripeService);
    }

    /*
        1. ユーザーにログインする
        2. 商品購入画面を開く
        3. 商品を選択して「購入する」ボタンを押下
        
        購入が完了する
    */
    public function test_purchase_001()
    {
        $this->mockStripeService->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id' => 'test_session_id',
                'url' => 'http://example.com/checkout'
            ]);

        /* ユーザーにログインする */
        $this->actingAs($this->user);
        
        /* 商品購入画面を開く */
        $response = $this->get("/purchase/{$this->item->id}" );
        $response->assertStatus(200);
        
        /* 商品を選択して「購入する」ボタンを押下 */
        $userItem = $this->item->users()->where('user_id', $this->user->id)->first();
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
            'postal_code' => $userItem->address->postal_code,
            'address' => $userItem->address->address,
        ]);
        $response->assertStatus(302);

        /* 購入が完了する */
        $order = Order::where('user_id', $this->user->id)
            ->where('item_id', $this->item->id)
            ->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->item->price, $order->price);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('test_session_id', $order->stripe_session_id);
    }

    /*
        1. ユーザーにログインする
        2. 商品購入画面を開く 
        3. 商品を選択して「購入する」ボタンを押下
        4. 商品一覧画面を表示する

        購入した商品が「sold」として表示されている
    */
    public function test_purchase_002()
    {
        $this->mockStripeService->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id' => 'test_session_id',
                'url' => 'http://example.com/checkout'
            ]);

        /* ユーザーにログインする */
        $this->actingAs($this->user);
        
        /* 商品一覧画面に「sold」が表示されていないことを確認 */
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('class="sold-text', false);
        
        /* 商品購入画面を開く */
        $response = $this->get("/purchase/{$this->item->id}" );
        $response->assertStatus(200);
        
        /* 商品を選択して「購入する」ボタンを押下 */
        $userItem = $this->item->users()->where('user_id', $this->user->id)->first();
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
            'postal_code' => $userItem->address->postal_code,
            'address' => $userItem->address->address,
        ]);
        $response->assertStatus(302);

        /* 本来ならstripeのwebhookによって数量が0になるが、テストなので強制的に書き換え */
        $this->item->stock = 0;
        $this->item->save();

        /* 商品一覧画面を表示する */
        $response = $this->get('/');
        $response->assertStatus(200);
        
        /* 購入した商品が「sold」として表示されている */
        $response->assertSee('class="sold-text', false);
    }

    /*
        1. ユーザーにログインする
        2. 商品購入画面を開く 
        3. 商品を選択して「購入する」ボタンを押下
        4. プロフィール画面を表示する

        購入した商品がプロフィールの購入した商品一覧に追加されている
    */
    public function test_purchase_003()
    {
        $this->mockStripeService->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id' => 'test_session_id',
                'url' => 'http://example.com/checkout'
            ]);

        /* ユーザーにログインする */
        $this->actingAs($this->user);
        
        /* プロフィールの購入した商品一覧になにもないことを確認 */
        $response = $this->get('/mypage/?tab=buy');
        $response->assertStatus(200);
        $response->assertViewHas('items');
        $purchasedItems = $response->viewData('items');
        $this->assertFalse($purchasedItems->contains('id',$this->item->id));
        
        /* 商品購入画面を開く */
        $response = $this->get("/purchase/{$this->item->id}" );
        $response->assertStatus(200);
        
        /* 商品を選択して「購入する」ボタンを押下 */
        $userItem = $this->item->users()->where('user_id', $this->user->id)->first();
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
            'postal_code' => $userItem->address->postal_code,
            'address' => $userItem->address->address,
        ]);
        $response->assertStatus(302);

        /* 本来ならstripeのwebhookによって数量が0になるが、テストなので強制的に書き換え */
        $order = Order::where('user_id', $this->user->id)
            ->where('item_id', $this->item->id)
            ->first();
        $order->payment_status = 'paid';
        $order->save();
        $orderItem = $order->item;
        $orderItem->stock = 0;
        $orderItem->save();

        /* プロフィール画面を表示する */
        $response = $this->get('/mypage/?tab=buy');
        $response->assertStatus(200);
        
        /* 購入した商品がプロフィールの購入した商品一覧に追加されている */
        $response->assertViewHas('items');
        $purchasedItems = $response->viewData('items');
        $this->assertTrue($purchasedItems->contains('id',$this->item->id));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}