<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Auth\RegisterUser;
use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterUserData;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthenticationService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    //
    public function __construct(
        private readonly AuthenticationService $authenticationService
    ) {}

    public function register(RegisterRequest $request, RegisterUser $registerUser): JsonResponse
    {

        $data = new RegisterUserData(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            organizationName: $request
                ->string('organization_name')
                ->toString(),
            organizationSlug: $request
                ->string('organization_slug')
                ->toString(),
        );

        $user = $registerUser->execute($data);

        return response()->json([
            'message' => 'Registration successful.',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }

    public function login(
        LoginRequest $request
    ): JsonResponse {
        $data = new LoginData(
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
        );

        $result = $this->authenticationService->login(
            $data->email,
            $data->password
        );

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'user' => $result['user'],
                'token' => $result['token'],
            ],
        ]);
    }

    public function logout(
        Request $request
    ): JsonResponse {
        $this->authenticationService->logout(
            $request->user()
        );

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(
        Request $request
    ): JsonResponse {
        return response()->json([
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    public function companies(Request $request): JsonResponse
    {
        $user = $request->user();

        $companies = $user->companies()
            ->wherePivot('status', 'active')
            ->get()
            ->map(function ($company) use ($user) {
                setPermissionsTeamId($company->id);

                $roleModels = $user->roles()
                    ->with('permissions')
                    ->get()
                    ->filter(function ($role) use ($company) {
                        return (int) ($role->pivot->company_id ?? 0) === (int) $company->id;
                    });

                $company->roles = $roleModels
                    ->pluck('name')
                    ->values()
                    ->all();

                $company->permissions = $roleModels
                    ->flatMap(fn($role) => $role->permissions->pluck('name'))
                    ->unique()
                    ->values()
                    ->all();

                return $company->makeHidden('pivot');
            });

        $user->unsetRelation('roles')->unsetRelation('permissions');

        return response()->json([
            'status' => true,
            'message' => 'Companies fetched successfully!',
            'data' => $companies,
        ], 200);
    }
}
