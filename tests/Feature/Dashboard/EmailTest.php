<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Dashboard\Profile\EmailUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_edit_to_their_email()
    {
        $response = $this->get('/dashboard/email')
                        ->assertRedirect(route('login'));

        //cannot save data
        $response = $this->post('/dashboard/email')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_their_email()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                        ->get('/dashboard/email')
                        ->assertRedirect(route('verification.notice'));

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/email')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_their_email()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                        ->get('/dashboard/email')
                        ->assertStatus(403);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/email')
                        ->assertStatus(403);
    }


    /** @test */
    public function an_allowed_user_may_edit_their_email()
    {
        //create a user
        $user = factory(User::class)->create();
        $oldemail = $user->email;

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get('/dashboard/email')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.email.index');

        /**
         * assert that the allowed user can save data
        */

        Mail::fake();
        Mail::assertNothingSent();

        Notification::fake();
        Notification::assertNothingSent();

        $emailData = [
            'password_current' => 'password',
            'email' => 'newemail@gmail.com',
            'email_confirmation' => 'newemail@gmail.com',
        ];

        $response = $this->post('/dashboard/email', $emailData)
                        ->assertRedirect(route('login'))
                        ->assertSessionHas('success','Email successfully updated. Please check your new email for an activation link.');

        // assert name email changed
        $this->assertDatabaseHas('users', [
            'email' => 'newemail@gmail.com',
        ]);

        //assert email was sent
        $userEdited = User::whereEmail($user->email)->first();
        Mail::assertSent(EmailUpdated::class, function ($mail) use ($oldemail) {
            return $mail->hasTo($oldemail) ;
        });

        $user = User::whereEmail('newemail@gmail.com')->first();

        // assert user email non verified
        $this->assertNull($user->email_verified_at);

        // assert email verification was not sent
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

    }

    /** @test */
    public function email_edition_require_all_fields()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');

        $emailData = [
            'password_current' => null,
            'email' => null,
            'email_confirmation' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/email', $emailData)
                        ->assertSessionHasErrors(['password_current','email']);

    }

    /** @test */
    public function email_edition_require_the_current_password()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');

        $emailData = [
            'password_current' => 'otherpassword',
            'email' => 'newemail@gmail.com',
            'email_confirmation' => 'newemail@gmail.com',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/email', $emailData)
                        ->assertSessionHasErrors(['password_current']);

    }

    /** @test */
    public function email_edition_require_email_confirmation()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');

        $emailData = [
            'password_current' => 'password',
            'email' => 'newemail@gmail.com',
            'email_confirmation' => 'emailnotconfirmed@gmail.com',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/email', $emailData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function email_edition_require_valid_email()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');

        $emailData = [
            'password_current' => 'password',
            'email' => '134566',
            'email_confirmation' => '134566',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/email', $emailData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function email_edition_require_unique_email()
    {
        //create a user
        $user = factory(User::class)->create(['email'=>'hamza@gmail.com']);
        $user2 = factory(User::class)->create(['email'=>'hamza2@gmail.com']);

        // allow user to View email
        $permission = Permission::create(['name' => 'View email']);
        $user->givePermissionTo('View email');

        // allow user to Edit email
        $permission = Permission::create(['name' => 'Edit email']);
        $user->givePermissionTo('Edit email');


        $emailData = [
            'password_current' => 'password',
            'email' => 'hamza2@gmail.com',
            'email_confirmation' => 'hamza2@gmail.com',
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/email', $emailData)
                        ->assertSessionHasErrors(['email']);

    }


}
