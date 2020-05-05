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

class RolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_access_to_roles_list()
    {
        $response = $this->get('/dashboard/roles');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_access_to_roles_list()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                         ->get('/dashboard/roles');

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_access_to_roles_list()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                         ->get('/dashboard/roles');

        $response->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_access_to_roles_list()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View roles
        $permission = Permission::create(['name' => 'View roles']);
        $user->givePermissionTo('View roles');

        //create 5 or 10 permissions
        $permissions = factory(Permission::class,rand(5,10))->create();

        // create 5 roles
        $roles = factory(Role::class,5)
                            ->create()
                            ->each(function ($role) use($permissions) {
                                $role->syncPermissions($permissions);
                            });

        // assert the allowed user can access to user list
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.roles.index')
                        ->assertViewHas('roles');

        //assert see all roles
        foreach ($roles as $role) {
            $response->assertSee($role->name)
                    ->assertSee($role->permissions->pluck('name')->implode(' | '))
                    ->assertSee($role->created_at->format('d/m/Y'));
        }
    }

    /** @test */
    /*public function the_list_of_roles_must_be__ordred_by_creation_date_and_paginated()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View roles
        $permission = Permission::create(['name' => 'View roles']);
        $user->givePermissionTo('View roles');

        $roles = [];
        // create 15 roles
        for ($i=0; $i < 15; $i++) {
            $tempRole = factory(Role::class)->create();
            $roles[] = $tempRole;
            sleep(1);
        }

        // assert see in order
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles')
                        ->assertSeeInOrder([
                            $roles[14]->name,
                            $roles[13]->name
                        ]);;

        // assert see the last user
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles')
                        ->assertSee($roles[14]->name)
                        ->assertSee($roles[14]->created_at->format('d/m/Y'));

        // assert dont see the first user
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles')
                        ->assertDontSee($roles[0]->name);

        // assert see the 15th user on the second page
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles?page=2')
                        ->assertSee($roles[0]->name)
                        ->assertSee($roles[0]->created_at->format('d/m/Y'));
    }*/

    /** @test */
    public function an_non_authenticated_user_cannot_create_a_role()
    {
        //cannot show create form
        $response = $this->get('/dashboard/roles/create')
                        ->assertRedirect(route('login'));

        //cannot save data
        $response = $this->post('/dashboard/roles')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_create_a_role()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles/create')
                        ->assertRedirect(route('verification.notice'));

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/roles')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_create_a_role()
    {
        $user = factory(User::class)->create();

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles/create')
                        ->assertStatus(403);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/roles')
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_create_a_role()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add roles
        $permission = Permission::create(['name' => 'Add roles']);
        $user->givePermissionTo('Add roles');

        // assert the allowed user can show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/roles/create')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.roles.create')
                        ->assertViewHas('permissions');

        /**
         * assert that the allowed user can save data
         */

        $permission1 = Permission::create(['name' => 'client']);
        $permission2 = Permission::create(['name' => 'admin']);

        //asser one permission
        $roleData = [
            'name' => 'newrole',
            'permissions' => [$permission1->id],
        ];

        $response = $this->post('/dashboard/roles', $roleData)
                        ->assertRedirect(route('dashboard.roles.index'))
                        ->assertSessionHas('success','Role successfully added.');

        $this->assertDatabaseHas('roles', [
            'name' => 'newrole',
        ]);

        //assert multiple permissions
        $roleData = [
            'name' => 'nicerole',
            'permissions' => [$permission1->id, $permission2->id],
        ];

        $response = $this->post('/dashboard/roles', $roleData)
                        ->assertRedirect(route('dashboard.roles.index'))
                        ->assertSessionHas('success','Role successfully added.');

        $this->assertDatabaseHas('roles', [
            'name' => 'nicerole',
        ]);

    }

    /** @test */
    public function role_creation_require_name_permissions()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add roles
        $permission = Permission::create(['name' => 'Add roles']);
        $user->givePermissionTo('Add roles');

        $roleData = [
            'name' => null,
            'permissions' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/roles', $roleData)
                        ->assertSessionHasErrors(['name','permissions']);

    }


    /** @test */
    public function role_creation_require_unique_name()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add roles
        $permission = Permission::create(['name' => 'Add roles']);
        $user->givePermissionTo('Add roles');

        $permission = Permission::create(['name' => 'newpermission']);
        $uniqueRole = Role::create(['name' => 'uniquename']);

        $permissionData = [
            'name' => 'uniquename',
            'permissions' => [$permission->id],
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/roles', $permissionData)
                        ->assertSessionHasErrors(['name']);

    }

    /** @test */
    public function an_non_authenticated_user_cannot_edit_a_role()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();

        //cannot show edit form
        $response = $this->get("/dashboard/roles/$role->id/edit")
                        ->assertRedirect(route('login'));

        //cannot update data
        $response = $this->put("/dashboard/roles/$role->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_a_role()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $role = factory(Role::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/roles/$role->id/edit")
                        ->assertRedirect(route('verification.notice'));

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/roles/$role->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_a_role()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/roles/$role->id/edit")
                        ->assertStatus(403);

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/roles/$role->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_edit_a_role()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to edit roles
        $permission = Permission::create(['name' => 'Edit roles']);
        $user->givePermissionTo('Edit roles');

        //create a role with permission
        $roleToEdit = Role::create(['name' => 'role']);
        $permission = Permission::create(['name' => 'permission']);
        $roleToEdit->givePermissionTo($permission);

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/roles/$roleToEdit->id/edit")
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.roles.edit')
                        ->assertViewHas(['role','permissions'])
                        ->assertSee($roleToEdit->name);

        /**
         * assert that the allowed user can update data
         */

        //create new permission
        $newPermission = Permission::create(['name' => 'newpermission']);

        $roleData = [
            'name' => 'newrole',
            'permissions' => [$newPermission->id],
        ];

        $response = $this->put("/dashboard/roles/$roleToEdit->id", $roleData)
                        ->assertRedirect(route('dashboard.roles.index'))
                        ->assertSessionHas('success','Role successfully updated.');

        // assert name changed
        $this->assertDatabaseHas('roles', [
            'name' => 'newrole',
        ]);

        $editedRole = Role::whereName('newrole')->first();

        //assert permission was changed
        $this->assertEquals($newPermission->id, $editedRole->permissions()->first()->id);

    }

    /** @test */
    public function role_edition_require_name_role()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to edit roles
        $permission = Permission::create(['name' => 'Edit roles']);
        $user->givePermissionTo('Edit roles');

        $role = Role::create(['name' => 'client']);

        $roleData = [
            'name' => null,
            'permissions' => null,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/roles/$role->id", $roleData)
                        ->assertSessionHasErrors(['name','permissions']);

    }

    /** @test */
    public function role_edition_require_unique_name()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to Edit roles
        $permission = Permission::create(['name' => 'Edit roles']);
        $user->givePermissionTo('Edit roles');

        $permission = Permission::create(['name' => 'client']);

        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $roleData = [
            'name' => 'role2',
            'roles' => [$permission->id],
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/roles/$role1->id", $roleData)
                        ->assertSessionHasErrors(['name']);

    }


    /** @test */
    public function an_non_authenticated_user_cannot_delete_a_role()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();

        //cannot delete data
        $response = $this->delete("/dashboard/roles/$role->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_delete_a_role()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $role = factory(Role::class)->create();

        //cannot update data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/roles/$role->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_delete_a_role()
    {
        $user = factory(User::class)->create();

        $role = factory(Role::class)->create();

        //cannot delete data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/roles/$role->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_delete_a_role()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to delete roles
        $permission = Permission::create(['name' => 'Delete roles']);
        $user->givePermissionTo('Delete roles');

        //create a new role
        $roleToDelete = factory(Role::class)->create();

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->delete("/dashboard/roles/$roleToDelete->id")
                        ->assertRedirect(route('dashboard.roles.index'))
                        ->assertSessionHas('success','Role successfully deleted.');

        // assert record didnt exist in database
        $this->assertDatabaseMissing('roles', [
            'name' => $roleToDelete->name,
        ]);

    }
}
