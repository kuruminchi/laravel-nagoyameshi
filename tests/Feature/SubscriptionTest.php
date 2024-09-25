<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // 有料プラン登録ページ(createアクション)
    // 1.未ログインのユーザーは有料プラン登録ページにアクセスできない
    public function test_guest_cannot_access_subscription_create()
    {
        $response = $this->get(route('subscription.create'));

        $response->assertRedirect(route('login'));
    }

    // 2.ログイン済みの無料会員は有料プラン登録ページにアクセスできる
    public function test_notsubscribed_user_can_access_subscription_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertStatus(200);
    }

    // 3.ログイン済みの有料会員は有料プラン登録ページにアクセスできない
    public function test_subscribed_user_cannot_access_subscription_create()
    {
        $user = User::factory()->withSubscription()->create();
        
        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertRedirect(route('subscription.edit'));
    }

    // 4.ログイン済みの管理者は有料プラン登録ページにアクセスできない
    public function test_adminUser_cannot_access_subscription_create()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('subscription.create'));

        $response->assertRedirect(route('admin.home'));
    }
}
