<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderLoginController extends Controller
{
    // SE HA ELIMINADO EL MÉTODO __construct() DE AQUÍ

    public function showLoginForm()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('auth.rider-login', ['pageConfigs' => $pageConfigs]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'dni' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::guard('rider')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('rider.dashboard'));
        }

        return back()->withErrors([
            'dni' => 'Las credenciales proporcionadas no coinciden.',
        ])->onlyInput('dni');
    }

    public function logout(Request $request)
    {
        Auth::guard('rider')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
