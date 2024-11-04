<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RequisitionsPeriod;

class CheckRequisitionsPeriod
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentStatus = RequisitionsPeriod::latest('id')->first();

        // Testa se o período de requisição está aberto
        if ($currentStatus->is_enabled == 1) {
            return $next($request); 
        }

        abort(404);
        return $next($request);
    }
}
