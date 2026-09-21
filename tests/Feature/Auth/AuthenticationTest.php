<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesSimasetFixtures;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesSimasetFixtures;
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->withoutVite();

        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login');
    }

    public function test_admin_can_login_successfully(): void
    {
        $this->withoutVite();

        $admin = $this->user('admin', overrides: [
            'username' => 'admin.simaset',
            'email' => 'admin@simaset.test',
        ]);

        $this->post(route('login.store'), [
            'login' => $admin->username,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_password_is_rejected(): void
    {
        $this->withoutVite();

        $admin = $this->user('admin', overrides: ['username' => 'admin.simaset']);

        $this->from(route('login'))->post(route('login.store'), [
            'login' => $admin->username,
            'password' => 'password-salah',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_page(): void
    {
        $this->withoutVite();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}
