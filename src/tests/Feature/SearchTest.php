<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $targetItem;
    private $otherItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $condition = Condition::first();

        $this->targetItem = Item::factory()->create([
            'owner_id' => $this->user->id,
            'condition_id' => $condition->id,
            'name' => 'テストABCDEFG',
            'stock' => 1
        ]);

        $this->otherItem = Item::factory()->create([
            'owner_id' => $this->user->id,
            'condition_id' => $condition->id,
            'name' => 'テストHIJKLMN',
            'stock' => 1
        ]);
    }

    /*
        1. 検索欄にキーワードを入力 
        2. 検索ボタンを押す
        
        部分一致する商品が表示される
    */
    public function test_search_001()
    {
        $response = $this->post('/', ['item_name' => 'DEF']);
        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($this->targetItem));
        $this->assertFalse($items->contains($this->otherItem));
        $response->assertSee('value="DEF"', false);
    }

    /*
        1. ホームページで商品を検索 
        2. 検索結果が表示される 
        3. マイリストページに遷移
        
        検索キーワードが保持されている
    */
    public function test_search_002()
    {
        $response = $this->post('/', ['item_name' => 'DEF']);
        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertTrue($items->contains($this->targetItem));
        $this->assertFalse($items->contains($this->otherItem));
        $response->assertSee('value="DEF"', false);
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('value="DEF"', false);
    }
}