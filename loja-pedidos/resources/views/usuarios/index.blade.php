
@extends('layouts.app')

@section('titulo', 'Usuários')

@push('estilos')
<style>
    .usuarios-pagina {
        color: #1b2733;
    }

    .usuarios-cabecalho {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .usuarios-cabecalho h1 {
        margin: 0;
        font-size: 2rem;
        color: #172d42;
    }

    .btn-novo-usuario {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        background: #087e91;
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        border-radius: 12px;
        transition: .2s;
    }

    .btn-novo-usuario:hover {
        background: #066574;
        transform: translateY(-2px);
    }

    .usuarios-card {
        background: #fff;
        border: 1px solid #e1e9ef;
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 5px 18px rgba(25, 55, 75, .05);
    }

    .usuarios-card h2 {
        margin: 0 0 18px;
        font-size: 1.15rem;
        color: #20394e;
    }

    .usuarios-tabela-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 12px;
    }

    .usuarios-tabela {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        margin: 0;
        min-width: 650px;
    }

    .usuarios-tabela th {
        background: #eaf4f8;
        color: #244357;
        font-size: .9rem;
        font-weight: bold;
        padding: 15px;
        border: none;
        border-bottom: 1px solid #dce7ed;
    }

    .usuarios-tabela td {
        padding: 15px;
        border: none;
        border-bottom: 1px solid #edf1f4;
        color: #34495a;
    }

    .usuarios-tabela tbody tr:last-child td {
        border-bottom: none;
    }

    .usuarios-tabela tbody tr:hover {
        background: #f7fbfd;
    }

    .perfil-selo {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: .82rem;
        font-weight: bold;
    }

    .perfil-admin {
        background: #e2f1ff;
        color: #1769aa;
    }

    .perfil-atendente {
        background: #e1f5ec;
        color: #16744c;
    }

    .acoes-usuario {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-editar-usuario,
    .btn-excluir-usuario {
        display: inline-block;
        border: none;
        border-radius: 9px;
        padding: 8px 13px;
        font-size: .88rem;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-editar-usuario {
        background: #e3f2fb;
        color: #087eae;
    }

    .btn-editar-usuario:hover {
        background: #cde9f8;
    }

    .btn-excluir-usuario {
        background: #fde9e7;
        color: #bd4138;
    }

    .btn-excluir-usuario:hover {
        background: #fbd4d0;
    }

    .usuarios-info {
        margin: 18px 0 0;
        padding: 14px 16px;
        background: #eaf5fa;
        color: #31576d;
        border-radius: 12px;
        font-size: .92rem;
    }

    @media (max-width: 600px) {
        .usuarios-cabecalho h1 {
            font-size: 1.6rem;
        }

        .usuarios-card {
            padding: 14px;
        }

        .btn-novo-usuario {
            width: 100%;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="usuarios-pagina">

    <div class="usuarios-cabecalho">
        <h1>Usuários do painel</h1>

        <a href="{{ route('usuarios.create') }}" class="btn-novo-usuario">
            + Novo usuário
        </a>
    </div>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <div class="usuarios-card">

        <h2>Equipe e acessos</h2>

        <div class="usuarios-tabela-wrapper">

            <table class="usuarios-tabela">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>
                                <strong>{{ $usuario->name }}</strong>
                            </td>

                            <td>{{ $usuario->email }}</td>

                            <td>
                                @if ($usuario->perfil === 'admin')
                                    <span class="perfil-selo perfil-admin">
                                        Administrador
                                    </span>
                                @else
                                    <span class="perfil-selo perfil-atendente">
                                        Atendente
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="acoes-usuario">

                                    <a
                                        href="{{ route('usuarios.edit', $usuario) }}"
                                        class="btn-editar-usuario"
                                    >
                                        Editar
                                    </a>

                                    @if ($usuario->id !== auth()->id())
                                        <form
                                            action="{{ route('usuarios.destroy', $usuario) }}"
                                            method="POST"
                                            style="display:inline"
                                            onsubmit="return confirm('Excluir {{ $usuario->name }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn-excluir-usuario"
                                            >
                                                Excluir
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                Nenhum usuário cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>

    <p class="usuarios-info">
        <strong>Informação:</strong>
        Administradores possuem acesso completo ao painel.
        Atendentes podem visualizar e atualizar os pedidos.
    </p>

</div>

@endsection
