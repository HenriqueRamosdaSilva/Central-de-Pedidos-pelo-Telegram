@extends('layouts.app')

@section('titulo', 'Usuários')

@section('conteudo')
    <h1>Usuários do painel</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <p><a href="{{ route('usuarios.create') }}">Novo usuário</a></p>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Perfil</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->perfil === 'admin' ? 'Administrador' : 'Atendente' }}</td>
                    <td>
                        <a href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
                        @if ($usuario->id !== auth()->id())
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Excluir {{ $usuario->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Excluir</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Administrador acessa tudo. Atendente só vê e atualiza os pedidos.</p>
@endsection
