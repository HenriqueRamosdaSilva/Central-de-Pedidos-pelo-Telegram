
@extends('layouts.app')

@section('titulo', 'Novo produto')

@push('estilos')
<style>
    .produto-pagina {
        width: 100%;
    }

    .produto-pagina .cabecalho-pagina {
        margin-bottom: 24px;
    }

    .produto-pagina h1 {
        margin: 0;
        color: #172d43;
        font-size: 2rem;
        font-weight: 750;
    }

    .produto-pagina .descricao-pagina {
        margin-top: 8px;
        color: #718096;
    }

    .produto-pagina .cartao-formulario {
        max-width: 760px;
        background: white;
        padding: 30px;
        border: 1px solid #e3eaf0;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(25, 50, 70, .05);
    }

    .produto-pagina .cartao-formulario h2 {
        margin: 0 0 8px;
        color: #263b50;
        font-size: 1.2rem;
    }

    .produto-pagina .cartao-formulario .orientacao {
        margin: 0 0 25px;
        color: #8795a4;
        font-size: .9rem;
    }

    @media (max-width: 600px) {
        .produto-pagina h1 {
            font-size: 1.65rem;
        }

        .produto-pagina .cartao-formulario {
            padding: 20px;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="produto-pagina">

    <div class="cabecalho-pagina">
        <h1>Novo produto</h1>

        <p class="descricao-pagina">
            Cadastre um novo produto para disponibilizá-lo no cardápio da loja.
        </p>
    </div>

    <div class="cartao-formulario">

        <h2>Informações do produto</h2>

        <p class="orientacao">
            Preencha os campos abaixo para cadastrar seu produto.
        </p>

        <form action="{{ route('produtos.store') }}" method="POST">
            @include('produtos._form')
        </form>

    </div>

</div>

@endsection
