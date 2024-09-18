<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Term;
use App\Models\Admin;
use App\Models\User;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // 利用規約ページ(indexアクション)
    // 1.未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_terms_index()
    {
        $response = $this->get(route('admin.terms.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_user_cannot_access_admin_terms_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_adminUser_can_access_admin_terms_index()
    {
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.index'));

        $response->assertStatus(200);
    }


    // 利用規約編集ページ(editアクション)
    // 1.未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_terms_edit()
    {
        $terms = Term::factory()->create();

        $response = $this->get(route('admin.terms.edit', $terms));

        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_user_cannot_access_admin_terms_edit()
    {
        $terms = Term::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.edit', $terms));

        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_adminUser_can_access_admin_terms_edit()
    {
        $terms = Term::factory()->create();
        $adminUser = Admin::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.edit', $terms));

        $response->assertStatus(200);
    }


    // 利用規約更新機能(updateアクション)
    // 1.未ログインのユーザーは利用規約を更新できない
    public function test_guest_cannot_update_admin_terms()
    {
        $old_details = Term::factory()->create();

        $new_details = [
            'content' => '新テスト',
        ];

        $response = $this->patch(route('admin.terms.update', $old_details), $new_details);

        $this->assertDatabaseMissing('terms', $new_details);
        $response->assertRedirect(route('admin.login'));
    }

    // 2.ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_user_cannot_update_admin_terms()
    {
        $user = User::factory()->create();
        $old_details = Term::factory()->create();

        $new_details = [
            'content' => '新テスト',
        ];

        $response = $this->actingAs($user)->patch(route('admin.terms.update', $old_details), $new_details);

        $this->assertDatabaseMissing('terms', $new_details);
        $response->assertRedirect(route('admin.login'));
    }

    // 3.ログイン済みの管理者は利用規約を更新できる
    public function test_adminUser_can_update_admin_terms()
    {
        $adminUser = Admin::factory()->create();
        $old_details = Term::factory()->create();

        $new_details = [
            'content' => '新テスト',
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.terms.update', $old_details), $new_details);

        $this->assertDatabaseHas('terms', $new_details);
        $response->assertRedirect(route('admin.terms.index'));
    }

}
