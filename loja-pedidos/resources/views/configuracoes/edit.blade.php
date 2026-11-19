@extends('layouts.app')

@section('titulo', 'Configurações')

@section('conteudo')
    <h1>Configurações da loja</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('configuracoes.update') }}" method="POST">
        @csrf
        @method('PUT')

        <h2>Entrega e pedido</h2>

        <label for="taxa_entrega">Taxa de entrega (R$). Use 0 para entrega grátis.</label>
        <input type="number" id="taxa_entrega" name="taxa_entrega" step="0.01" min="0"
               value="{{ old('taxa_entrega', $config->taxa_entrega) }}">

        <label for="pedido_minimo">Pedido mínimo (R$), sem contar a entrega. Use 0 para não ter mínimo.</label>
        <input type="number" id="pedido_minimo" name="pedido_minimo" step="0.01" min="0"
               value="{{ old('pedido_minimo', $config->pedido_minimo) }}">

        <h2>Estoque</h2>

        <label for="estoque_minimo">Avisar quando um produto ficar com este número de unidades ou menos.</label>
        <input type="number" id="estoque_minimo" name="estoque_minimo" min="0"
               value="{{ old('estoque_minimo', $config->estoque_minimo) }}">

        <h2>Pix (copia e cola)</h2>
        <p>
            Quando o cliente escolhe Pix, o bot envia um código "copia e cola" com o valor do pedido.
            Preencha os três campos; se a chave ficar vazia, nenhum código é enviado.
        </p>

        <label for="chave_pix">Chave Pix (CPF, CNPJ, e-mail, celular com +55 ou chave aleatória)</label>
        <input type="text" id="chave_pix" name="chave_pix" value="{{ old('chave_pix', $config->chave_pix) }}">

        <label for="pix_nome">Nome do recebedor (máximo 25 caracteres)</label>
        <input type="text" id="pix_nome" name="pix_nome" maxlength="25" value="{{ old('pix_nome', $config->pix_nome) }}">

        <label for="pix_cidade">Cidade do recebedor (máximo 15 caracteres)</label>
        <input type="text" id="pix_cidade" name="pix_cidade" maxlength="15" value="{{ old('pix_cidade', $config->pix_cidade) }}">

        <h2>Horário de funcionamento</h2>
        <p>Fora desses horários o bot não finaliza pedidos. O fechamento precisa ser depois da abertura no mesmo dia.</p>

        <table>
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Aberto</th>
                    <th>Abre</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordem as $dia)
                    @php $h = $horarios[$dia]; @endphp
                    <tr>
                        <td>{{ $dias[$dia] }}</td>
                        <td>
                            <input type="checkbox" name="horarios[{{ $dia }}][aberto]" value="1"
                                   @checked($errors->any() ? ! empty(old("horarios.$dia.aberto")) : $h->aberto)>
                        </td>
                        <td>
                            <input type="time" name="horarios[{{ $dia }}][abre]"
                                   value="{{ old("horarios.$dia.abre", substr($h->abre, 0, 5)) }}">
                        </td>
                        <td>
                            <input type="time" name="horarios[{{ $dia }}][fecha]"
                                   value="{{ old("horarios.$dia.fecha", substr($h->fecha, 0, 5)) }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><button type="submit">Salvar configurações</button></p>
    </form>

    <h2>Avisos para o vendedor no Telegram</h2>
    <p>
        Para receber os pedidos novos no Telegram, abra o bot (ou adicione o bot a um grupo da equipe) e envie:
        <strong>/vendedor {{ $config->codigo_vendedor }}</strong>
    </p>
    <p>
        Quem recebe os avisos também pode aceitar, concluir ou cancelar pedidos pelos botões da mensagem.
        Para parar de receber, envie /sairvendedor no mesmo chat.
    </p>

    <form action="{{ route('configuracoes.codigo') }}" method="POST"
          onsubmit="return confirm('Gerar um novo código? O código atual deixa de funcionar (chats já vinculados continuam recebendo).')">
        @csrf
        <button type="submit">Gerar novo código</button>
    </form>

    <h3>Chats recebendo os avisos</h3>

    @forelse ($vendedores as $vendedor)
        @if ($loop->first)
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
        @endif
                    <tr>
                        <td>{{ $vendedor->nome }}</td>
                        <td>{{ $vendedor->tipo === 'private' ? 'Conversa privada' : 'Grupo' }}</td>
                        <td>
                            <form action="{{ route('configuracoes.vendedores.remover', $vendedor) }}" method="POST"
                                  onsubmit="return confirm('Remover {{ $vendedor->nome }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Remover</button>
                            </form>
                        </td>
                    </tr>
        @if ($loop->last)
                </tbody>
            </table>
        @endif
    @empty
        <p>Nenhum chat vinculado ainda.</p>
    @endforelse
@endsection
