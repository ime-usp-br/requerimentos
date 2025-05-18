<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                $user = $request->user();
                
                if (!$user) {
                    return null;
                }
                
                return [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'codpes' => $user->codpes,
                        'current_role_id' => $user->current_role_id,
                        'current_department_id' => $user->current_department_id,
                        'currentRole' => $user->currentRole,
                        'currentDepartment' => $user->currentDepartment,
                        'roles' => $user->roles,
                    ],
                ];
            },
        ]);
    }
}
