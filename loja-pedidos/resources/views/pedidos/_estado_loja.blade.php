
<div class="estado-loja {{ $config->loja_pausada ? 'pausada' : ($dentroDoHorario ? 'aberta' : 'fora') }}">

    <span>
        @if ($config->loja_pausada)
            ⏸️ Loja pausada: o bot não está aceitando pedidos.
        @elseif ($dentroDoHorario)
            🟢 Loja aberta: o bot está aceitando pedidos.
        @else
            🌙 Fora do horário de funcionamento: o bot não está aceitando pedidos.
        @endif
    </span>

    <form
        action="{{ route('loja.pausa') }}"
        method="POST"
        @unless ($config->loja_pausada)
            onsubmit="return confirm('Pausar a loja? O bot vai parar de aceitar pedidos agora.')"
        @endunless
    >
        @csrf

        <button type="submit">
            {{ $config->loja_pausada ? 'Reabrir loja' : 'Pausar loja' }}
        </button>
    </form>

</div>

@if ($estoqueBaixo > 0)

    <div class="alerta-estoque">
        <span>⚠️</span>

        <span>
            <strong>{{ $estoqueBaixo }} produto(s)</strong>
            com estoque baixo ou esgotado.
        </span>

        @if (auth()->user()->perfil === 'admin')
            <a href="{{ route('produtos.index', ['baixo' => 1]) }}">
                Ver produtos →
            </a>
        @endif
    </div>

@endif
