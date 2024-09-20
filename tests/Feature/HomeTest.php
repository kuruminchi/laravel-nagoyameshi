<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Admin;
use App\Models\User;

class HomeTest extends TestCase
{
    // 1.未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_guest_can_access_home()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    // 2.ログイン済みの一般ユーザーは会員側のトップページにアクセスできる
    public function test_user_can_access_home()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
    }

    // 3.ログイン済みの管理者は会員側のトップページにアクセスできない
    public function test_adminUser_cannot_access_home()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('home'));

        $response->assertRedirect(route('admin.home'));
    }
}
