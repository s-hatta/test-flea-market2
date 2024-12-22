<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private $item;
    private $user;
    private $categories;
    private $conditions;
    private $comments;

    protected function setUp(): void
    {
        parent::setUp();
        
        /* 商品状態 */
        $this->seed(\Database\Seeders\ConditionsTableSeeder::class);
        $this->conditions = Condition::all();
        
        /* 商品カテゴリ */
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->categories = Category::all();
        
        /* テスト用の商品情報 */
        $this->ownUser = User::factory()->create();
        $this->item = Item::factory()->create([
            'owner_id' => $this->ownUser->id,
            'condition_id' => $this->conditions[0]->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 10000,
            'detail' => '商品の詳細説明文です。',
            'img_url' => 'test_item.jpg',
            'stock' => 1
        ]);
        $this->item->categories()->attach($this->categories[0]);

        /* コメント追加 */
        $this->commentUsers = User::factory(3)->create();
        foreach( $this->commentUsers as $commentUser )
            {
            $comment = new Comment([
                'user_id' => $commentUser->id,
                'item_id' => $this->item->id,
                'comment' => 'テストコメント'. $commentUser->name,
            ]);
            $comment->save();
            }
        $this->comments = Comment::where( 'item_id', $this->item->id )->get();
        
        /* いいね追加 */
        $this->likeUsers = User::factory(2)->create();
        foreach( $this->likeUsers as $likeUser )
        {
            $this->item->users()->attach($likeUser->id, ['is_like' => true]);
        }
    }

    /*
        1. 商品詳細ページを開く
        
        すべての情報が商品詳細ページに表示されている
    */
    public function test_item_detail_001()
    {
        /* 商品詳細ページを開く */
        $response = $this->get("/item/{$this->item->id}");
        $response->assertStatus(200);
        
        /* 商品画像 */
        $response->assertSee('storage/images/items/' . $this->item->img_url);

        /* 商品名 */
        $response->assertSee($this->item->name);
        
        /* ブランド名 */
        $response->assertSee($this->item->brand_name);
        
        /* 価格 */
        $response->assertSeeInOrder(['class="price"', number_format($this->item->price)]);
        
        /* いいね数 */
        $likeCount = $this->item->users()->wherePivot('is_like', true)->count();
        $response->assertSeeInOrder(['id="like-count"', (string)$likeCount]);
        
        /* コメント数 */
        $commentCount = $this->item->comments->count();
        $response->assertSeeInOrder(['<td>', (string)$likeCount], '</td>');
        
        /* 商品説明 */
        $response->assertSee($this->item->detail);
        
        /* 商品情報（カテゴリ、商品の状態） */
        $response->assertSee($this->item->condition->condition);
        foreach ($this->item->categories as $category) {
            $response->assertSee($category->content);
        }
        
        /* コメント */
        $response->assertSee('コメント (' . $this->item->comments->count() . ')');
        foreach( $this->commentUsers as $commentUser )
        {
            $response->assertSee($commentUser->name);
            $response->assertSee($commentUser->comment);
        }
    }

    /*
        1. 商品詳細ページを開く
        
        複数選択されたカテゴリが商品詳細ページに表示されている
    */
    public function test_item_detail_002()
    {
        /* カテゴリを追加 */
        $this->item->categories()->attach($this->categories[2]);
        $this->item->categories()->attach($this->categories[4]);

        /* 商品詳細ページを開く */
        $response = $this->get("/item/{$this->item->id}");
        $response->assertStatus(200);

        /* 複数選択されたカテゴリが商品詳細ページに表示されている） */
        $categories = $this->item->categories()->get();
        foreach( $categories as $category ) {
            $response->assertSee($category->content);
        }
        $this->assertEquals(3, $this->item->categories()->count());
    }
}