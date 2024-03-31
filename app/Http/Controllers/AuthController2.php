<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Session;
use Auth;
use Str;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;

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

        
        Mail::to($user->email)->send(new VerifyEmail($user));

        Session::flash('success', 'Registrasi Berhasil! Silakan cek email Anda untuk verifikasi.');
        return redirect()->route('login');
    }

    public function verify($token)
    {
        $user = User::where('email_verification_token', $token)->first();
        
        if (!$user) {
            Session::flash('failed', 'Token verifikasi tidak valid.');
            return redirect()->route('login');
        }

        $user->email_verified = true;
        $user->email_verification_token = null;
        $user->save();

        Session::flash('success', 'Email Anda telah berhasil diverifikasi.');
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
            if (Auth::user()->email_verified) {
                Session::flash('success', 'Login Berhasil!');
                
                return redirect()->route('profiles.index');
            } else {
                Auth::logout();
                Session::flash('failed', 'Email Anda belum diverifikasi.');

                return back();
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Session::flash('success', 'Logout Berhasil!');
        return redirect()->route('login');
    }

    public function forgotPasswordForm()
    {
        $title = "Forgot Password";

        return view('auth.forgotpassword', compact('title'));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordForm($token)
    {
        $title = "Reset Password";

        return view('auth.resetpassword', compact('title', 'token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
