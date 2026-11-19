
@forelse ($pedidos as $pedido)

    <article class="pedido {{ $pedido->status }}">

        <h3>
            <span>
                Pedido #{{ $pedido->id }}
            </span>

            <small>
                {{ $pedido->created_at->format('d/m/Y H:i') }}
            </small>
        </h3>

        <p>
            <strong>Cliente:</strong>
            {{ $pedido->cliente->nome }}

            @if ($pedido->cliente->telefone)
                ({{ $pedido->cliente->telefone }})
            @endif
        </p>

        <div class="pedido-entrega">
            <p>
                <strong>Tipo de pedido:</strong>

                @if ($pedido->tipo === 'entrega')
                    🚚 Entrega: {{ $pedido->endereco }}
                @else
                    🏪 Retirada na loja
                @endif
            </p>
        </div>

        <p>
            <strong>Itens do pedido:</strong>
        </p>

        <ul>
            @foreach ($pedido->itens as $item)
                <li>
                    {{ $item->quantidade }}x {{ $item->nome }}
                </li>
            @endforeach
        </ul>

        @if ($pedido->observacao)
            <p>
                <strong>Observação:</strong>
                {{ $pedido->observacao }}
            </p>
        @endif

        <p>
            <strong>Pagamento:</strong>
            {{ $pedido->rotuloPagamento() }}

            @if ($pedido->troco_para)
                <br>

                Troco para:
                R$ {{ number_format($pedido->troco_para, 2, ',', '.') }}

                <br>

                Devolver:
                R$ {{ number_format($pedido->troco_para - $pedido->total, 2, ',', '.') }}
            @endif
        </p>

        <p>
            <strong>Valor total:</strong>

            <strong>
                R$ {{ number_format($pedido->total, 2, ',', '.') }}
            </strong>

            @if ($pedido->taxa_entrega > 0)
                <br>

                <small>
                    Inclui entrega de
                    R$ {{ number_format($pedido->taxa_entrega, 2, ',', '.') }}
                </small>
            @endif
        </p>

        <p>
            <strong>Status:</strong>

            <span class="pedido-status">
                {{ str_replace('_', ' ', $pedido->status) }}
            </span>
        </p>

        @if ($pedido->nota)
            <p>
                <strong>Avaliação:</strong>

                <span style="color: #e6a000;">
                    {{ str_repeat('★', $pedido->nota) }}{{ str_repeat('☆', 5 - $pedido->nota) }}
                </span>

                @if ($pedido->comentario_avaliacao)
                    <br>
                    {{ $pedido->comentario_avaliacao }}
                @endif
            </p>
        @endif

        <div class="pedido-acoes">

            @if ($proximo = $pedido->proximoStatus())

                <form
                    action="{{ route('pedidos.status', $pedido) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="{{ $proximo[0] }}"
                    >

                    <button type="submit">
                        {{ $proximo[1] }}
                    </button>
                </form>

            @endif

            @if (in_array($pedido->status, ['novo', 'em_preparo']))

                <form
                    action="{{ route('pedidos.status', $pedido) }}"
                    method="POST"
                    onsubmit="return confirm('Cancelar o pedido #{{ $pedido->id }}? O cliente será avisado.')"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="cancelado"
                    >

                    <button type="submit" class="btn-cancelar">
                        Cancelar pedido
                    </button>
                </form>

            @endif

        </div>

    </article>

@empty

    <div class="pedidos-vazios">

        <div class="icone-vazio">📦</div>

        <h3>Nenhum pedido por aqui!</h3>

        <p>
            Quando um cliente realizar um pedido pelo Telegram,
            ele aparecerá nesta tela.
        </p>

    </div>

@endforelse

<div class="paginacao">
    {{ $pedidos->links() }}
</div>
