
@extends('layouts.app')

@section('titulo', 'Novo usuário')

@push('estilos')
<style>
    .pagina-form-usuario h1 {
        color: #172d42;
        font-size: 2rem;
        margin: 0 0 10px;
    }

    .pagina-form-usuario .descricao {
        color: #718292;
        margin: 0 0 24px;
    }
</style>
@endpush

@section('conteudo')

<div class="pagina-form-usuario">

    <h1>Novo usuário</h1>

    <p class="descricao">
        Cadastre um novo integrante para acessar o painel da loja.
    </p>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @include('usuarios._form')
    </form>

</div>

@endsection
