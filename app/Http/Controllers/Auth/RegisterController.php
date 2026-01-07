<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new \Illuminate\Auth\Events\Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    protected function validator(array $data)
    {
        // Flash the individual fields for name and address
        session()->flash('first_name', $this->extractFirstName($data['name'] ?? ''));
        session()->flash('middle_name', $this->extractMiddleName($data['name'] ?? ''));
        session()->flash('last_name', $this->extractLastName($data['name'] ?? ''));
        session()->flash('street_address', $this->extractStreetAddress($data['address'] ?? ''));
        session()->flash('barangay', $this->extractBarangay($data['address'] ?? ''));

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['required', 'string', 'max:500'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Please enter your full name',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
            'phone.required' => 'Phone number is required',
            'phone.regex' => 'Please enter a valid Philippine mobile number (e.g., 09123456789 or +639123456789)',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters long',
            'password.confirmed' => 'Password confirmation does not match',
            'address.required' => 'Please provide your complete address',
            'address.max' => 'Address is too long',
            'terms.accepted' => 'You must agree to the terms and conditions to register',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Helper methods to extract name parts
    private function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    private function extractMiddleName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) === 3) {
            return $parts[1];
        }
        return '';
    }

    private function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) >= 2) {
            return end($parts);
        }
        return '';
    }

    // Helper methods to extract address parts
    private function extractStreetAddress($fullAddress)
    {
        // Extract street before first comma
        $parts = explode(',', $fullAddress);
        return trim($parts[0] ?? '');
    }

    private function extractBarangay($fullAddress)
    {
        // Extract barangay (between first and second comma)
        $parts = explode(',', $fullAddress);
        if (isset($parts[1])) {
            // Remove "Barangay " prefix if exists
            $barangay = trim($parts[1]);
            $barangay = str_replace('Barangay ', '', $barangay);
            return $barangay;
        }
        return '';
    }
}