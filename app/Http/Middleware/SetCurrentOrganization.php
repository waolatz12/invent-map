<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $organizationId = $request->header(
            'X-Company-ID'
        );

        if (! $organizationId) {
            return response()->json([
                'message' => 'X-Company-ID header is required.',
            ], 400);
        }

        $organization = $user->companies()
            ->where('companies.id', $organizationId)
            ->wherePivot('status', 'active')
            ->first();

        if (! $organization) {
            return response()->json([
                'message' => 'You do not belong to this company.',
            ], 403);
        }

        app()->instance(
            'currentOrganization',
            $organization
        );

        setPermissionsTeamId(
            $organization->id
        );

        $user
            ->unsetRelation('roles')
            ->unsetRelation('permissions');

        return $next($request);
    }
}
