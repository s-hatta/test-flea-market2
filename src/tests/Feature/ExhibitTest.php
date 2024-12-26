<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemExhibitTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $categories;
    private $conditions;

    protected function setUp(): void
    {
        parent::setUp();

        /* ユーザー情報 */
        $this->user = User::factory()->create();
        
        /* 商品カテゴリ */
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->categories = Category::all();
        
        /* 商品状態 */
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $this->conditions = Condition::all();
    }

    /*
        1．ユーザーにログインする
        2. 商品出品画面を開く
        3. 各項目に適切な情報を入力して保存する
        
        各項目が正しく保存されている
    */
    public function test_exhibit_001(): void
    {
        /* ユーザーにログインする */
        $this->actingAs($this->user);
        
        /* 商品出品画面を開く */
        $response = $this->get('/sell');
        $response->assertStatus(200);
        
        /* 商品データ */
        $imgFile = UploadedFile::fake()->create('test_image.jpg', 100);
        $itemData = [
            'categories' => [$this->categories[0]->id, $this->categories[1]->id],
            'condition_id' => $this->conditions[0]->id,
            'name' => 'テスト商品名',
            'brand_name' => 'テストブランド',
            'detail' => 'テスト用の説明文',
            'price' => 123456,
            'item_image' => $imgFile,
        ];
        
        /* 商品を出品 */
        $response = $this->post('/sell', $itemData);
        $response->assertRedirect('/');
        
        /* 各項目が正しく保存されていること */
        $this->assertDatabaseHas('items', [
            'owner_id' => $this->user->id,
            'condition_id' => $this->conditions[0]->id,
            'name' => $itemData['name'],
            'brand_name' => $itemData['brand_name'],
            'detail' => $itemData['detail'],
            'price' => $itemData['price'],
            'img_url' => $imgFile->hashName(),
            'stock' => 1,
        ]);
        $item = Item::where('name', $itemData['name'])->first();
        $this->assertNotNull($item->img_url);
        $this->assertDatabaseHas('categories_items', [
            'category_id' => $this->categories[0]->id,
        ]);
        $this->assertDatabaseHas('categories_items', [
            'category_id' => $this->categories[1]->id,
        ]);
    }
}