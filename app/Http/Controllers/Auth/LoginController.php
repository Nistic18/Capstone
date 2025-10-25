<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected function authenticated($request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('users.index');
        }
        if ($user->role === 'supplier') {
            return redirect()->route('supplier.dashboard');
        }
    }
public function login(Request $request)
{
    $this->validateLogin($request);

    $email = Str::lower($request->input($this->username()));
    $ip = $request->ip();

    // Get user record (we'll only lock if this exists)
    $user = \App\Models\User::where($this->username(), $email)->first();

    // Use RateLimiter key only if user exists
    $key = $user ? $user->id . '|' . $ip : null;

    $shortLockLimit = 3;  // 3 attempts = 30-second lock
    $longLockLimit  = 6;  // 6 total = 1-hour lock

    // ✅ If user exists, check for locks
    if ($user && RateLimiter::tooManyAttempts($key . ':hour', 1)) {
        $seconds = RateLimiter::availableIn($key . ':hour');
        throw ValidationException::withMessages([
            'password' => ['Your account is locked for 1 hour. Please try again after ' . gmdate('i:s', $seconds) . '.'],
        ]);
    }

    if ($user && RateLimiter::tooManyAttempts($key, $shortLockLimit)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'password' => ['Too many failed attempts. Please wait ' . gmdate('i:s', $seconds) . ' before trying again.'],
        ]);
    }

    // ✅ Attempt login (Laravel's built-in)
    if ($this->attemptLogin($request)) {
        // Clear rate limits on success
        if ($key) {
            RateLimiter::clear($key);
            RateLimiter::clear($key . ':hour');
            cache()->forget('login_attempts:' . $key);
        }
        return $this->sendLoginResponse($request);
    }

    // ✅ Wrong email (no lock, just message)
    if (!$user) {
        throw ValidationException::withMessages([
            $this->username() => ['This email is not registered.'],
        ]);
    }

    // ✅ Wrong password (count + lock)
    if (!Hash::check($request->input('password'), $user->password)) {
        RateLimiter::hit($key, 30); // 30-sec window
        $attemptCount = cache()->get('login_attempts:' . $key, 0) + 1;
        cache()->put('login_attempts:' . $key, $attemptCount, now()->addHour());

        if ($attemptCount >= $longLockLimit) {
            RateLimiter::hit($key . ':hour', 3600); // 1-hour lock
            RateLimiter::clear($key);
            cache()->forget('login_attempts:' . $key);
            throw ValidationException::withMessages([
                'password' => ['Your account has been locked for 1 hour due to multiple failed password attempts.'],
            ]);
        }

        throw ValidationException::withMessages([
            'password' => ['Incorrect password. (Attempt ' . $attemptCount . ' of ' . $longLockLimit . ')'],
        ]);
    }

    return $this->sendFailedLoginResponse($request);
}
}

