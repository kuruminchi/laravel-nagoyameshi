<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // 店舗一覧ページ(indexアクション)
    // 1.未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_guest_cannot_access_restaurants_index()
    {
        $response = $this->get(route('restaurants.index'));

        $response->assertStatus(200);
    }
    
    // 2.ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
    public function test_user_can_access_admin_restaurants_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('restaurants.index'));

        $response->assertStatus(200);
    }

    // 3.ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    public function test_adminUser_cannot_access_restaurants_index()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('restaurants.index'));

        $response->assertRedirect(route('admin.home'));
    }
}
