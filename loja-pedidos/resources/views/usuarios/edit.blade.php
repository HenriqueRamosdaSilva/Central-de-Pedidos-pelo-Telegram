
@extends('layouts.app')

@section('titulo', 'Editar usuário')

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

    .usuario-atual {
        display: inline-block;
        background: #e5f3f9;
        color: #087e91;
        border-radius: 10px;
        padding: 8px 13px;
        margin-bottom: 20px;
        font-size: .9rem;
        font-weight: 600;
    }
</style>
@endpush

@section('conteudo')

<div class="pagina-form-usuario">

    <h1>Editar usuário</h1>

    <p class="descricao">
        Atualize as informações e as permissões de acesso deste integrante.
    </p>

    <div class="usuario-atual">
        Editando: {{ $usuario->name }}
    </div>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">

        @method('PUT')

        @include('usuarios._form')

    </form>

</div>

@endsection
