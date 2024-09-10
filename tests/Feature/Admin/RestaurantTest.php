<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Admin;
use App\Models\User;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // 店舗一覧ページ(indexアクション)
    // 1.未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_index()
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_adminUser_can_access_admin_restaurants_index()
    {
        $adminUser = Admin::factory()->create();

        // メモ：actingAsの第二引数は認証用でどのguardを使うか指定することができる。
        // これがないとテストエラーになった。
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.index'));

        $response->assertStatus(200);
    }


    // 店舗詳細ページ(showアクション)
    // 1.未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.show', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_show()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
    public function test_adminUser_can_access_admin_restaurants_show()
    {
        $restaurant = Restaurant::factory()->create();

        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.show', $restaurant));

        $response->assertStatus(200);
    }


    // 店舗登録ページ(createアクション)
    // 1.未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_create()
    {
        $response = $this->get(route('admin.restaurants.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.create'));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_adminUser_can_access_admin_restaurants_create()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.create'));

        $response->assertStatus(200);
    }


    // 店舗登録機能(storeアクション)
    // 1.未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_store_admin_restaurants()
    {
        $restaurant = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50 
        ];

        // メモ：post()の第二引数に連想配列(ファクトリー内に記載)を指定することでデータを送信できる。
        $response = $this->post(route('admin.restaurants.store'), $restaurant);

        $this->assertDatabaseMissing('restaurants', $restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは店舗を登録できない
    public function test_user_cannot_store_admin_restaurants()
    {
        $user = User::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $restaurant = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds
        ];

        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurant);

        unset($restaurant['category_ids']);

        $this->assertDatabaseMissing('restaurants', $restaurant);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は店舗を登録できる
    public function test_adminUser_can_store_admin_restaurants()
    {
        $adminUser = Admin::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $restaurant = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds
        ];

        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.restaurants.store'), $restaurant);

        unset($restaurant['category_ids']);
        $this->assertDatabaseHas('restaurants', $restaurant);

        foreach ( $categoryIds as $categoryId ) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    // 店舗編集ページ(editアクション)
    // 1.未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->get(route('admin.restaurants.edit', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_user_cannot_access_admin_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $restaurant));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
    public function test_adminUser_can_access_admin_restaurants_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.edit', $restaurant));

        $response->assertStatus(200);
    }


    // 店舗更新機能(updateアクション)
    // 1.未ログインのユーザーは店舗を更新できない
    public function test_guest_cannot_update_admin_restaurants()
    {
        $old_details = Restaurant::factory()->create();

        $new_details = [
            'name' => '新テスト',
            'description' => '新テスト',
            'lowest_price' => 2000,
            'highest_price' => 10000,
            'postal_code' => '1110000',
            'address' => '新テスト',
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 100 
        ];

        $response = $this->patch(route('admin.restaurants.update', $old_details), $new_details);

        $this->assertDatabaseMissing('restaurants', $new_details);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは店舗を更新できない
    // ログイン済みの一般ユーザーは店舗にカテゴリを正しく設定できない
    public function test_user_cannot_update_admin_restaurants()
    {
        $user = User::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $old_details = Restaurant::factory()->create();

        $new_details = [
            'name' => '新テスト',
            'description' => '新テスト',
            'lowest_price' => 2000,
            'highest_price' => 10000,
            'postal_code' => '1110000',
            'address' => '新テスト',
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 100,
            'category_ids' => $categoryIds
        ];

        $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $old_details), $new_details);

        unset($new_details['category_ids']);
        $this->assertDatabaseMissing('restaurants', $new_details);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は店舗を更新できる
    public function test_adminUser_can_update_admin_restaurants()
    {
        $adminUser = Admin::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $old_details = Restaurant::factory()->create();

        $new_details = [
            'name' => '新テスト',
            'description' => '新テスト',
            'lowest_price' => 2000,
            'highest_price' => 10000,
            'postal_code' => '1110000',
            'address' => '新テスト',
            'opening_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 100,
            'category_ids' => $categoryIds
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.restaurants.update', $old_details), $new_details);

        unset($new_details['category_ids']);
        $this->assertDatabaseHas('restaurants', $new_details);

        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseHas('category_restaurant', [
                'category_id' => $categoryId,
            ]);
        }

        $response->assertRedirect(route('admin.restaurants.show', $old_details));
    }


    // 店舗削除機能(destroyアクション)
    // 1.未ログインのユーザーは店舗を削除できない
    public function test_guest_cannot_destroy_admin_restaurants()
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));

        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login'));
    }
    // 2.ログイン済みの一般ユーザーは店舗を削除できない
    public function test_user_cannot_destroy_admin_restaurants()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));

        $this->assertDatabaseHas('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は店舗を削除できる
    public function test_adminUser_can_destroy_admin_restaurants()
    {
        $adminUser = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));
        
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.restaurants.index'));
    }

}
