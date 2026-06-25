<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->isAdmin() ? '/admin/dashboard' : '/user/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status === 'inactive') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
            }

            return redirect()->intended(
                $user->isAdmin() ? route('admin.dashboard') : route('user.dashboard')
            )->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'      => ['required', 'string', 'max:20'],
            'gender'     => ['required', 'in:Laki-laki,Perempuan'],
            'nik'        => ['nullable', 'string', 'size:16'],
            'dob'        => ['nullable', 'date', 'before:today'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'address'    => ['nullable', 'string'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'agree_terms'=> ['required', 'accepted'],
        ]);

        // Handle upload avatar ke Supabase saat registrasi
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Menyimpan file otomatis ke folder 'avatars' pada bucket Supabase
            $avatarPath = Storage::disk('supabase')->put('avatars', $request->file('avatar'));
        }

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'gender'     => $validated['gender'],
            'nik'        => $validated['nik'] ?? null,
            'dob'        => $validated['dob'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address'    => $validated['address'] ?? null,
            'password'   => Hash::make($validated['password']),
            'role'       => 'user',
            'avatar'     => $avatarPath, // Menyimpan full path (cth: avatars/nama_file.jpg)
        ]);

        // Otomatis buat schema folder penampung jika diatur private/public di Supabase
        Auth::login($user);
        return redirect()->route('user.dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function showProfile()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'gender'     => ['required', 'in:Laki-laki,Perempuan'],
            'nik'        => ['nullable', 'string', 'size:16'],
            'dob'        => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'address'    => ['nullable', 'string'],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama dari Supabase jika ada sebelum upload yang baru
            if ($user->avatar) {
                Storage::disk('supabase')->delete($user->avatar);
            }

            // Upload file baru ke folder 'avatars' di Supabase
            $avatarPath = Storage::disk('supabase')->put('avatars', $request->file('avatar'));
            $validated['avatar'] = $avatarPath;
        }

        $user->update($validated);
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah!');
    }
}
