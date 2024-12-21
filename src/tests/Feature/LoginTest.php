<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /*
        1. ログインページを開く 
        2. メールアドレスを入力せずに他の必要項目を入力する 
        3. ログインボタンを押す
        
        「メールアドレスを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_email_validation_required(): void
    {
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /*
        1. ログインページを開く 
        2. パスワードを入力せずに他の必要項目を入力する 
        3. ログインボタンを押す
        
        「パスワードを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_password_validation_required(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /*
        1. ログインページを開く 
        2. 必要項目を登録されていない情報を入力する 
        3. ログインボタンを押す
        
        「ログイン情報が登録されていません」というバリデーションメッセージが表示されること
    */
    public function test_invalid_credentials_error(): void
    {
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    /*
        1. ログインページを開く 
        2. 全ての必要項目を入力する 
        3. ログインボタンを押す
        
        ログイン処理が実行されること
    */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'last_login_at' => '2024-01-01 00:00:00',
        ]);
        $response = $this->get('/login');
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
        $response->assertRedirect('/');
    }
}
