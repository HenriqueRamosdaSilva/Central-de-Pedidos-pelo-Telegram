@csrf

@if ($errors->any())
    <ul class="erros">
        @foreach ($errors->all() as $erro)
            <li>{{ $erro }}</li>
        @endforeach
    </ul>
@endif

<label for="nome">Nome</label>
<input type="text" id="nome" name="nome" value="{{ old('nome', $produto->nome ?? '') }}">

<label for="descricao">Descrição</label>
<textarea id="descricao" name="descricao" rows="3">{{ old('descricao', $produto->descricao ?? '') }}</textarea>

<label for="preco">Preço</label>
<input type="number" id="preco" name="preco" step="0.01" min="0" value="{{ old('preco', $produto->preco ?? '') }}">

<label for="quantidade">Quantidade em estoque</label>
<input type="number" id="quantidade" name="quantidade" min="0" value="{{ old('quantidade', $produto->quantidade ?? '') }}">

<label for="ativo">Aparece no cardápio?</label>
<select id="ativo" name="ativo">
    <option value="1" @selected((bool) old('ativo', $produto->ativo ?? true))>Sim</option>
    <option value="0" @selected(! old('ativo', $produto->ativo ?? true))>Não</option>
</select>

<p>
    <button type="submit">Salvar</button>
    <a href="{{ route('produtos.index') }}">Voltar</a>
</p>
