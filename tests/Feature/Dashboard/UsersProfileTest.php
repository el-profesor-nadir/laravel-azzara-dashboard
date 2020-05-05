<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Spatie\Permission\Models\Permission;

class UsersProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_update_the_user_profile()
    {

        $user = factory(User::class)->create();

        //cannot show edit form
        $response = $this->get("/dashboard/users/$user->id/profile")
                        ->assertRedirect(route('login'));

        //cannot update data
        $response = $this->post("/dashboard/users/$user->id/profile")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_update_the_user_profile()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$user->id/profile")
                        ->assertRedirect(route('verification.notice'));

        //cannot update data
        $response = $this->actingAs($user)
                        ->post("/dashboard/users/$user->id/profile")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_update_the_user_profile()
    {
        $user = factory(User::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$user->id/profile")
                        ->assertStatus(403);

        //cannot update data
        $response = $this->actingAs($user)
                        ->post("/dashboard/users/$user->id/profile")
                        ->assertStatus(403);
    }


    /** @test */
    public function an_allowed_user_may_update_the_user_profile()
    {
        $this->withoutExceptionHandling();
        //create a user
        $user = factory(User::class)->create();

        // allow user to View users profile
        $permission = Permission::create(['name' => 'View users profile']);
        $user->givePermissionTo('View users profile');

        // allow user to Edit users profile
        $permission = Permission::create(['name' => 'Edit users profile']);
        $user->givePermissionTo('Edit users profile');

        $userToEdit = factory(User::class)->create();

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$userToEdit->id/profile")
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.users.profile.edit');

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

        $response = $this->post("/dashboard/users/$userToEdit->id/profile", $profileData)
                        ->assertRedirect("/dashboard/users/$userToEdit->id/profile")
                        ->assertSessionHas('success','User profile successfully updated.');

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
                        ->get("/dashboard/users/$userToEdit->id/profile")
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.users.profile.edit')
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
    public function user_profile_edition_require_all_fields()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View profile
        $permission = Permission::create(['name' => 'View users profile']);
        $user->givePermissionTo('View users profile');

        // allow user to Edit profile
        $permission = Permission::create(['name' => 'Edit users profile']);
        $user->givePermissionTo('Edit users profile');

        $userToEdit = factory(User::class)->create();

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
                        ->post("/dashboard/users/$userToEdit->id/profile", $profileData)
                        ->assertSessionHasErrors(['name','first_name','last_name','gender',
                                                'address','country','city','zip_code','phone']);

    }

    /** @test */
    public function user_profile_edition_require_valid_gender()
    {
        //create a user
        $user = factory(User::class)->create();

         // allow user to View profile
        $permission = Permission::create(['name' => 'View users profile']);
        $user->givePermissionTo('View users profile');

        // allow user to Edit profile
        $permission = Permission::create(['name' => 'Edit users profile']);
        $user->givePermissionTo('Edit users profile');

        $userToEdit = factory(User::class)->create();

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
                        ->post("/dashboard/users/$userToEdit->id/profile", $profileData)
                        ->assertSessionHasErrors(['gender']);
    }

}
