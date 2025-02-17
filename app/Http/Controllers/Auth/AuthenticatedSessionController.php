<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     if($request->user()->role === 'admin'){
    //         return redirect()->route('admin.dashboard');
    //     } elseif($request->user()->role === 'staff'){
    //         return redirect()->route('receptionist.dashboard');
    //     }   

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            // Get the logged in user
            $user = User::where('email', $request->email)->first();

            if ($user && $user->patient_id) {
                // Store the patient ID in session
                session(['patient_id' => $user->patient_id]);
            }

            // Redirect to the intended route
            if($request->user()->role === 'admin'){
                return redirect()->route('admin.dashboard');
            } elseif($request->user()->role === 'staff'){
                return redirect()->route('receptionist.dashboard');
            }   else {
                return redirect()->intended(RouteServiceProvider::HOME);

            }
    
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
