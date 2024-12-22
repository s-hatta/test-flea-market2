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
        
        // Get the response content for DOM parsing
        $content = $response->getContent();
        
        /* 出品した商品の中身を取得（id="exhibitionsからその閉じタグまで） */
        $exhibitionsIndex = strpos($content, 'id="exhibitions"');
        $exhibitionsEnd = $this->findClosingDiv($content, $exhibitionsIndex);
        $exhibitionsContent = substr($content, $exhibitionsIndex, $exhibitionsEnd - $exhibitionsIndex);
        
        /* 購入した商品の中身を取得（id="purchasesからその閉じタグまで） */
        $purchasesIndex = strpos($content, 'id="purchases"');
        $purchasesEnd = $this->findClosingDiv($content, $purchasesIndex);
        $purchasesContent = substr($content, $purchasesIndex, $purchasesEnd - $purchasesIndex);
        
        /* 出品した商品一覧 */
        foreach ($exhibitedItems as $item) {
            $this->assertStringContainsString($item->name, $exhibitionsContent);
            $this->assertStringContainsString('storage/images/items/' . $item->img_url, $exhibitionsContent);
            $this->assertStringNotContainsString($item->name, $purchasesContent);
        }
        $this->assertEquals($exhibitedItems->count(), substr_count($exhibitionsContent, 'item-card'));
        
        /* 購入した商品一覧 */
        foreach ($purchasedItems as $item) {
            $this->assertStringContainsString($item->name, $purchasesContent);
            $this->assertStringContainsString('storage/images/items/' . $item->img_url, $purchasesContent);
            $this->assertStringNotContainsString($item->name, $exhibitionsContent);
        }
        $this->assertEquals($purchasedItems->count(), substr_count($purchasesContent, 'item-card'));
    }

    /*
        指定されたdiv要素の終了位置を見つける
    */
    private function findClosingDiv($content, $startIndex)
    {
        $divCount = 1;
        $currentIndex = $startIndex + 1;
        $contentLength = strlen($content);

        while ($divCount > 0 && $currentIndex < $contentLength) {
            $openTag = strpos($content, '<div', $currentIndex);
            $closeTag = strpos($content, '</div>', $currentIndex);

            if ($closeTag === false) {
                break;
            }

            if ($openTag === false || $closeTag < $openTag) {
                $divCount--;
                $currentIndex = $closeTag + 6;
            } else {
                $divCount++;
                $currentIndex = $openTag + 4;
            }
        }
        return $currentIndex;
    }
}