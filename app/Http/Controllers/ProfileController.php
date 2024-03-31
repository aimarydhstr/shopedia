<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Session;

class ProfileController extends Controller
{
    public function index()
    {
        $title = 'Profile';
        $auth = Auth::user();
        
        return view('profiles.index', compact('auth', 'title'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,' . auth()->user()->id,
            'username' => 'required|string|unique:users,username,' . auth()->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = auth()->user();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->has('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('images/users'), $imageName);

            if ($user->image && file_exists(public_path('images/' . $user->image))) {
                unlink(public_path('images/users/' . $user->image));
            }

            $userData['image'] = $imageName;
        }

        $user->update($userData);

        Session::flash('success', 'Profile Berhasil Diperbaharui!');
        return redirect()->back();
    }

}
