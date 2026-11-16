@forelse ($pedidos as $pedido)
    <article class="pedido {{ $pedido->status }}">
        <h3>
            Pedido #{{ $pedido->id }}
            <small>{{ $pedido->created_at->format('d/m H:i') }}</small>
        </h3>

        <p>
            {{ $pedido->cliente->nome }}{{ $pedido->cliente->telefone ? ' (' . $pedido->cliente->telefone . ')' : '' }} —
            {{ $pedido->tipo === 'entrega' ? 'Entrega: ' . $pedido->endereco : 'Retirada na loja' }}
        </p>

        <ul>
            @foreach ($pedido->itens as $item)
                <li>{{ $item->quantidade }}x {{ $item->nome }}</li>
            @endforeach
        </ul>

        <p>
            <strong>R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
            · Status: {{ str_replace('_', ' ', $pedido->status) }}
        </p>

        @if ($proximo = $pedido->proximoStatus())
            <form action="{{ route('pedidos.status', $pedido) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="{{ $proximo[0] }}">
                <button type="submit">{{ $proximo[1] }}</button>
            </form>
        @endif

        @if (in_array($pedido->status, ['novo', 'em_preparo']))
            <form action="{{ route('pedidos.status', $pedido) }}" method="POST"
                  onsubmit="return confirm('Cancelar o pedido #{{ $pedido->id }}? O cliente será avisado.')">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelado">
                <button type="submit">Cancelar pedido</button>
            </form>
        @endif
    </article>
@empty
    <p>Nenhum pedido por aqui. Quando um cliente pedir pelo Telegram, ele aparece nesta tela.</p>
@endforelse

<div class="paginacao">
    {{ $pedidos->links() }}
</div>
