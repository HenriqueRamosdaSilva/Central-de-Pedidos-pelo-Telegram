
@push('estilos')
<style>
    .produto-form {
        width: 100%;
    }

    .produto-form .campo {
        margin-bottom: 21px;
    }

    .produto-form label {
        display: block;
        margin-bottom: 8px;
        color: #34495e;
        font-size: .92rem;
        font-weight: 700;
    }

    .produto-form input,
    .produto-form textarea,
    .produto-form select {
        width: 100%;
        max-width: none;
        padding: 13px 15px;
        border: 1px solid #d5dee7;
        border-radius: 11px;
        background: #fbfdff;
        color: #263b50;
        font-family: inherit;
        font-size: .95rem;
        outline: none;
        transition: .2s;
    }

    .produto-form input:focus,
    .produto-form textarea:focus,
    .produto-form select:focus {
        border-color: #087f89;
        box-shadow: 0 0 0 3px rgba(8, 127, 137, .10);
        background: #fff;
    }

    .produto-form textarea {
        resize: vertical;
        min-height: 110px;
    }

    .produto-form .ajuda {
        display: block;
        margin-top: 7px;
        color: #8795a4;
        font-size: .82rem;
    }

    .produto-form .acoes-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #edf1f5;
    }

    .produto-form .btn-salvar {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 12px 24px;
        border: 0;
        border-radius: 11px;
        background: #087f89;
        color: white;
        font-size: .95rem;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    .produto-form .btn-salvar:hover {
        background: #066b74;
        transform: translateY(-1px);
    }

    .produto-form .btn-voltar {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 12px 20px;
        border-radius: 11px;
        background: #edf2f6;
        color: #526579;
        text-decoration: none;
        font-weight: 700;
        transition: .2s;
    }

    .produto-form .btn-voltar:hover {
        background: #dfe7ed;
    }
</style>
@endpush

<div class="produto-form">

    @csrf

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <div class="campo">
        <label for="nome">Nome do produto</label>

        <input
            type="text"
            id="nome"
            name="nome"
            value="{{ old('nome', $produto->nome ?? '') }}"
            placeholder="Ex.: X-Burger"
        >
    </div>

    <div class="campo">
        <label for="descricao">Descrição</label>

        <textarea
            id="descricao"
            name="descricao"
            rows="3"
            placeholder="Descreva os detalhes do produto..."
        >{{ old('descricao', $produto->descricao ?? '') }}</textarea>

        <span class="ajuda">
            Uma descrição ajuda a identificar melhor o produto no cardápio.
        </span>
    </div>

    <div class="campo">
        <label for="preco">Preço (R$)</label>

        <input
            type="number"
            id="preco"
            name="preco"
            step="0.01"
            min="0"
            value="{{ old('preco', $produto->preco ?? '') }}"
            placeholder="Ex.: 25.90"
        >
    </div>

    <div class="campo">
        <label for="quantidade">Quantidade em estoque</label>

        <input
            type="number"
            id="quantidade"
            name="quantidade"
            min="0"
            value="{{ old('quantidade', $produto->quantidade ?? '') }}"
            placeholder="Ex.: 50"
        >

        <span class="ajuda">
            Informe quantas unidades estão disponíveis.
        </span>
    </div>

    <div class="campo">
        <label for="ativo">Aparece no cardápio?</label>

        <select id="ativo" name="ativo">
            <option
                value="1"
                @selected((bool) old('ativo', $produto->ativo ?? true))
            >
                Sim, produto ativo
            </option>

            <option
                value="0"
                @selected(! (bool) old('ativo', $produto->ativo ?? true))
            >
                Não, produto inativo
            </option>
        </select>
    </div>

    <div class="acoes-form">

        <button type="submit" class="btn-salvar">
            Salvar produto
        </button>

        <a href="{{ route('produtos.index') }}" class="btn-voltar">
            Voltar
        </a>

    </div>

</div>
