@extends('layouts.app')

@section('titulo', 'Produtos')

@section('conteudo')
    <h1>Produtos</h1>

    <p><a href="{{ route('produtos.create') }}">Novo produto</a></p>

    <form action="{{ route('produtos.index') }}" method="GET">
        <label for="busca">Pesquisar pelo nome</label>
        <input type="text" id="busca" name="busca" value="{{ $termo }}" placeholder="Ex.: burger">
        <button type="submit">Pesquisar</button>
        <a href="{{ route('produtos.index') }}">Limpar</a>
    </form>

    @if ($produtos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Situação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produtos as $produto)
                    <tr>
                        <td>{{ $produto->nome }}</td>
                        <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td>{{ $produto->quantidade }}</td>
                        <td>{{ $produto->ativo ? 'Ativo' : 'Inativo' }}</td>
                        <td>
                            <a href="{{ route('produtos.edit', $produto) }}">Editar</a>
                            <form action="{{ route('produtos.destroy', $produto) }}" method="POST" style="display:inline"
                                  onsubmit="return confirm('Excluir {{ $produto->nome }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Nenhum produto encontrado. Cadastre o primeiro para ele aparecer no cardápio do bot.</p>
    @endif

    <div class="paginacao">
        {{ $produtos->links() }}
    </div>
@endsection
