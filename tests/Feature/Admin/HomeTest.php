<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // 管理者トップページ(indexアクション)
    // 1.未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_guest_cannot_access_adminhome()
    {
        $response = $this->get(route('admin.home'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側のトップページにアクセスできない
    public function test_user_cannot_access_adminhome()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_adminUser_can_access_adminhome()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.home'));
        $response->assertStatus(200);
    }
}
