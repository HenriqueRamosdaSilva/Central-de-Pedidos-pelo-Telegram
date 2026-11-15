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
