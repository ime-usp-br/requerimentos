<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class CheckCurrentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roleNames)
    {   
        $user = Auth::user();
        foreach($roleNames as $roleName) {
            $role = Role::where('name', $roleName)->first();
            // dd($roleName);
            if ($user->current_role_id === $role->id) {
                return $next($request);
            }
        }
        
        abort(403, 'Você não está autorizado a acessar essa página.');
        // talvez seja melhor deixar como 404 pra não vazar info do backend
        // abort(404);
    }
}