
@extends('layouts.app')

@section('titulo', 'Produtos')

@push('estilos')
<style>
    .produtos-page {
        width: 100%;
        color: #1b2733;
    }

    .produtos-page .pagina-topo {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 26px;
    }

    .produtos-page h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 750;
        color: #172d43;
    }

    .produtos-page .subtitulo {
        margin: 7px 0 0;
        color: #718096;
        font-size: .95rem;
    }

    .produtos-page .btn-novo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 20px;
        background: #087f89;
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        transition: .2s;
        box-shadow: 0 4px 12px rgba(8, 127, 137, .15);
    }

    .produtos-page .btn-novo:hover {
        background: #066b74;
        transform: translateY(-2px);
    }

    .produtos-page .painel-busca {
        background: white;
        border: 1px solid #e3eaf0;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 22px;
        box-shadow: 0 3px 12px rgba(25, 50, 70, .04);
    }

    .produtos-page .painel-busca h2 {
        font-size: 1.05rem;
        margin: 0 0 16px;
        color: #263b50;
    }

    .produtos-page .busca-campo {
        max-width: 520px;
    }

    .produtos-page label {
        display: block;
        font-size: .9rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .produtos-page input[type="text"] {
        width: 100%;
        max-width: none;
        padding: 12px 14px;
        border: 1px solid #d5dee7;
        border-radius: 10px;
        outline: none;
        background: #fbfdff;
        transition: .2s;
    }

    .produtos-page input:focus {
        border-color: #087f89;
        box-shadow: 0 0 0 3px rgba(8, 127, 137, .10);
    }

    .produtos-page .opcoes-busca {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        margin-top: 16px;
    }

    .produtos-page .opcoes-busca label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-weight: 500;
    }

    .produtos-page input[type="checkbox"] {
        accent-color: #087f89;
        width: 17px;
        height: 17px;
    }

    .produtos-page .btn-pesquisar {
        border: 0;
        padding: 11px 19px;
        border-radius: 10px;
        background: #087f89;
        color: white;
        font-weight: 700;
        cursor: pointer;
    }

    .produtos-page .btn-pesquisar:hover {
        background: #066b74;
    }

    .produtos-page .btn-limpar {
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
    }

    .produtos-page .btn-limpar:hover {
        color: #087f89;
    }

    .produtos-page .painel-listagem {
        background: white;
        border: 1px solid #e3eaf0;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 3px 12px rgba(25, 50, 70, .04);
    }

    .produtos-page .listagem-topo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .produtos-page .listagem-topo h2 {
        margin: 0;
        font-size: 1.15rem;
        color: #263b50;
    }

    .produtos-page .quantidade {
        color: #718096;
        font-size: .9rem;
    }

    .produtos-page .tabela-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .produtos-page table {
        width: 100%;
        min-width: 650px;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        background: white;
    }

    .produtos-page th {
        background: #f1f6f9;
        color: #526579;
        font-size: .83rem;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 15px 13px;
        border: 0;
        border-bottom: 1px solid #e3eaf0;
    }

    .produtos-page td {
        padding: 15px 13px;
        border: 0;
        border-bottom: 1px solid #edf1f5;
        color: #334155;
        vertical-align: middle;
    }

    .produtos-page tbody tr:last-child td {
        border-bottom: 0;
    }

    .produtos-page tbody tr:hover td {
        background: #f8fbfc;
    }

    .produtos-page .produto-nome {
        font-weight: 700;
        color: #24394d;
    }

    .produtos-page .preco {
        font-weight: 700;
        color: #087f89;
    }

    .produtos-page .selo {
        display: inline-block;
        margin-left: 5px;
        padding: 4px 9px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
    }

    .produtos-page .selo-baixo {
        background: #fff1ce;
        color: #9a6700;
    }

    .produtos-page .selo-esgotado {
        background: #fde5e3;
        color: #b42318;
    }

    .produtos-page .selo-ativo {
        background: #dcf5e8;
        color: #18794e;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
    }

    .produtos-page .selo-inativo {
        background: #edf0f3;
        color: #64748b;
        padding: 5px 11px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
    }

    .produtos-page .acoes {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .produtos-page .btn-editar {
        display: inline-block;
        padding: 8px 13px;
        border-radius: 9px;
        background: #e5f3fb;
        color: #0877a8;
        text-decoration: none;
        font-weight: 700;
        font-size: .85rem;
    }

    .produtos-page .btn-editar:hover {
        background: #cce9f7;
    }

    .produtos-page .btn-excluir {
        padding: 8px 13px;
        border: 0;
        border-radius: 9px;
        background: #fde9e7;
        color: #bd332a;
        font-weight: 700;
        font-size: .85rem;
        cursor: pointer;
    }

    .produtos-page .btn-excluir:hover {
        background: #fbd4d0;
    }

    .produtos-page .form-excluir {
        display: inline;
        margin: 0;
    }

    .produtos-page .vazio {
        text-align: center;
        padding: 45px 20px;
        color: #718096;
    }

    .produtos-page .vazio-icone {
        font-size: 2.5rem;
        margin-bottom: 12px;
    }

    .produtos-page .vazio strong {
        display: block;
        color: #34495e;
        font-size: 1.1rem;
        margin-bottom: 7px;
    }

    .produtos-page .paginacao {
        margin-top: 20px;
    }

    .produtos-page .paginacao nav {
        margin-top: 0;
    }

    @media (max-width: 600px) {
        .produtos-page h1 {
            font-size: 1.65rem;
        }

        .produtos-page .painel-busca,
        .produtos-page .painel-listagem {
            padding: 15px;
        }

        .produtos-page .opcoes-busca {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="produtos-page">

    <div class="pagina-topo">
        <div>
            <h1>Produtos</h1>
            <p class="subtitulo">Gerencie os produtos e o estoque da sua loja.</p>
        </div>

        <a href="{{ route('produtos.create') }}" class="btn-novo">
            + Novo produto
        </a>
    </div>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <div class="painel-busca">
        <h2>Pesquisar produtos</h2>

        <form action="{{ route('produtos.index') }}" method="GET">

            <div class="busca-campo">
                <label for="busca">Nome do produto</label>

                <input
                    type="text"
                    id="busca"
                    name="busca"
                    value="{{ $termo }}"
                    placeholder="Ex.: burger"
                >
            </div>

            <div class="opcoes-busca">

                <label>
                    <input
                        type="checkbox"
                        name="baixo"
                        value="1"
                        @checked($apenasBaixo)
                    >
                    Só estoque baixo (até {{ $minimo }} unidades) ou esgotado
                </label>

                <button type="submit" class="btn-pesquisar">
                    Pesquisar
                </button>

                <a href="{{ route('produtos.index') }}" class="btn-limpar">
                    Limpar filtros
                </a>

            </div>

        </form>
    </div>

    <div class="painel-listagem">

        <div class="listagem-topo">
            <h2>Lista de produtos</h2>

            <span class="quantidade">
                {{ $produtos->total() }} produto(s) encontrado(s)
            </span>
        </div>

        @if ($produtos->count() > 0)

            <div class="tabela-scroll">

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

                            <tr class="{{ $produto->ativo && $produto->quantidade === 0 ? 'esgotado' : ($produto->ativo && $produto->quantidade <= $minimo ? 'baixo' : '') }}">

                                <td>
                                    <span class="produto-nome">
                                        {{ $produto->nome }}
                                    </span>
                                </td>

                                <td>
                                    <span class="preco">
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    {{ $produto->quantidade }}

                                    @if ($produto->ativo && $produto->quantidade === 0)
                                        <span class="selo selo-esgotado">
                                            Esgotado
                                        </span>
                                    @elseif ($produto->ativo && $produto->quantidade <= $minimo)
                                        <span class="selo selo-baixo">
                                            Estoque baixo
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($produto->ativo)
                                        <span class="selo-ativo">Ativo</span>
                                    @else
                                        <span class="selo-inativo">Inativo</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="acoes">

                                        <a
                                            href="{{ route('produtos.edit', $produto) }}"
                                            class="btn-editar"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('produtos.destroy', $produto) }}"
                                            method="POST"
                                            class="form-excluir"
                                            onsubmit="return confirm('Excluir {{ $produto->nome }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn-excluir">
                                                Excluir
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>
                </table>

            </div>

        @else

            <div class="vazio">
                <div class="vazio-icone">📦</div>

                <strong>Nenhum produto encontrado</strong>

                <p>
                    Cadastre seu primeiro produto para ele aparecer
                    no cardápio do bot.
                </p>

                <a href="{{ route('produtos.create') }}" class="btn-novo">
                    + Cadastrar produto
                </a>
            </div>

        @endif

        <div class="paginacao">
            {{ $produtos->links() }}
        </div>

    </div>

</div>

@endsection
