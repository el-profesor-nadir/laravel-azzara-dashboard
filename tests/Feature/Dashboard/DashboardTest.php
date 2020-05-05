<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\User;

class DashboardTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function an_authenticated_and_email_verified_user_may_access_to_dashboard()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                         ->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function an_non_authenticated_user_cannot_access_to_dashboard()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_access_to_dashboard()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                         ->get('/dashboard');

        $response->assertRedirect(route('verification.notice'));
    }

}
