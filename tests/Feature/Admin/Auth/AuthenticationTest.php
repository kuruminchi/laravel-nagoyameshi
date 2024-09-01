<?php

namespace Tests\Feature\Admin\Auth;

use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
   use RefreshDatabase;

  //  管理者用のログインページが正しく表示される
   public function test_login_screen_can_be_rendered(): void
   {
     $response = $this->get('/admin/login');

     $response->assertStatus(200);
   }

  //  正しいメールアドレスとパスワードを入力すればログインできる
   public function test_admins_can_authenticate_using_the_login_screen(): void
   {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
    //  メモ：Hash->データを固定長のランダムに見えるハッシュ値に不可逆変換して置き換えること
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     $response = $this->post('/admin/login', [
      'email' => $admin->email,
      'password' => 'nagoyameshi',
     ]);

    //  管理者としてログインしていることを検証している
     $this->assertTrue(Auth::guard('admin')->check());
     $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);
   }

  //  不正なパスワードを入力した場合、ログインできない
   public function test_admins_can_not_authenticate_with_invalid_password(): void
   {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     $this->post('/admin/login', [
      'email' => $admin->email,
      'password' => 'wrong-password',
     ]);

     $this->assertGuest();
   }

  // ログイン中の管理者はログアウトできる
  public function test_admins_can_logout(): void
  {
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    // actingAsの第二引数に'admin'を指定することで管理者ログインする振る舞いになる。
    // 第二引数をしていせずに、$userと第一引数に指定すると一般ユーザーとしての振る舞いになる。
    $response = $this->actingAs($admin, 'admin')->post('/admin/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
  }

}
