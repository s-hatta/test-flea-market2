<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Address $address;

    protected function setUp(): void
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
            'img_url' => 'test_user.jpg',
            'address_id' => $address->id,
        ]);
    }

    /*
        1. ユーザーにログインする
        2. プロフィールページを開く
        
        各項目の初期値が正しく表示されている
    */
    public function test_profile_edit_001(): void
    {
        /* ユーザーにログインする */
        $response = $this->actingAs($this->user);
        
        /* プロフィールページを開く */
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);

        /* 各項目の初期値が正しく表示されていること */
        $response->assertViewHas('user', function ($user) {
            return $user->id === $this->user->id
                && $user->name === $this->user->name
                && $user->img_url === $this->user->img_url
                && $user->address->postal_code === $this->user->address->postal_code
                && $user->address->address === $this->user->address->address
                && $user->address->building === $this->user->address->building;
        });
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->img_url);
        $response->assertSee($this->user->address->postal_code);
        $response->assertSee($this->user->address->address);
        $response->assertSee($this->user->address->building);
    }
}