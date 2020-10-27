<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

class IsContentCreator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::find($request->id);
        
        if($user->is_document_verified == NO || $user->is_email_verified == NO) {

            $response = ['success' => false, 'error' => api_error(156), 'error_code' => 156];

            return response()->json($response, 200);
        }

        return $next($request);
    }
}
