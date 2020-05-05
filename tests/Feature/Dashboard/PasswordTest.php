<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Dashboard\Profile\PasswordUpdated;

class PasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_edit_to_their_password()
    {
        $response = $this->get('/dashboard/password')
                        ->assertRedirect(route('login'));

        //cannot save data
        $response = $this->post('/dashboard/password')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_their_password()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                        ->get('/dashboard/password')
                        ->assertRedirect(route('verification.notice'));

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/password')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_their_password()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                        ->get('/dashboard/password')
                        ->assertStatus(403);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/password')
                        ->assertStatus(403);
    }


    /** @test */
    public function an_allowed_user_may_edit_their_password()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View password
        $permission = Permission::create(['name' => 'View password']);
        $user->givePermissionTo('View password');

        // allow user to Edit password
        $permission = Permission::create(['name' => 'Edit password']);
        $user->givePermissionTo('Edit password');

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get('/dashboard/password')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.password.index');

        /**
         * assert that the allowed user can save data
        */

        Mail::fake();

        // Assert that no mailables were sent...
        Mail::assertNothingSent();

        $passwordData = [
            'password_current' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->post('/dashboard/password', $passwordData)
                        ->assertRedirect(route('dashboard.password.index'))
                        ->assertSessionHas('success','Password successfully updated.');

        //assert password was changed
        $userEdited = User::whereEmail($user->email)->first();
        $this->assertNotTrue(Hash::check('password',$userEdited->password));
        $this->assertTrue(Hash::check('newpassword',$userEdited->password));

        //assert email was sent
        Mail::assertSent(PasswordUpdated::class, function ($mail) use ($userEdited) {
            return $mail->hasTo($userEdited->email) ;
        });

    }

    /** @test */
    public function password_edition_require_all_fields()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View password
        $permission = Permission::create(['name' => 'View password']);
        $user->givePermissionTo('View password');

        // allow user to Edit password
        $permission = Permission::create(['name' => 'Edit password']);
        $user->givePermissionTo('Edit password');

        $passwordData = [
            'password_current' => null,
            'password' => null,
            'password_confirmation' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/password', $passwordData)
                        ->assertSessionHasErrors(['password_current','password']);

    }

    /** @test */
    public function password_edition_require_the_current_password()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View password
        $permission = Permission::create(['name' => 'View password']);
        $user->givePermissionTo('View password');

        // allow user to Edit password
        $permission = Permission::create(['name' => 'Edit password']);
        $user->givePermissionTo('Edit password');

        $passwordData = [
            'password_current' => 'otherpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/password', $passwordData)
                        ->assertSessionHasErrors(['password_current']);

    }

    /** @test */
    public function password_edition_require_password_confirmation()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View password
        $permission = Permission::create(['name' => 'View password']);
        $user->givePermissionTo('View password');

        // allow user to Edit password
        $permission = Permission::create(['name' => 'Edit password']);
        $user->givePermissionTo('Edit password');

        $passwordData = [
            'password_current' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'passwordnotconfirmed',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/password', $passwordData)
                        ->assertSessionHasErrors(['password']);

    }


}
