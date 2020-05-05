<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Support\Facades\Hash;
use App\User;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_access_to_users_list()
    {
        $response = $this->get('/dashboard/users');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_access_to_users_list()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                         ->get('/dashboard/users');

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_access_to_users_list()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                         ->get('/dashboard/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_access_to_users_list()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View users
        $permission = Permission::create(['name' => 'View users']);
        $user->givePermissionTo('View users');

        // create 5 users
        $users = factory(User::class,5)->create();

        // assert the allowed user can access to user list
        $response = $this->actingAs($user)
                        ->get('/dashboard/users')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.users.index')
                        ->assertViewHas('users');

        //assert see all users
        foreach ($users as $user) {
            $response->assertSee($user->name)
                    ->assertSee($user->email)
                    ->assertSee($user->roles->pluck('name')->implode(' '))
                    ->assertSee($user->created_at->format('d/m/Y'));
        }
    }

    /** @test */
    /*public function the_list_of_users_must_be__ordred_by_creation_date_and_paginated()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View users
        $permission = Permission::create(['name' => 'View users']);
        $user->givePermissionTo('View users');

        $users = [];
        // create 15 users
        for ($i=0; $i < 15; $i++) {
            $tempUser = factory(User::class)->create();
            $users[] = $tempUser;
            sleep(1);
        }

        // assert see in order
        $response = $this->actingAs($user)
                        ->get('/dashboard/users')
                        ->assertSeeInOrder([
                            $users[14]->name,
                            $users[13]->name
                        ]);;

        // assert see the last user
        $response = $this->actingAs($user)
                        ->get('/dashboard/users')
                        ->assertSee($users[14]->name)
                        ->assertSee($users[14]->email)
                        ->assertSee($users[14]->created_at->format('d/m/Y'));

        // assert dont see the first user
        $response = $this->actingAs($user)
                        ->get('/dashboard/users')
                        ->assertDontSee($users[0]->name)
                        ->assertDontSee($users[0]->email);

        // assert see the 15th user on the second page
        $response = $this->actingAs($user)
                        ->get('/dashboard/users?page=2')
                        ->assertSee($users[0]->name)
                        ->assertSee($users[0]->email)
                        ->assertSee($users[0]->created_at->format('d/m/Y'));


    }*/

    /** @test */
    public function an_non_authenticated_user_cannot_create_a_user()
    {
        //cannot show create form
        $response = $this->get('/dashboard/users/create')
                        ->assertRedirect(route('login'));

        //cannot save data
        $response = $this->post('/dashboard/users')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_create_a_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/users/create')
                        ->assertRedirect(route('verification.notice'));

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/users')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_create_a_user()
    {
        $user = factory(User::class)->create();

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/users/create')
                        ->assertStatus(403);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/users')
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_create_a_user()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add users
        $permission = Permission::create(['name' => 'Add users']);
        $user->givePermissionTo('Add users');

        // assert the allowed user can show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/users/create')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.users.create')
                        ->assertViewHas('roles');

        /**
         * assert that the allowed user can save data
         */

        $role = Role::create(['name' => 'client']);

        Notification::fake();
        Notification::assertNothingSent();

        //fisrt : user with verification email

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->post('/dashboard/users', $userData)
                        ->assertRedirect(route('dashboard.users.index'))
                        ->assertSessionHas('success','User successfully added.');

        $this->assertDatabaseHas('users', [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
        ]);

        // assert user email verified
        $user1 = User::whereEmail('hamza@gmail.com')->first();
        $this->assertNotNull($user1->email_verified_at);

        // assert email verification was not sent
        Notification::assertNotSentTo(
            $user1,
            VerifyEmail::class
        );

        //second : user need verification email

        $userData = [
            'name' => 'nadir',
            'email' => 'nadir@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
            'need_verification' => 1,
        ];

        $response = $this->post('/dashboard/users', $userData)
                        ->assertRedirect(route('dashboard.users.index'))
                        ->assertSessionHas('success','User successfully added.');

        $this->assertDatabaseHas('users', [
            'name' => 'nadir',
            'email' => 'nadir@gmail.com',
        ]);

        // assert user email non verified
        $user2 = User::whereEmail('nadir@gmail.com')->first();
        $this->assertNull($user2->email_verified_at);

        // assert email verification was not sent
        Notification::assertSentTo(
            $user2,
            VerifyEmail::class
        );

    }

    /** @test */
    public function user_creation_require_name_email_password_role()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add users
        $permission = Permission::create(['name' => 'Add users']);
        $user->givePermissionTo('Add users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => null,
            'email' => null,
            'password' => null,
            'password_confirmation' => null,
            'role_id' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/users', $userData)
                        ->assertSessionHasErrors(['name','email','password','role_id']);

    }

    /** @test */
    public function user_creation_require_valid_email()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add users
        $permission = Permission::create(['name' => 'Add users']);
        $user->givePermissionTo('Add users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'invalidemail',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/users', $userData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function user_creation_require_unique_email()
    {
        //create a user
        $user = factory(User::class)->create(['email'=>'hamza@gmail.com']);

        // allow user to add users
        $permission = Permission::create(['name' => 'Add users']);
        $user->givePermissionTo('Add users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/users', $userData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function user_creation_require_password_confirmation()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add users
        $permission = Permission::create(['name' => 'Add users']);
        $user->givePermissionTo('Add users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'diffrentpassword',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/users', $userData)
                        ->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function an_non_authenticated_user_cannot_edit_a_user()
    {
        $user = factory(User::class)->create();

        //cannot show edit form
        $response = $this->get("/dashboard/users/$user->id/edit")
                        ->assertRedirect(route('login'));

        //cannot update data
        $response = $this->put("/dashboard/users/$user->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_a_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$user->id/edit")
                        ->assertRedirect(route('verification.notice'));

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_a_user()
    {
        $user = factory(User::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$user->id/edit")
                        ->assertStatus(403);

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_edit_a_user()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to edit users
        $permission = Permission::create(['name' => 'Edit users']);
        $user->givePermissionTo('Edit users');

        //create a new client
        $userToEdit = factory(User::class)->create();
        $clientRole = Role::create(['name' => 'client']);
        $userToEdit->assignRole($clientRole);

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/users/$userToEdit->id/edit")
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.users.edit')
                        ->assertViewHas(['roles','user'])
                        ->assertSee($userToEdit->name)
                        ->assertSee($userToEdit->email);

        /**
         * assert that the allowed user can update data
         */

        $adminRole = Role::create(['name' => 'admin']);

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role_id' => $adminRole->id,
        ];

        $response = $this->put("/dashboard/users/$userToEdit->id", $userData)
                        ->assertRedirect(route('dashboard.users.index'))
                        ->assertSessionHas('success','User successfully updated.');

        // assert name email changed
        $this->assertDatabaseHas('users', [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
        ]);

        $user3 = User::whereEmail('hamza@gmail.com')->first();

        //assert password was changed
        $this->assertNotTrue(Hash::check('password',$user3->password));
        $this->assertTrue(Hash::check('newpassword',$user3->password));

        //assert role was changed
        $this->assertEquals($adminRole->id, $user3->roles()->first()->id);

        /**
         * second : user need verification email
         */

        Notification::fake();
        Notification::assertNothingSent();

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role_id' => $adminRole->id,
            'need_verification' => 1,
        ];

        $response = $this->put("/dashboard/users/$userToEdit->id", $userData);

        $user3 = User::whereEmail('hamza@gmail.com')->first();

        // assert user email non verified
        $this->assertNull($user3->email_verified_at);

        // assert email verification was not sent
        Notification::assertSentTo(
            $user3,
            VerifyEmail::class
        );

    }

    /** @test */
    public function user_edition_require_name_email_role()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to edit users
        $permission = Permission::create(['name' => 'Edit users']);
        $user->givePermissionTo('Edit users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => null,
            'email' => null,
            'password' => null,
            'password_confirmation' => null,
            'role_id' => null,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id", $userData)
                        ->assertSessionHasErrors(['name','email','role_id']);

    }

    /** @test */
    public function user_edition_require_valid_email()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to Edit users
        $permission = Permission::create(['name' => 'Edit users']);
        $user->givePermissionTo('Edit users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'invalidemail',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id", $userData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function user_edition_require_unique_email()
    {
        //create a user
        $user = factory(User::class)->create(['email'=>'hamza@gmail.com']);
        $user2 = factory(User::class)->create(['email'=>'hamza2@gmail.com']);

        // allow user to Edit users
        $permission = Permission::create(['name' => 'Edit users']);
        $user->givePermissionTo('Edit users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza2@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id", $userData)
                        ->assertSessionHasErrors(['email']);

    }

    /** @test */
    public function user_edition_require_password_confirmation()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to Edit users
        $permission = Permission::create(['name' => 'Edit users']);
        $user->givePermissionTo('Edit users');

        $role = Role::create(['name' => 'client']);

        $userData = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'diffrentpassword',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/users/$user->id", $userData)
                        ->assertSessionHasErrors(['password']);

    }

    /** @test */
    public function an_non_authenticated_user_cannot_delete_a_user()
    {
        $user = factory(User::class)->create();

        //cannot delete data
        $response = $this->delete("/dashboard/users/$user->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_delete_a_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot update data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/users/$user->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_delete_a_user()
    {
        $user = factory(User::class)->create();

        //cannot delete data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/users/$user->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_delete_a_user()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to delete users
        $permission = Permission::create(['name' => 'Delete users']);
        $user->givePermissionTo('Delete users');

        //create a new client
        $userToDelete = factory(User::class)->create();
        $clientRole = Role::create(['name' => 'client']);
        $userToDelete->assignRole($clientRole);

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->delete("/dashboard/users/$userToDelete->id")
                        ->assertRedirect(route('dashboard.users.index'))
                        ->assertSessionHas('success','User successfully deleted.');

        // assert record didnt exist in database
        $this->assertDatabaseMissing('users', [
            'name' => $userToDelete->name,
            'email' => $userToDelete->email,
        ]);

    }
}
