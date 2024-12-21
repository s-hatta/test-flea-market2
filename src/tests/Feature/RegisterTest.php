<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    
    /*
        1. 会員登録ページを開く
        2. 名前を入力せずに他の必要項目を入力する
        3. 登録ボタンを押す
        
        「お名前を入力してください」というバリデーションメッセージが表示されること
    */
    public function test_register_validation_name_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }
    
    /*
        1. 会員登録ページを開く
        2. メールアドレスを入力せずに他の必要項目を入力する
        3. 登録ボタンを押す
        
        「メールアドレスを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_register_validation_email_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    
    /*
        1. 会員登録ページを開く
        2. パスワードを入力せずに他の必要項目を入力する
        3. 登録ボタンを押す
        
        「パスワードを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_register_validation_password_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }
    
    /*
        1. 会員登録ページを開く
        2. 7文字以下のパスワードと他の必要項目を入力する
        3. 登録ボタンを押す
        
        「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示されること
    */
    public function test_register_validation_password_min()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }
    
    /*
        1. 会員登録ページを開く
        2. 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する
        3. 登録ボタンを押す
        
        「パスワードと一致しません」というバリデーションメッセージが表示されること
    */
    public function test_register_validation_password_confirmed()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }
    
    /*
        1. 会員登録ページを開く
        2. 全ての必要項目を正しく入力する 
        3. 登録ボタンを押す
        
        会員情報が登録され、ログイン画面に遷移すること
    */
    public function test_register_success_and_redirect(): void
    {
        /* 会員登録 */
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* 会員情報が登録されているか */
        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com'
        ]);
        $user = User::where('email', 'test@example.com')->first();

        /* メール認証画面が表示されているか */
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');

        /* メール認証 */
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        /* 認証URLにアクセス */
        $response = $this->actingAs($user)->get($verificationUrl);

        /* 認証後の確認 */
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/?verified=1');  //loginではなくこちら
    }
}
