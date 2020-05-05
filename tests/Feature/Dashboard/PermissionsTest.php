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

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_non_authenticated_user_cannot_access_to_permissions_list()
    {
        $response = $this->get('/dashboard/permissions');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_access_to_permissions_list()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
                         ->get('/dashboard/permissions');

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_access_to_permissions_list()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
                         ->get('/dashboard/permissions');

        $response->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_access_to_permissions_list()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View permissions
        $permission = Permission::create(['name' => 'View permissions']);
        $user->givePermissionTo('View permissions');

        //create 1 or 2 roles
        $roles = factory(Role::class,rand(1,2))->create();

        // create 5 permissions
        $permissions = factory(Permission::class,5)
                            ->create()
                            ->each(function ($permission) use($roles) {
                                $permission->syncRoles($roles);
                            });

        // assert the allowed user can access to user list
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.permissions.index')
                        ->assertViewHas('permissions');

        //assert see all permissions
        foreach ($permissions as $permission) {
            $response->assertSee($permission->name)
                    ->assertSee($permission->roles->pluck('name')->implode(' | '))
                    ->assertSee($permission->created_at->format('d/m/Y'));
        }
    }

    /** @test */
    /*public function the_list_of_permissions_must_be__ordred_by_creation_date_and_paginated()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to View permissions
        $permission = Permission::create(['name' => 'View permissions']);
        $user->givePermissionTo('View permissions');

        $permissions = [];
        // create 15 permissions
        for ($i=0; $i < 15; $i++) {
            $tempPermission = factory(Permission::class)->create();
            $permissions[] = $tempPermission;
            sleep(1);
        }

        // assert see in order
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions')
                        ->assertSeeInOrder([
                            $permissions[14]->name,
                            $permissions[13]->name
                        ]);;

        // assert see the last user
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions')
                        ->assertSee($permissions[14]->name)
                        ->assertSee($permissions[14]->created_at->format('d/m/Y'));

        // assert dont see the first user
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions')
                        ->assertDontSee($permissions[0]->name);

        // assert see the 15th user on the second page
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions?page=2')
                        ->assertSee($permissions[0]->name)
                        ->assertSee($permissions[0]->created_at->format('d/m/Y'));
    }*/

    /** @test */
    public function an_non_authenticated_user_cannot_create_a_permission()
    {
        //cannot show create form
        $response = $this->get('/dashboard/permissions/create')
                        ->assertRedirect(route('login'));

        //cannot save data
        $response = $this->post('/dashboard/permissions')
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_create_a_permission()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions/create')
                        ->assertRedirect(route('verification.notice'));

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/permissions')
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_create_a_permission()
    {
        $user = factory(User::class)->create();

        //cannot show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions/create')
                        ->assertStatus(403);

        //cannot save data
        $response = $this->actingAs($user)
                        ->post('/dashboard/permissions')
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_create_a_permission()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add permissions
        $permission = Permission::create(['name' => 'Add permissions']);
        $user->givePermissionTo('Add permissions');

        // assert the allowed user can show create form
        $response = $this->actingAs($user)
                        ->get('/dashboard/permissions/create')
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.permissions.create')
                        ->assertViewHas('roles');

        /**
         * assert that the allowed user can save data
         */

        $roleClient = Role::create(['name' => 'client']);
        $roleAdmin = Role::create(['name' => 'admin']);

        //asser one role
        $permissionData = [
            'name' => 'newpermission',
            'roles' => [$roleClient->id],
        ];

        $response = $this->post('/dashboard/permissions', $permissionData)
                        ->assertRedirect(route('dashboard.permissions.index'))
                        ->assertSessionHas('success','Permission successfully added.');

        $this->assertDatabaseHas('permissions', [
            'name' => 'newpermission',
        ]);

        //asser multiple roles
        $permissionData = [
            'name' => 'nicepermission',
            'roles' => [$roleClient->id, $roleAdmin->id],
        ];

        $response = $this->post('/dashboard/permissions', $permissionData)
                        ->assertRedirect(route('dashboard.permissions.index'))
                        ->assertSessionHas('success','Permission successfully added.');

        $this->assertDatabaseHas('permissions', [
            'name' => 'nicepermission',
        ]);

    }

    /** @test */
    public function permission_creation_require_name_roles()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add permissions
        $permission = Permission::create(['name' => 'Add permissions']);
        $user->givePermissionTo('Add permissions');

        $permissionData = [
            'name' => null,
            'roles' => null,
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/permissions', $permissionData)
                        ->assertSessionHasErrors(['name','roles']);

    }


    /** @test */
    public function permission_creation_require_unique_name()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to add permissions
        $permission = Permission::create(['name' => 'Add permissions']);
        $user->givePermissionTo('Add permissions');

        $role = Role::create(['name' => 'client']);
        $uniquePermission = Permission::create(['name' => 'uniquename']);

        $permissionData = [
            'name' => 'uniquename',
            'roles' => [$role->id],
        ];

        $response = $this->actingAs($user)
                        ->post('/dashboard/permissions', $permissionData)
                        ->assertSessionHasErrors(['name']);

    }

    /** @test */
    public function an_non_authenticated_user_cannot_edit_a_permission()
    {
        $user = factory(User::class)->create();

        $permission = factory(Permission::class)->create();

        //cannot show edit form
        $response = $this->get("/dashboard/permissions/$permission->id/edit")
                        ->assertRedirect(route('login'));

        //cannot update data
        $response = $this->put("/dashboard/permissions/$permission->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_edit_a_permission()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $permission = factory(Permission::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/permissions/$permission->id/edit")
                        ->assertRedirect(route('verification.notice'));

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/permissions/$permission->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_edit_a_permission()
    {
        $user = factory(User::class)->create();

        $permission = factory(Permission::class)->create();

        //cannot show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/permissions/$permission->id/edit")
                        ->assertStatus(403);

        //cannot update data
        $response = $this->actingAs($user)
                        ->put("/dashboard/permissions/$permission->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_edit_a_permission()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to edit permissions
        $permission = Permission::create(['name' => 'Edit permissions']);
        $user->givePermissionTo('Edit permissions');

        //create a permission with role client
        $permissionToEdit = Permission::create(['name' => 'permission']);
        $roleClient = Role::create(['name' => 'client']);
        $permissionToEdit->assignRole($roleClient);

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->get("/dashboard/permissions/$permissionToEdit->id/edit")
                        ->assertStatus(200)
                        ->assertViewIs('dashboard.permissions.edit')
                        ->assertViewHas(['roles','permission'])
                        ->assertSee($permissionToEdit->name);

        /**
         * assert that the allowed user can update data
         */

        //create new role admin
        $adminRole = Role::create(['name' => 'admin']);

        $permissionData = [
            'name' => 'newpermission',
            'roles' => [$adminRole->id],
        ];

        $response = $this->put("/dashboard/permissions/$permissionToEdit->id", $permissionData)
                        ->assertRedirect(route('dashboard.permissions.index'))
                        ->assertSessionHas('success','Permission successfully updated.');

        // assert name email changed
        $this->assertDatabaseHas('permissions', [
            'name' => 'newpermission',
        ]);

        $editedPermission = Permission::whereName('newpermission')->first();

        //assert role was changed
        $this->assertEquals($adminRole->id, $editedPermission->roles()->first()->id);

    }

    /** @test */
    public function permission_edition_require_name_role()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to edit permissions
        $permission = Permission::create(['name' => 'Edit permissions']);
        $user->givePermissionTo('Edit permissions');

        $role = Role::create(['name' => 'client']);

        $permissionData = [
            'name' => null,
            'roles' => null,
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/permissions/$permission->id", $permissionData)
                        ->assertSessionHasErrors(['name','roles']);

    }

    /** @test */
    public function permission_edition_require_unique_name()
    {
        //create a user
        $user = factory(User::class)->create();

        // allow user to Edit permissions
        $permission = Permission::create(['name' => 'Edit permissions']);
        $user->givePermissionTo('Edit permissions');

        $role = Role::create(['name' => 'client']);

        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $permissionData = [
            'name' => 'permission2',
            'roles' => [$role->id],
        ];

        $response = $this->actingAs($user)
                        ->put("/dashboard/permissions/$permission1->id", $permissionData)
                        ->assertSessionHasErrors(['name']);

    }


    /** @test */
    public function an_non_authenticated_user_cannot_delete_a_permission()
    {
        $user = factory(User::class)->create();

        $permission = factory(Permission::class)->create();

        //cannot delete data
        $response = $this->delete("/dashboard/permissions/$permission->id")
                        ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_non_verified_email_user_cannot_delete_a_permission()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);

        $permission = factory(Permission::class)->create();

        //cannot update data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/permissions/$permission->id")
                        ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function an_non_allowed_user_cannot_delete_a_permission()
    {
        $user = factory(User::class)->create();

        $permission = factory(Permission::class)->create();

        //cannot delete data
        $response = $this->actingAs($user)
                        ->delete("/dashboard/permissions/$permission->id")
                        ->assertStatus(403);
    }

    /** @test */
    public function an_allowed_user_may_delete_a_permission()
    {

        //create a user
        $user = factory(User::class)->create();

        // allow user to delete permissions
        $permission = Permission::create(['name' => 'Delete permissions']);
        $user->givePermissionTo('Delete permissions');

        //create a new permission
        $permissionToDelete = factory(Permission::class)->create();
        $clientRole = Role::create(['name' => 'client']);
        $permissionToDelete->assignRole($clientRole);

        // assert the allowed user can show edit form
        $response = $this->actingAs($user)
                        ->delete("/dashboard/permissions/$permissionToDelete->id")
                        ->assertRedirect(route('dashboard.permissions.index'))
                        ->assertSessionHas('success','Permission successfully deleted.');

        // assert record didnt exist in database
        $this->assertDatabaseMissing('permissions', [
            'name' => $permissionToDelete->name,
        ]);

    }
}
