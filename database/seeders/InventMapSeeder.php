<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Organizations
        |--------------------------------------------------------------------------
        */

        $acme = Company::updateOrCreate(
            [
                'slug' => 'acme-electronics',
            ],
            [
                'name' => 'Acme Electronics',
                'status' => 'active',
            ]
        );

        $retail = Company::updateOrCreate(
            [
                'slug' => 'global-retail',
            ],
            [
                'name' => 'Global Retail Store',
                'status' => 'active',
            ]
        );
           /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $john = User::updateOrCreate(
            [
                'email' => 'john@inventmap.test',
            ],
            [
                'name' => 'John Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $sarah = User::updateOrCreate(
            [
                'email' => 'sarah@inventmap.test',
            ],
            [
                'name' => 'Sarah Inventory',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $mike = User::updateOrCreate(
            [
                'email' => 'mike@inventmap.test',
            ],
            [
                'name' => 'Mike Warehouse',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Organization Membership
        |--------------------------------------------------------------------------
        */

        $acme->users()->syncWithoutDetaching([
            $john->id => [
                'status' => 'active',
                'joined_at' => now(),
            ],
            $sarah->id => [
                'status' => 'active',
                'joined_at' => now(),
            ],
            $mike->id => [
                'status' => 'active',
                'joined_at' => now(),
            ],
        ]);

        $retail->users()->syncWithoutDetaching([
            $john->id => [
                'status' => 'active',
                'joined_at' => now(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Organization-specific roles
        |--------------------------------------------------------------------------
        */

        /*
         * Acme
         */
        setPermissionsTeamId($acme->id);

        $john->assignRole('Admin');

        $sarah->assignRole('Inventory Manager');

        $mike->assignRole('Warehouse Staff');

        /*
         * Global Retail
         */
        setPermissionsTeamId($retail->id);

        $john->assignRole('Viewer');

        /*
        |--------------------------------------------------------------------------
        | Reset permission context
        |--------------------------------------------------------------------------
        */

        setPermissionsTeamId(null);
    }
}
