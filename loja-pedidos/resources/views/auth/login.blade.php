
@extends('layouts.app')

@section('titulo', 'Entrar')

@section('conteudo')

<style>
    .login-page {
        width: 100%;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 20px;
        background: #ffffff;
        font-family: Arial, sans-serif;
    }

    .login-card {
        width: 100%;
        max-width: 430px;
        padding: 40px;
        background: #ffffff;
        border: 1px solid #e8edf2;
        border-radius: 20px;
        box-shadow: 0 8px 35px rgba(0, 0, 0, 0.07);
        text-align: center;
    }

    .login-logo {
        display: block;
        width: 140px;
        height: 140px;
        object-fit: contain;
        margin: 0 auto 15px;
    }

    .login-card h1 {
        color: #171717;
        font-size: 27px;
        font-weight: 700;
        margin: 0 0 10px;
    }

    .login-description {
        color: #777777;
        font-size: 14px;
        margin: 0 0 30px;
    }

    .login-input {
        display: block;
        width: 100%;
        max-width: none;
        height: 52px;
        padding: 0 16px;
        margin-bottom: 18px;
        border: 1px solid #dce2e8;
        border-radius: 11px;
        background: #ffffff;
        color: #222222;
        font-size: 15px;
        outline: none;
        transition: all 0.3s ease;
    }

    .login-input:focus {
        border-color: #299fdb;
        box-shadow: 0 0 0 3px rgba(41, 159, 219, 0.13);
    }

    .login-input::placeholder {
        color: #999999;
    }

    .login-options {
        display: flex;
        align-items: center;
        margin: 5px 0 25px;
        text-align: left;
    }

    .login-options label {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0;
        color: #555555;
        font-size: 14px;
        cursor: pointer;
    }

    .login-options input {
        width: 16px;
        height: 16px;
        padding: 0;
        accent-color: #299fdb;
        cursor: pointer;
    }

    .login-button {
        display: block;
        width: 100%;
        height: 52px;
        padding: 0;
        border: none;
        border-radius: 11px;
        background: #299fdb;
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .login-button:hover {
        background: #1687c0;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(41, 159, 219, 0.25);
    }

    .login-errors {
        padding: 12px 15px;
        margin: 0 0 20px;
        border: 1px solid #f3caca;
        border-radius: 10px;
        background: #fff0f0;
        color: #c62828;
        text-align: left;
        font-size: 13px;
    }

    .login-errors li {
        margin-left: 15px;
    }

    @media (max-width: 480px) {
        .login-card {
            padding: 30px 22px;
        }

        .login-card h1 {
            font-size: 24px;
        }

        .login-logo {
            width: 120px;
            height: 120px;
        }
    }
</style>

<div class="login-page">

    <div class="login-card">

        <img
            src="{{ asset('logo_do_projeto.png') }}"
            alt="Logo do projeto"
            class="login-logo"
        >

        <h1>Entrar no painel</h1>

        <p class="login-description">
            Acesse sua conta para gerenciar sua loja.
        </p>

        @if ($errors->any())
            <ul class="login-errors">
                @foreach ($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('login.entrar') }}" method="POST">
            @csrf

            <input
                type="email"
                id="email"
                name="email"
                class="login-input"
                placeholder="Digite seu e-mail"
                value="{{ old('email') }}"
                aria-label="E-mail"
                required
                autofocus
            >

            <input
                type="password"
                id="password"
                name="password"
                class="login-input"
                placeholder="Digite sua senha"
                aria-label="Senha"
                required
            >

            <div class="login-options">
                <label>
                    <input
                        type="checkbox"
                        name="lembrar"
                        value="1"
                    >
                    Manter conectado
                </label>
            </div>

            <button type="submit" class="login-button">
                Entrar
            </button>

        </form>

    </div>

</div>

@endsection
