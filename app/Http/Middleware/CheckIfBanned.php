<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is banned
            if ($user->is_banned) {
                // Check if it's a temporary restriction that has expired
                if ($user->banned_until && Carbon::now()->greaterThan($user->banned_until)) {
                    // Restriction has expired, unban the user
                    $user->update([
                        'is_banned' => false,
                        'banned_until' => null,
                        'ban_reason' => null,
                    ]);
                    
                    // Allow access since ban expired
                    return $next($request);
                }

                // User is still banned/restricted
                Auth::logout();
                
                $message = $user->banned_until 
                    ? "Your account has been restricted until " . Carbon::parse($user->banned_until)->format('M d, Y H:i') . ". Reason: " . $user->ban_reason
                    : "Your account has been permanently banned. Reason: " . $user->ban_reason;
                
                return redirect()->route('login')->withErrors([
                    'email' => $message
                ]);
            }
        }

        return $next($request);
    }
}