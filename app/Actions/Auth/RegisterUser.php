<?php

namespace App\Actions\Auth;
use App\DTOs\Auth\RegisterUserData;
use App\Models\Company;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterUser
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(
        RegisterUserData $data
    ): User {
        return DB::transaction(function () use ($data) {

            $organization = Company::create([
                'name' => $data->organizationName,
                'slug' => $data->organizationSlug,
                'status' => 'active',
            ]);

            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $organization->users()->attach($user->id, [
                'status' => 'active',
                'joined_at' => now(),
            ]);

            setPermissionsTeamId($organization->id);

            $user->assignRole('Owner');

            return $user;
        });
    }
}
