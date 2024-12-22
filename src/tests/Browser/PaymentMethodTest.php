<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Condition;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentMethodTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        
        /* ユーザー情報 */
        $address = new Address([
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2',
            'building' => 'サンプルマンション202',
        ]);
        $address->save();
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'address_id' => $address->id,
        ]);

        // テストアイテムの作成
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $condition = Condition::first();
        $this->item = Item::factory()->create([
            'name' => 'Test Item',
            'price' => 1000,
            'condition_id' => $condition->id,
            'stock' => 1,
        ]);
    }

    /*
        1. 支払い方法選択画面を開く
        2. プルダウンメニューから支払い方法を選択する
        
        選択した支払い方法が正しく反映される
    */
    public function test_payment_method_001()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/purchase/' . $this->item->id)
                    
                    /* 初期状態の確認 */
                    ->assertSeeIn('#selected-payment', '選択してください')
                    ->screenshot('payment001')
                    
                    /* コンビニ払いを選択 */
                    ->select('#payment', 'cvs')
                    ->assertSeeIn('#selected-payment', 'コンビニ払い')
                    ->screenshot('payment002')
                    
                    /* カード払いを選択 */
                    ->select('#payment', 'card')
                    ->assertSeeIn('#selected-payment', 'カード払い')
                    ->screenshot('payment003');
        });
    }
}