<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * create permissions
         *
         * */

        //Dashboard
        Permission::updateOrCreate(['name' => 'View dashboard']);

        //Users
        Permission::updateOrCreate(['name' => 'View users']);
        Permission::updateOrCreate(['name' => 'Add users']);
        Permission::updateOrCreate(['name' => 'Edit users']);
        Permission::updateOrCreate(['name' => 'Delete users']);
        Permission::updateOrCreate(['name' => 'View users profile']);
        Permission::updateOrCreate(['name' => 'Edit users profile']);


        //Permissions
        Permission::updateOrCreate(['name' => 'View permissions']);
        Permission::updateOrCreate(['name' => 'Add permissions']);
        Permission::updateOrCreate(['name' => 'Edit permissions']);
        Permission::updateOrCreate(['name' => 'Delete permissions']);

        //Roles
        Permission::updateOrCreate(['name' => 'View roles']);
        Permission::updateOrCreate(['name' => 'Add roles']);
        Permission::updateOrCreate(['name' => 'Edit roles']);
        Permission::updateOrCreate(['name' => 'Delete roles']);

        //Profile
        Permission::updateOrCreate(['name' => 'View profile']);
        Permission::updateOrCreate(['name' => 'Edit profile']);

        //Password
        Permission::updateOrCreate(['name' => 'View password']);
        Permission::updateOrCreate(['name' => 'Edit password']);

        //Email
        Permission::updateOrCreate(['name' => 'View email']);
        Permission::updateOrCreate(['name' => 'Edit email']);


        /**
         * create roles and assign existing permissions
         *
         * */

        //Admin
        $admin = Role::updateOrCreate(['name' => 'admin']);

        $admin->givePermissionTo('View dashboard');

        $admin->givePermissionTo('View users');
        $admin->givePermissionTo('Add users');
        $admin->givePermissionTo('Edit users');
        $admin->givePermissionTo('Delete users');
        $admin->givePermissionTo('View users profile');
        $admin->givePermissionTo('Edit users profile');

        $admin->givePermissionTo('View permissions');
        $admin->givePermissionTo('Add permissions');
        $admin->givePermissionTo('Edit permissions');
        $admin->givePermissionTo('Delete permissions');

        $admin->givePermissionTo('View roles');
        $admin->givePermissionTo('Add roles');
        $admin->givePermissionTo('Edit roles');
        $admin->givePermissionTo('Delete roles');

        $admin->givePermissionTo('View profile');
        $admin->givePermissionTo('Edit profile');

        $admin->givePermissionTo('View password');
        $admin->givePermissionTo('Edit password');

        $admin->givePermissionTo('View email');
        $admin->givePermissionTo('Edit email');

        //Client
        $client = Role::updateOrCreate(['name' => 'client']);
        $client->givePermissionTo('View dashboard');


        /**
         * gets all permissions via Gate::before rule; see AuthServiceProvider
         *
         * */
        $super_admin = Role::updateOrCreate(['name' => 'super-admin']);

        /**
         * create demo users
         *
         */

        //client
        $user = Factory(App\User::class)->create([
            'name' => 'client',
            'email' => 'client@laravel-azzara-dashboard.com',
        ]);
        $user->assignRole($client);

        //admin
        $user = Factory(App\User::class)->create([
            'name' => 'admin',
            'email' => 'admin@laravel-azzara-dashboard.com',
        ]);
        $user->assignRole($admin);

        //super admin
        $user = Factory(App\User::class)->create([
            'name' => 'sudo',
            'email' => 'sudo@laravel-azzara-dashboard.com',
        ]);
        $user->assignRole($super_admin);
    }
}
