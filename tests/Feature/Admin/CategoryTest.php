<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Admin;
use App\Models\User;

class CategoryTest extends TestCase
{
   use RefreshDatabase;

   // カテゴリ一覧ページ(indexアクション)
    // 1.未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index()
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_adminUser_can_access_admin_categories_index()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.categories.index'));

        $response->assertStatus(200);
    }


    // カテゴリ登録機能(storeアクション)
    // 1.未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_access_admin_categories_store()
    {
        $category = [
            'name' => 'テスト',
        ];

        // メモ：post()の第二引数に連想配列(ファクトリー内に記載)を指定することでデータを送信できる。
        $response = $this->post(route('admin.categories.store'), $category);

        $this->assertDatabaseMissing('categories', $category);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_user_cannot_access_admin_categories_store()
    {
        $user = User::factory()->create();
        $category = [
            'name' => 'テスト',
        ];

        $response = $this->actingAs($user)->post(route('admin.categories.store'), $category);

        $this->assertDatabaseMissing('categories', $category);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者はカテゴリを登録できる
    public function test_adminUser_can_access_admin_categories_store()
    {
        $adminUser = Admin::factory()->create();
        $category = [
            'name' => 'テスト',
        ];

        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.categories.store'), $category);

        $this->assertDatabaseHas('categories', $category);
        $response->assertRedirect(route('admin.categories.index'));
    }


    // カテゴリ更新機能(updateアクション)
    // 1.未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_update_admin_categories()
    {
        $old_category = Category::factory()->create();

        $new_category = [
            'name' => '新テスト',
        ];

        $response = $this->patch(route('admin.categories.update', $old_category), $new_category);

        $this->assertDatabaseMissing('categories', $new_category);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_user_cannot_update_admin_categories()
    {
        $user = User::factory()->create();
        $old_category = Category::factory()->create();

        $new_category = [
            'name' => '新テスト',
        ];

        $response = $this->actingAs($user)->patch(route('admin.categories.update', $old_category), $new_category);

        $this->assertDatabaseMissing('categories', $new_category);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者はカテゴリを更新できる
    public function test_adminUser_can_update_admin_categories()
    {
        $adminUser = Admin::factory()->create();
        $old_category = Category::factory()->create();

        $new_category = [
            'name' => '新テスト',
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.categories.update', $old_category), $new_category);

        $this->assertDatabaseHas('categories', $new_category);
        $response->assertRedirect(route('admin.categories.index'));
    }


    // カテゴリ削除機能(destroyアクション)
    // 1.未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_destroy_admin_categories()
    {
        $category = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_user_cannot_destroy_admin_categories()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者はカテゴリを削除できる
    public function test_adminUser_can_destroy_admin_categories()
    {
        $adminUser = Admin::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.categories.destroy', $category));
        
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.categories.index'));
    }
}
