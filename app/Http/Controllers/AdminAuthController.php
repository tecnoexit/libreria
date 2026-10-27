<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin() { return session('admin_authenticated') ? redirect()->route('admin.dashboard') : view('admin.login'); }

    public function login(Request $request)
    {
        $data = $request->validate(['username' => 'required|string', 'password' => 'required|string']);
        $user = (string) env('ADMIN_USER', 'admin'); $password = (string) env('ADMIN_PASSWORD');
        if (hash_equals($user, $data['username']) && $password !== '' && hash_equals($password, $data['password'])) {
            $request->session()->regenerate(); $request->session()->put('admin_authenticated', true);
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['username' => 'Usuario o contraseña incorrectos.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_authenticated'); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
