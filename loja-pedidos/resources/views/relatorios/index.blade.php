
@extends('layouts.app')

@section('titulo', 'Relatório de vendas')

@push('estilos')
<style>
    /* ==============================
       RELATÓRIOS - ESTILO GERAL
    ============================== */

    .relatorio-page {
        width: 100%;
        color: #1b2733;
    }

    .relatorio-page h1 {
        font-size: 29px;
        font-weight: 750;
        margin: 0 0 8px;
        color: #17212b;
    }

    .relatorio-subtitulo {
        color: #788695;
        font-size: 14px;
        margin: 0 0 25px;
    }

    .relatorio-page h2 {
        font-size: 19px;
        font-weight: 700;
        margin: 0 0 18px;
        color: #202d3b;
    }

    /* ==============================
       ATALHOS DE PERÍODO
    ============================== */

    .relatorio-atalhos {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 22px;
    }

    .relatorio-atalhos a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 17px;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #dfe7ee;
        color: #425365;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .relatorio-atalhos a:hover {
        background: #eaf6fc;
        border-color: #299fdb;
        color: #1687c0;
        transform: translateY(-1px);
    }

    /* ==============================
       FILTROS
    ============================== */

    .relatorio-filtros {
        background: #ffffff;
        border: 1px solid #e6edf2;
        border-radius: 15px;
        padding: 22px;
        margin-bottom: 20px;
        box-shadow: 0 3px 12px rgba(24, 48, 70, .035);
    }

    .relatorio-filtros h2 {
        margin-bottom: 16px;
    }

    .relatorio-form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 15px;
    }

    .relatorio-campo {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .relatorio-campo label {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #526171;
    }

    .relatorio-campo input {
        width: 210px;
        max-width: 100%;
        height: 44px;
        padding: 0 12px;
        border: 1px solid #dce4eb;
        border-radius: 9px;
        background: #fbfcfd;
        color: #273747;
        outline: none;
        transition: all .2s ease;
    }

    .relatorio-campo input:focus {
        border-color: #299fdb;
        box-shadow: 0 0 0 3px rgba(41, 159, 219, .12);
    }

    .relatorio-btn-filtrar {
        height: 44px;
        padding: 0 25px;
        border: none;
        border-radius: 9px;
        background: #168fca;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s ease;
    }

    .relatorio-btn-filtrar:hover {
        background: #087bb5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(22, 143, 202, .2);
    }

    /* ==============================
       INFORMAÇÃO DO RELATÓRIO
    ============================== */

    .relatorio-info {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 20px 0;
        padding: 13px 16px;
        border-radius: 10px;
        background: #eaf6fc;
        color: #32617d;
        font-size: 13px;
    }

    /* ==============================
       INDICADORES / CARDS
    ============================== */

    .relatorio-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin: 20px 0 30px;
    }

    .relatorio-kpi {
        position: relative;
        overflow: hidden;
        min-height: 115px;
        padding: 20px;
        background: #ffffff;
        border: 1px solid #e8edf2;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(24, 48, 70, .035);
        transition: all .2s ease;
    }

    .relatorio-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(24, 48, 70, .08);
    }

    .relatorio-kpi::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #168fca;
    }

    .relatorio-kpi .kpi-titulo {
        display: block;
        margin-bottom: 10px;
        color: #758393;
        font-size: 13px;
        font-weight: 600;
    }

    .relatorio-kpi strong {
        display: block;
        color: #172b40;
        font-size: 24px;
        font-weight: 750;
    }

    .relatorio-kpi .kpi-detalhe {
        display: block;
        margin-top: 6px;
        color: #8a97a5;
        font-size: 12px;
    }

    /* ==============================
       SEÇÕES DE DADOS
    ============================== */

    .relatorio-secao {
        margin-top: 28px;
    }

    .relatorio-tabela-card {
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #e6edf2;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(24, 48, 70, .035);
    }

    .relatorio-tabela-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .relatorio-page table {
        width: 100%;
        min-width: 430px;
        margin: 0;
        border-collapse: collapse;
        background: #ffffff;
    }

    .relatorio-page th {
        padding: 15px 18px;
        background: #f5f8fb;
        color: #536477;
        border: none;
        border-bottom: 1px solid #e7edf2;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .relatorio-page td {
        padding: 15px 18px;
        border: none;
        border-bottom: 1px solid #edf1f5;
        color: #465565;
        font-size: 14px;
    }

    .relatorio-page tbody tr:last-child td {
        border-bottom: none;
    }

    .relatorio-page tbody tr:hover {
        background: #f8fbfd;
    }

    .relatorio-page td strong {
        color: #203246;
    }

    /* ==============================
       BARRA DE FATURAMENTO
    ============================== */

    .barra-dia-container {
        width: 100%;
        height: 11px;
        overflow: hidden;
        background: #eaf0f5;
        border-radius: 20px;
    }

    .barra-dia {
        height: 100%;
        min-width: 2px;
        background: linear-gradient(90deg, #168fca, #55b9e8);
        border-radius: 20px;
        transition: width .3s ease;
    }

    /* ==============================
       GRADE DE INFORMAÇÕES
    ============================== */

    .relatorio-grade {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-top: 22px;
    }

    .relatorio-grade-coluna {
        min-width: 0;
    }

    .relatorio-grade-coluna h2 {
        margin-bottom: 15px;
    }

    /* ==============================
       ESTADO VAZIO
    ============================== */

    .relatorio-vazio {
        padding: 35px 20px;
        margin-top: 15px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e6edf2;
        border-radius: 14px;
        color: #778595;
        font-size: 14px;
    }

    .relatorio-vazio strong {
        display: block;
        margin-bottom: 7px;
        color: #34475a;
        font-size: 16px;
    }

    /* ==============================
       ERROS
    ============================== */

    .relatorio-page .erros {
        padding: 14px 18px 14px 35px;
        margin-bottom: 20px;
        border-radius: 10px;
    }

    /* ==============================
       RESPONSIVIDADE
    ============================== */

    @media (max-width: 900px) {
        .relatorio-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .relatorio-grade {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .relatorio-page h1 {
            font-size: 24px;
        }

        .relatorio-filtros {
            padding: 17px;
        }

        .relatorio-form {
            flex-direction: column;
            align-items: stretch;
        }

        .relatorio-campo,
        .relatorio-campo input,
        .relatorio-btn-filtrar {
            width: 100%;
        }

        .relatorio-kpis {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .relatorio-kpi {
            min-height: 100px;
        }

        .relatorio-atalhos {
            gap: 7px;
        }

        .relatorio-atalhos a {
            padding: 9px 12px;
            font-size: 12px;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="relatorio-page">

    <!-- CABEÇALHO -->
    <h1>Relatório de vendas</h1>

    <p class="relatorio-subtitulo">
        Acompanhe o desempenho e os resultados da sua loja.
    </p>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <!-- ATALHOS DE PERÍODO -->
    <div class="relatorio-atalhos">

        @foreach ($atalhos as $rotulo => $datas)
            <a href="{{ route('relatorios.index', $datas) }}">
                {{ $rotulo }}
            </a>
        @endforeach

    </div>

    <!-- FILTROS DE DATA -->
    <div class="relatorio-filtros">

        <h2>Filtrar período</h2>

        <form
            action="{{ route('relatorios.index') }}"
            method="GET"
            class="relatorio-form"
        >

            <div class="relatorio-campo">
                <label for="de">Data inicial</label>

                <input
                    type="date"
                    id="de"
                    name="de"
                    value="{{ $de->toDateString() }}"
                >
            </div>

            <div class="relatorio-campo">
                <label for="ate">Data final</label>

                <input
                    type="date"
                    id="ate"
                    name="ate"
                    value="{{ $ate->toDateString() }}"
                >
            </div>

            <button type="submit" class="relatorio-btn-filtrar">
                Filtrar relatório
            </button>

        </form>

    </div>

    <!-- INFORMAÇÃO -->
    <div class="relatorio-info">
        <span>ℹ️</span>
        <span>
            Os dados consideram os pedidos do período que não foram cancelados.
        </span>
    </div>

    <!-- INDICADORES -->
    <div class="relatorio-kpis">

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Faturamento total</span>

            <strong>
                R$ {{ number_format($faturamento, 2, ',', '.') }}
            </strong>
        </div>

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Total de pedidos</span>

            <strong>{{ $totalPedidos }}</strong>
        </div>

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Ticket médio</span>

            <strong>
                R$ {{ number_format($ticketMedio, 2, ',', '.') }}
            </strong>
        </div>

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Taxas de entrega</span>

            <strong>
                R$ {{ number_format($taxasDeEntrega, 2, ',', '.') }}
            </strong>
        </div>

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Pedidos cancelados</span>

            <strong>{{ $cancelados }}</strong>
        </div>

        <div class="relatorio-kpi">
            <span class="kpi-titulo">Avaliação média</span>

            <strong>
                {{ $avaliacao && $avaliacao->media
                    ? number_format($avaliacao->media, 1, ',', '.') . ' / 5'
                    : 'Sem avaliações' }}
            </strong>

            @if ($avaliacao && $avaliacao->total > 0)
                <span class="kpi-detalhe">
                    {{ $avaliacao->total }} avaliação(ões)
                </span>
            @endif
        </div>

    </div>

    <!-- RESULTADOS -->
    @if ($totalPedidos === 0)

        <div class="relatorio-vazio">
            <strong>Nenhuma venda encontrada</strong>

            Não existem vendas neste período.
            Experimente selecionar outro intervalo de datas.
        </div>

    @else

        @php
            $maiorDia = (float) $porDia->max('faturamento');
        @endphp

        <!-- VENDAS POR DIA -->
        <section class="relatorio-secao">

            <h2>Vendas por dia</h2>

            <div class="relatorio-tabela-card">

                <div class="relatorio-tabela-scroll">

                    <table>

                        <thead>
                            <tr>
                                <th>Dia</th>
                                <th>Pedidos</th>
                                <th>Faturamento</th>
                                <th style="width: 30%">Desempenho</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($porDia as $d)

                                <tr>

                                    <td>
                                        {{ \Illuminate\Support\Carbon::parse($d->dia)->format('d/m/Y') }}
                                    </td>

                                    <td>{{ $d->pedidos }}</td>

                                    <td>
                                        <strong>
                                            R$ {{ number_format($d->faturamento, 2, ',', '.') }}
                                        </strong>
                                    </td>

                                    <td>

                                        <div class="barra-dia-container">

                                            <div
                                                class="barra-dia"
                                                style="width: {{ $maiorDia > 0 ? round($d->faturamento / $maiorDia * 100) : 0 }}%"
                                            ></div>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

        <!-- OUTRAS INFORMAÇÕES -->
        <div class="relatorio-grade">

            <!-- PRODUTOS MAIS VENDIDOS -->
            <section class="relatorio-grade-coluna">

                <h2>Produtos mais vendidos</h2>

                <div class="relatorio-tabela-card">

                    <div class="relatorio-tabela-scroll">

                        <table>

                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Faturamento</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($maisVendidos as $item)

                                    <tr>

                                        <td>{{ $item->nome }}</td>

                                        <td>{{ $item->quantidade }}</td>

                                        <td>
                                            <strong>
                                                R$ {{ number_format($item->faturamento, 2, ',', '.') }}
                                            </strong>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

            <!-- FORMAS DE PAGAMENTO E ENTREGA -->
            <section class="relatorio-grade-coluna">

                <h2>Formas de pagamento</h2>

                <div class="relatorio-tabela-card">

                    <div class="relatorio-tabela-scroll">

                        <table>

                            <thead>
                                <tr>
                                    <th>Forma</th>
                                    <th>Pedidos</th>
                                    <th>Faturamento</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($porPagamento as $p)

                                    <tr>

                                        <td>
                                            {{ \App\Models\Pedido::PAGAMENTOS[$p->forma_pagamento] ?? 'Não informado' }}
                                        </td>

                                        <td>{{ $p->pedidos }}</td>

                                        <td>
                                            <strong>
                                                R$ {{ number_format($p->faturamento, 2, ',', '.') }}
                                            </strong>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                <h2 style="margin-top: 25px;">
                    Entrega x retirada
                </h2>

                <div class="relatorio-tabela-card">

                    <div class="relatorio-tabela-scroll">

                        <table>

                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Pedidos</th>
                                    <th>Faturamento</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($porTipo as $t)

                                    <tr>

                                        <td>
                                            {{ $t->tipo === 'entrega' ? 'Entrega' : 'Retirada' }}
                                        </td>

                                        <td>{{ $t->pedidos }}</td>

                                        <td>
                                            <strong>
                                                R$ {{ number_format($t->faturamento, 2, ',', '.') }}
                                            </strong>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

        </div>

    @endif

</div>

@endsection
