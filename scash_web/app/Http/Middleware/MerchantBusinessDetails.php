<?php

namespace App\Http\Middleware;

use App\Models\BusinessDetail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantBusinessDetails
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = Auth::user()->id;
        $businessDetails = BusinessDetail::where('user_id', $user_id)->first();
        if(!empty($businessDetails)){
            return $next($request);
        } else {
            return redirect(route('merchant.auth.business-details'));
        }
        return $next($request);

    }
}
