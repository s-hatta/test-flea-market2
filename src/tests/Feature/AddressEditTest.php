<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use App\Models\Condition;
use App\Services\StripePaymentService;
use Mockery;

class AddressEditTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;
    private $address;
    private $newAddress;

    protected function setUp(): void
    {
        parent::setUp();

        /* 変更前の住所 */
        $this->currentAddress = [
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2',
            'building' => 'サンプルマンション202'
        ];

        /* 変更後の住所 */
        $this->newAddress = [
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2',
            'building' => 'サンプルマンション202'
        ];

        /* ユーザー情報 */
        $this->address = new Address($this->currentAddress);
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
            'stock' => 1
        ]);
    }

    /*
        1. ユーザーにログインする
        2. 送付先住所変更画面で住所を登録する
        3. 商品購入画面を再度開く

        登録した住所が商品購入画面に正しく反映される
    */
    public function test_address_edit_001()
    {
        $this->actingAs($this->user);

        /* 商品購入画面を最初に開くと変更前の住所になっているか */
        $response = $this->get("/purchase/{$this->item->id}");
        $response->assertStatus(200);
        $response->assertSee($this->currentAddress['postal_code']);
        $response->assertSee($this->currentAddress['address']);
        $response->assertSee($this->currentAddress['building']);

        /* 送付先住所変更画面で変更前の住所が表示されているか */
        $response = $this->get("/purchase/address/{$this->item->id}");
        $response->assertStatus(200);
        $response->assertSee($this->currentAddress['postal_code']);
        $response->assertSee($this->currentAddress['address']);
        $response->assertSee($this->currentAddress['building']);

        /* 住所を変更 */
        $response = $this->post("/purchase/address/{$this->item->id}", $this->newAddress);

        /* 商品購入画面を再び開くと変更後の住所になっているか */
        $response = $this->get("/purchase/{$this->item->id}");
        $response->assertStatus(200);
        $response->assertSee($this->newAddress['postal_code']);
        $response->assertSee($this->newAddress['address']);
        $response->assertSee($this->newAddress['building']);
    }

    /*
        1. ユーザーにログインする
        2. 送付先住所変更画面で住所を登録する
        3. 商品を購入する

        正しく送付先住所が紐づいている
    */
    public function test_address_edit_002()
    {
        /* 住所を変更して商品を購入 */
        $this->actingAs($this->user);
        $response = $this->get("/purchase/{$this->item->id}");
        $response = $this->get("/purchase/address/{$this->item->id}");
        $response = $this->post("/purchase/address/{$this->item->id}", $this->newAddress);
        $response = $this->get("/purchase/{$this->item->id}");
        $this->post("/purchase/address/{$this->item->id}", $this->newAddress);

        /* 購入処理実行 */
        $userItem = $this->item->users()->where('user_id', $this->user->id)->first();
        $response = $this->post("/purchase/{$this->item->id}", [
            'payment_method' => 'card',
            'postal_code' => $userItem->address->postal_code,
            'address' => $userItem->address->address,
        ]);
        $response->assertStatus(302);

        /* ユーザーIDと商品IDから注文情報を取得 */
        $order = Order::where('user_id', $this->user->id)
            ->where('item_id', $this->item->id)
            ->first();
        $this->assertNotNull($order, 'Order not found');

        /* 注文情報の住所が変更後住所と紐づいているか */
        $orderAddress = Address::find($order->address_id);
        $this->assertNotNull($orderAddress);
        $this->assertEquals($this->newAddress['postal_code'], $orderAddress->postal_code);
        $this->assertEquals($this->newAddress['address'], $orderAddress->address);
        $this->assertEquals($this->newAddress['building'], $orderAddress->building);
    }
}
