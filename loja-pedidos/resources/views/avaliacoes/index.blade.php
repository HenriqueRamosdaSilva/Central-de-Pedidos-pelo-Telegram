@extends('layouts.app')

@section('titulo', 'Avaliações')

@push('estilos')
    <style>
        .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin: 18px 0; }
        .kpi { background: #fff; padding: 14px; border-left: 6px solid #0e6b73; border-radius: 4px; }
        .kpi strong { display: block; margin-top: 4px; font-size: 1.4rem; }
        .barra-nota { height: 14px; min-width: 2px; background: #e59a00; border-radius: 3px; }
        .filtros { display: flex; flex-wrap: wrap; gap: 4px 16px; align-items: end; }
        .avaliacao { background: #fff; border-left: 6px solid #8796a3; border-radius: 4px; padding: 10px 16px; margin-top: 14px; }
        .avaliacao.alta { border-color: #1e8e5a; }
        .avaliacao.media { border-color: #e59a00; }
        .avaliacao.baixa { border-color: #c0392b; }
        .avaliacao h3 { margin: 4px 0; color: #b87400; }
        .avaliacao h3 small { color: #566676; }
        .avaliacao .meta { margin: 4px 0; color: #566676; font-size: .9rem; }
    </style>
@endpush

@section('conteudo')
    <h1>Avaliações dos clientes</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <div class="kpis">
        <div class="kpi">
            Nota média
            <strong>{{ $total > 0 ? number_format($media, 1, ',', '.') . ' / 5' : 'Sem avaliações' }}</strong>
        </div>
        <div class="kpi">Avaliações<strong>{{ $total }}</strong></div>
        <div class="kpi">Com comentário<strong>{{ $totalComComentario }}</strong></div>
    </div>

    @if ($total > 0)
        <h2>Distribuição das notas</h2>
        <table>
            <thead>
                <tr>
                    <th>Nota</th>
                    <th>Quantidade</th>
                    <th>%</th>
                    <th style="width:45%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ([5, 4, 3, 2, 1] as $nota)
                    @php $qtd = $distribuicao[$nota] ?? 0; $pct = round($qtd / $total * 100); @endphp
                    <tr>
                        <td>{{ $nota }} {{ str_repeat('★', $nota) }}</td>
                        <td>{{ $qtd }}</td>
                        <td>{{ $pct }}%</td>
                        <td><div class="barra-nota" style="width: {{ $pct }}%"></div></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Avaliações</h2>

    <form action="{{ route('avaliacoes.index') }}" method="GET" class="filtros">
        <div>
            <label for="nota">Nota</label>
            <select id="nota" name="nota">
                <option value="">Todas</option>
                @foreach ([5, 4, 3, 2, 1] as $n)
                    <option value="{{ $n }}" @selected(request('nota') == $n)>{{ $n }} {{ str_repeat('★', $n) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="de">De</label>
            <input type="date" id="de" name="de" value="{{ request('de') }}">
        </div>

        <div>
            <label for="ate">Até</label>
            <input type="date" id="ate" name="ate" value="{{ request('ate') }}">
        </div>

        <div>
            <label>
                <input type="checkbox" name="comentario" value="1" @checked(request()->boolean('comentario'))>
                Só com comentário
            </label>
        </div>

        <div>
            <button type="submit">Filtrar</button>
            <a href="{{ route('avaliacoes.index') }}">Limpar</a>
        </div>
    </form>

    @forelse ($avaliacoes as $p)
        <article class="avaliacao {{ $p->nota >= 4 ? 'alta' : ($p->nota == 3 ? 'media' : 'baixa') }}">
            <h3>
                {{ str_repeat('★', $p->nota) }}{{ str_repeat('☆', 5 - $p->nota) }}
                <small>{{ $p->nota }}/5</small>
            </h3>

            @if ($p->comentario_avaliacao)
                <p>{{ $p->comentario_avaliacao }}</p>
            @else
                <p><em>Sem comentário.</em></p>
            @endif

            <p class="meta">
                Pedido #{{ $p->id }} · {{ $p->cliente->nome }} · {{ $p->created_at->format('d/m/Y H:i') }}
            </p>
            <p class="meta">
                {{ $p->itens->map(fn ($i) => $i->quantidade . 'x ' . $i->nome)->implode(', ') }}
            </p>
        </article>
    @empty
        <p>Nenhuma avaliação encontrada. Elas aparecem aqui depois que os clientes avaliam pedidos entregues.</p>
    @endforelse

    <div class="paginacao">
        {{ $avaliacoes->links() }}
    </div>
@endsection
