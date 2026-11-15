@extends('layouts.app')

@section('titulo', 'Novo produto')

@section('conteudo')
    <h1>Novo produto</h1>

    <form action="{{ route('produtos.store') }}" method="POST">
        @include('produtos._form')
    </form>
@endsection
