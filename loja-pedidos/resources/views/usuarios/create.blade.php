@extends('layouts.app')

@section('titulo', 'Novo usuário')

@section('conteudo')
    <h1>Novo usuário</h1>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @include('usuarios._form')
    </form>
@endsection
