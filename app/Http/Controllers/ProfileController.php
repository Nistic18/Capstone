<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:11',
    ]);
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->phone = $request->input('phone');
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Profile successfully updated!');
    }

    public function changepassword()
    {
        return view('profile.changepassword', ['user' => Auth::user()]);
    }

    public function password(Request $request)
    {
        {
            $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
    
            $user = Auth::user();
    
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Previous Password Incorrect!']);
            }

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();
    
            return back()->with('status', 'Password successfully changed');
        }
    }
public function show($id)
{
    $user = User::with('products.reviews.user')->findOrFail($id);
    return view('profile.show', compact('user'));
}
public function myprofile()
{
    $user = auth()->user();

    // For newsfeed
    $newsfeedPosts = \App\Models\Post::latest()->take(10)->get();

    return view('profile.myprofile', compact('user', 'newsfeedPosts'));
}

}