<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RequisitionsPeriod;
use App\Enums\RoleId;

class CheckRequisitionsPeriod
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  $type
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $type = null)
    {
        $currentStatus = RequisitionsPeriod::latest('id')->first();
        if ($request->user() && $request->user()->current_role_id == RoleId::SG) {
            return $next($request);
        }

        if ($type === 'creation' && $currentStatus && $currentStatus->is_creation_enabled) {
            return $next($request);
        }
        if ($type === 'edition' && $currentStatus && $currentStatus->is_update_enabled) {
            return $next($request);
        }
        if ($type === 'creation') {
            abort(403, 'Criação de requerimentos não está aberta no momento');
        } elseif ($type === 'edition') {
            abort(403, 'Edição de requerimentos não está aberta no momento');
        } else {
            abort(404);
        }
    }
}
