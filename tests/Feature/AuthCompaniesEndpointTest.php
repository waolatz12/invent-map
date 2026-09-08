<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthCompaniesEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_active_companies_with_roles_and_permissions_for_each_company(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();

        $company = Company::create([
            'name' => 'Acme',
            'slug' => 'acme',
            'status' => 'active',
        ]);

        $company->users()->attach($user->id, [
            'status' => 'active',
            'joined_at' => now(),
        ]);

        setPermissionsTeamId($company->id);
        $user->assignRole('Owner');

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/v1/auth/companies');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $company->id)
            ->assertJsonPath('data.0.roles.0', 'Owner')
            ->assertJsonFragment(['products.view']);
    }
}
