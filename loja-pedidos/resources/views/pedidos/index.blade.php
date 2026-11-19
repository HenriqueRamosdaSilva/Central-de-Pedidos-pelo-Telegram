@extends('layouts.app')

@section('titulo', 'Pedidos')

@section('conteudo')
    <h1>Pedidos</h1>

    <form action="{{ route('pedidos.index') }}" method="GET">
        <label for="status">Mostrar</label>
        <select id="status" name="status" onchange="this.form.submit()">
            @foreach (['abertos' => 'Em aberto', 'novo' => 'Novos', 'em_preparo' => 'Em preparo', 'pronto' => 'Prontos', 'entregue' => 'Entregues', 'cancelado' => 'Cancelados', 'todos' => 'Todos'] as $valor => $rotulo)
                <option value="{{ $valor }}" @selected($status === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
        <noscript><button type="submit">Filtrar</button></noscript>
    </form>

    <div id="lista">
        @include('pedidos._estado_loja')
        @include('pedidos._lista')
    </div>
@endsection

@push('scripts')
    <script>
        // A cada 8 segundos busca a página de novo e troca só a lista de pedidos.
        setInterval(async () => {
            const resposta = await fetch(location.href);
            if (!resposta.ok) return;
            const doc = new DOMParser().parseFromString(await resposta.text(), 'text/html');
            const nova = doc.getElementById('lista');
            if (nova) document.getElementById('lista').innerHTML = nova.innerHTML;
        }, 8000);
    </script>
@endpush

@push('estilos')
    <style>
        .estado-loja { display: flex; flex-wrap: wrap; gap: 8px 16px; align-items: center; justify-content: space-between; padding: 10px 14px; margin: 12px 0; border-radius: 6px; }
        .estado-loja.aberta { background: #d9f2e4; }
        .estado-loja.fora { background: #e3eaf0; }
        .estado-loja.pausada { background: #fde4e1; }
        .estado-loja form { margin: 0; }
        .alerta-estoque { background: #fff3cd; padding: 10px 14px; border-radius: 6px; }
    </style>
@endpush
