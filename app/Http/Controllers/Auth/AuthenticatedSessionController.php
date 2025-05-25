<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\OneTimePasswords\Models\OneTimePassword;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login-otp');
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();

        $user->sendOneTimePassword();

        return redirect()->route('enter-otp');
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'otp' => 'required|numeric',
        ]);

        $otp = OneTimePassword::query()
            ->where('password', $data['otp'])
            ->where('authenticatable_type', 'App\Models\User')
            ->first();

        if (! $otp) {
            return redirect()->back()->withErrors(['otp' => 'OTP not found.']);
        }

        $user = User::find($otp->authenticatable_id);
        $result = $user->attemptLoginUsingOneTimePassword($otp->password, remember: false);

        if ($result->isOk()) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return redirect()->back()->withErrors(['otp' => 'Error logging in.']);
    }

    public function enterOtp()
    {
        return Inertia::render('auth/enter-otp');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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
