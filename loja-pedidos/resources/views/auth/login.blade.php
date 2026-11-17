@extends('layouts.app')

@section('titulo', 'Entrar')

@section('conteudo')
    <h1>Entrar no painel</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('login.entrar') }}" method="POST">
        @csrf

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <label>
            <input type="checkbox" name="lembrar" value="1"> Manter conectado
        </label>

        <p><button type="submit">Entrar</button></p>
    </form>
@endsection
