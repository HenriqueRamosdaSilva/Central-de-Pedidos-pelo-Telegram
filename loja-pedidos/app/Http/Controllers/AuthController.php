<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function formulario()
    {
        return view('auth.login');
    }

    public function entrar(Request $request)
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $chave = Str::lower($credenciais['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            $segundos = RateLimiter::availableIn($chave);

            throw ValidationException::withMessages([
                'email' => "Muitas tentativas. Tente de novo em {$segundos} segundos.",
            ]);
        }

        if (! Auth::attempt($credenciais, $request->boolean('lembrar'))) {
            RateLimiter::hit($chave, 60);

            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        RateLimiter::clear($chave);
        $request->session()->regenerate();

        return redirect()->intended(route('pedidos.index'));
    }

    public function sair(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
