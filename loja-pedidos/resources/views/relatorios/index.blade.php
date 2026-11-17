@extends('layouts.app')

@section('titulo', 'Relatório de vendas')

@push('estilos')
    <style>
        .atalhos a { margin-right: 14px; }
        .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 12px; margin: 18px 0; }
        .kpi { background: #fff; padding: 14px; border-left: 6px solid #0e6b73; border-radius: 4px; }
        .kpi strong { display: block; margin-top: 4px; font-size: 1.4rem; }
        .grade { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 18px; }
        .barra-dia { height: 14px; min-width: 2px; background: #0e6b73; border-radius: 3px; }
    </style>
@endpush

@section('conteudo')
    <h1>Relatório de vendas</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <p class="atalhos">
        @foreach ($atalhos as $rotulo => $datas)
            <a href="{{ route('relatorios.index', $datas) }}">{{ $rotulo }}</a>
        @endforeach
    </p>

    <form action="{{ route('relatorios.index') }}" method="GET">
        <label for="de">De</label>
        <input type="date" id="de" name="de" value="{{ $de->toDateString() }}">

        <label for="ate">Até</label>
        <input type="date" id="ate" name="ate" value="{{ $ate->toDateString() }}">

        <p><button type="submit">Filtrar</button></p>
    </form>

    <p>Vendas = pedidos do período que não foram cancelados.</p>

    <div class="kpis">
        <div class="kpi">Faturamento<strong>R$ {{ number_format($faturamento, 2, ',', '.') }}</strong></div>
        <div class="kpi">Pedidos<strong>{{ $totalPedidos }}</strong></div>
        <div class="kpi">Ticket médio<strong>R$ {{ number_format($ticketMedio, 2, ',', '.') }}</strong></div>
        <div class="kpi">Taxas de entrega<strong>R$ {{ number_format($taxasDeEntrega, 2, ',', '.') }}</strong></div>
        <div class="kpi">Cancelados<strong>{{ $cancelados }}</strong></div>
        <div class="kpi">
            Avaliação média
            <strong>
                {{ $avaliacao && $avaliacao->media ? number_format($avaliacao->media, 1, ',', '.') . ' / 5' : 'Sem avaliações' }}
            </strong>
            @if ($avaliacao && $avaliacao->total > 0)
                {{ $avaliacao->total }} avaliação(ões)
            @endif
        </div>
    </div>

    @if ($totalPedidos === 0)
        <p>Nenhuma venda neste período.</p>
    @else
        @php $maiorDia = (float) $porDia->max('faturamento'); @endphp

        <h2>Vendas por dia</h2>
        <table>
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Pedidos</th>
                    <th>Faturamento</th>
                    <th style="width:40%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($porDia as $d)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($d->dia)->format('d/m/Y') }}</td>
                        <td>{{ $d->pedidos }}</td>
                        <td>R$ {{ number_format($d->faturamento, 2, ',', '.') }}</td>
                        <td>
                            <div class="barra-dia" style="width: {{ $maiorDia > 0 ? round($d->faturamento / $maiorDia * 100) : 0 }}%"></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grade">
            <div>
                <h2>Produtos mais vendidos</h2>
                <table>
                    <thead>
                        <tr><th>Produto</th><th>Qtd.</th><th>Faturamento</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($maisVendidos as $item)
                            <tr>
                                <td>{{ $item->nome }}</td>
                                <td>{{ $item->quantidade }}</td>
                                <td>R$ {{ number_format($item->faturamento, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <h2>Formas de pagamento</h2>
                <table>
                    <thead>
                        <tr><th>Forma</th><th>Pedidos</th><th>Faturamento</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($porPagamento as $p)
                            <tr>
                                <td>{{ \App\Models\Pedido::PAGAMENTOS[$p->forma_pagamento] ?? 'Não informado' }}</td>
                                <td>{{ $p->pedidos }}</td>
                                <td>R$ {{ number_format($p->faturamento, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h2>Entrega x retirada</h2>
                <table>
                    <thead>
                        <tr><th>Tipo</th><th>Pedidos</th><th>Faturamento</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($porTipo as $t)
                            <tr>
                                <td>{{ $t->tipo === 'entrega' ? 'Entrega' : 'Retirada' }}</td>
                                <td>{{ $t->pedidos }}</td>
                                <td>R$ {{ number_format($t->faturamento, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
