<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class LikeTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $user;
    private $item;

    protected function setUp(): void
    {
        parent::setUp();
        
        /* ユーザー情報 */
        $this->user = User::factory()->create();
        
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
        1. ユーザーにログインする
        2. 商品詳細ページを開く
        3. いいねアイコンを押下
        
        いいねした商品として登録され、いいね合計値が増加表示される
    */
    public function test_like_001()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/item/' . $this->item->id)
                    ->assertSeeIn('#like-count', '0')
                    ->click('#like-icon')
                    ->waitFor('#like-icon[src*="icon_liked.png"]')
                    ->assertSeeIn('#like-count', '1');
        });
    }

    /*
        1. ユーザーにログインする
        2. 商品詳細ページを開く
        3. いいねアイコンを押下
        
        いいねアイコンが押下された状態では色が変化する
    */
    public function test_like_002()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/item/' . $this->item->id)
                    ->assertSourceHas('icon_like.png')
                    ->click('#like-icon')
                    ->waitFor('#like-icon[src*="icon_liked.png"]')
                    ->assertSourceHas('icon_liked.png');
        });
    }

    /*
        1. ユーザーにログインする
        2. 商品詳細ページを開く
        3. いいねアイコンを押下
        
        いいねが解除され、いいね合計値が減少表示される
    */
    public function test_like_003()
    {
        /* 事前にいいねしておく */
        $this->item->users()->attach($this->user->id, ['is_like' => true]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/item/' . $this->item->id)
                    ->assertSeeIn('#like-count', '1')
                    ->assertSourceHas('icon_liked.png')
                    ->click('#like-icon')
                    ->waitFor('#like-icon[src*="icon_like.png"]')
                    ->assertSeeIn('#like-count', '0')
                    ->assertSourceHas('icon_like.png');
        });
    }
}