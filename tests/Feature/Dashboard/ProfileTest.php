<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Spatie\Permission\Models\Permission;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_access_to_their_profile()
    {
        $response = $this->get('/dashboard/profile');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_access_their_profile()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                         ->get('/dashboard/profile');

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_access_their_profile()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                         ->get('/dashboard/profile');

        $response->assertStatus(403);
    }

    /** @test */
    public function an_non_authenticated_user_cannot_edit_their_profile()
    {
        //cannot save data
        $response = $this->post('/dashboard/profile')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_their_profile()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/profile')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_their_profile()
    {
        $user = factory(User::class)->create();

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/profile')
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_edit_their_profile()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View profile
        $permission = Permission::create(['name' => 'View profile']);
        $user->givePermissionTo('View profile');

        // allow user to Edit profile
        $permission = Permission::create(['name' => 'Edit profile']);
        $user->givePermissionTo('Edit profile');

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get('/dashboard/profile')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.profile.index');

        /**
         * assert that the allowed user can save data
        */

        $profileData = [
            'name' => 'elprofesor',
            'first_name' => 'hamza',
            'last_name' => 'nadir',
            'gender' => 'male',
            'address' => 'my address',
            'country' => 'Morocco',
            'city' => 'mohammedia',
            'zip_code' => '28820',
            'phone' => '0607553655',
        ];

        $response = $this->post('/dashboard/profile', $profileData)
                        ->assertRedirect(route('dashboard.profile.index'))
                        ->assertSessionHas('success','Account profile updated.');

        $this->assertDatabaseHas('users', [
            'name' => 'elprofesor',
            'first_name' => 'hamza',
            'last_name' => 'nadir',
            'gender' => 'male',
            'address' => 'my address',
            'country' => 'Morocco',
            'city' => 'mohammedia',
            'zip_code' => '28820',
            'phone' => '0607553655',
        ]);

        $response = $this->actingAs($user)
                        ->get('/dashboard/profile')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.profile.index')
                        ->assertSee('elprofesor')
                        ->assertSee('hamza')
                        ->assertSee('nadir')
                        ->assertSee('male')
                        ->assertSee('my address')
                        ->assertSee('Morocco')
                        ->assertSee('mohammedia')
                        ->assertSee('28820')
                        ->assertSee('0607553655') ;

    }

    /** @test */
    public function profile_edition_require_all_fields()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View profile
        $permission = Permission::create(['name' => 'View profile']);
        $user->givePermissionTo('View profile');

        // allow user to Edit profile
        $permission = Permission::create(['name' => 'Edit profile']);
        $user->givePermissionTo('Edit profile');

        $profileData = [
            'name' => null,
            'first_name' => null,
            'last_name' => null,
            'gender' => null,
            'address' => null,
            'country' => null,
            'city' => null,
            'zip_code' => null,
            'phone' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/profile', $profileData)
                        ->assertSessionHasErrors(['name','first_name','last_name','gender',
                                                'address','country','city','zip_code','phone']);

    }

    /** @test */
    public function profile_edition_require_valid_gender()
    {
        //create a user
        $user = factory(User::class)->create();

         // allow user to View profile
        $permission = Permission::create(['name' => 'View profile']);
        $user->givePermissionTo('View profile');

        // allow user to Edit profile
        $permission = Permission::create(['name' => 'Edit profile']);
        $user->givePermissionTo('Edit profile');

         $profileData = [
            'name' => 'elprofesor',
            'first_name' => 'hamza',
            'last_name' => 'nadir',
            'gender' => 'notValid',
            'address' => 'my address',
            'country' => 'Morocco',
            'city' => 'mohammedia',
            'zip_code' => '28820',
            'phone' => '0607553655',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/profile', $profileData)
                        ->assertSessionHasErrors(['gender']);
    }

}
