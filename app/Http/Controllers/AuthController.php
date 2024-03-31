<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Session;
use Auth;
use Str;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $title = "Registrasi Akun";
        $auth = Auth::user();

        return view('auths.register', compact('title', 'auth'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_token' => $token,
            'image' => $request->image,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'User',
            'is_active' => 1,
        ]);

        if(!$user){
            Session::flash('failed', 'Registrasi Gagal!');
            return redirect()->back();
        }

        Session::flash('success', 'Registrasi Berhasil!');
        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        $title = "Login Akun";
        $auth = Auth::user();

        return view('auths.login', compact('title', 'auth'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = $request->only('username', 'password');

        if (!Auth::attempt($data)) {
            Session::flash('failed', 'Username atau Password salah!');
            return redirect()->back();
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout(); 
            Session::flash('failed', 'Akun Anda tidak aktif. Harap hubungi administrator.');
            return redirect()->back();
        }
    
        
        if ($user->role == 'Admin') {
            Session::flash('success', 'Login Berhasil!');

            return redirect()->route('dashboard');
        } else {
            Session::flash('success', 'Login Berhasil!');
            
            return redirect()->route('profiles.index');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::flash('success', 'Logout Berhasil!');
        return redirect()->route('login');
    }

}