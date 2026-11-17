@extends('layouts.app')

@section('titulo', 'Editar usuário')

@section('conteudo')
    <h1>Editar usuário</h1>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
        @method('PUT')
        @include('usuarios._form')
    </form>
@endsection
