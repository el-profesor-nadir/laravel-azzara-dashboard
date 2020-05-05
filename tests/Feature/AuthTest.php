<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_a_login_form()
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function test_user_cannot_view_a_login_form_when_authenticated()
    {
        $user = factory(User::class)->make();

        $response = $this->actingAs($user)
                        ->get('/login');

        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function a_visitor_can_register_with_role_client()
    {
        Notification::fake();
        Notification::assertNothingSent();

        //Arrange
        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'agree' => 1,
        ];

        $role = Role::create(['name' => 'client']);

        //Act
        $response = $this->post(route('register'), $userData)
                        ->assertRedirect(route('dashboard.index'));

        //Assert
        $this->assertDatabaseHas('users', [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
        ]);

        $user = User::whereEmail('hamza@gmail.com')->first();

        // assert user email non verified
        $this->assertNull($user->email_verified_at);

        // assert email verification was not sent
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

    }
}
