<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;

    protected function setUp(): void
    {
        parent::setUp();

        /* ユーザー情報 */
        $this->user = User::factory()->create();

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
        2. コメントを入力する
        3. コメントボタンを押す
        
        コメントが保存され、コメント数が増加する
    */
    public function test_comment_001()
    {
        /* ユーザーにログインする */
        $this->actingAs($this->user);

        /* コメントを入力し、コメントボタンを押す */
        $commentText = 'test comment.';
        $this->assertEquals($this->item->comments()->count(), 0);
        $response = $this->post("/comments/{$this->item->id}", [
            'comment' => $commentText
        ]);

        /* コメントが保存され、コメント数が増加する */
        $response->assertRedirect();
        $this->assertEquals($this->item->comments()->count(), 1);
        $comments = Comment::where('user_id', $this->user->id )->get();
        $this->assertTrue($comments->contains('comment',$commentText));
    }

    /*
        1. コメントを入力する
        2. コメントボタンを押す
        
        コメントが送信されない
    */
    public function test_comment_002()
    {
        /* コメントを入力し、コメントボタンを押す */
        $response = $this->post("/comments/{$this->item->id}", [
            'comment' => 'test comment'
        ]);

        /*
            コメントが送信されない
            （コメント数が増加せず、loginへリダイレクトされる）
        */
        $response->assertRedirect('/login');
        $this->assertEquals(0, Comment::count());
    }

    /*
        1. ユーザーにログインする
        2. コメントボタンを押す
        
        バリデーションメッセージが表示される
    */
    public function test_comment_003()
    {
        /* ユーザーにログインする */
        $this->actingAs($this->user);

        /* コメントを入力せずコメントボタンを押す */
        $response = $this->post("/comments/{$this->item->id}", [
            'comment' => ''
        ]);

        /* バリデーションメッセージが表示される */
        $response->assertSessionHasErrors('comment');
        $this->assertEquals(0, Comment::count());
    }

    /*
        1. ユーザーにログインする
        2. 256文字以上のコメントを入力する
        3. コメントボタンを押す
        
        バリデーションメッセージが表示される
    */
    public function test_comment_004()
    {
        /* ユーザーにログインする */
        $this->actingAs($this->user);

        /* 256文字以上のコメントを入力し、コメントボタンを押す */
        $commentText = str_repeat('a', 256);
        $response = $this->post("/comments/{$this->item->id}", [
            'comment' => $commentText
        ]);

        $response->assertSessionHasErrors('comment');
        $this->assertEquals(0, Comment::count());
    }

    /*
        1. ユーザーにログインする
        2. 255文字以上のコメントを入力する
        3. コメントボタンを押す
        
        コメントが保存され、コメント数が増加する
        （255文字と256文字で挙動が変わるので境界値テスト。おまけ）
    */
    public function test_commet_005()
    {
        /* ユーザーにログインする */
        $this->actingAs($this->user);

        /* 255文字のコメントを入力し、コメントボタンを押す */
        $commentText = str_repeat('a', 255);

        $response = $this->post("/comments/{$this->item->id}", [
            'comment' => $commentText
        ]);

        /* コメントが保存され、コメント数が増加する */
        $response->assertRedirect();
        $this->assertEquals($this->item->comments()->count(), 1);
        $comments = Comment::where('user_id', $this->user->id )->get();
        $this->assertTrue($comments->contains('comment',$commentText));
    }
}