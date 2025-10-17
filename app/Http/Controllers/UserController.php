<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }


    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,buyer,reseller,supplier',
            'password' => 'nullable|min:8', // only validate if present
        ]);

        // Update basic fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Only update password if it was entered
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    
// public function approveReseller($id)
// {
//     $user = User::findOrFail($id);

//     if ($user->latestResellerApplication) {
//         $user->latestResellerApplication->update(['status' => 'approved']);
//         $user->update(['role' => 'reseller']);
//     }

//     return back()->with('success', 'Reseller application approved.');
// }

// public function rejectReseller(Request $request, $id)
// {
//     $request->validate([
//         'rejection_reason' => 'required|string|max:1000',
//     ]);

//     $user = User::findOrFail($id);

//     if ($user->latestResellerApplication) {
//         $user->latestResellerApplication->update([
//             'status' => 'rejected',
//             'rejection_reason' => $request->rejection_reason,
//         ]);
//     }

//     return back()->with('error', 'Reseller application rejected with reason.');
// }


}
