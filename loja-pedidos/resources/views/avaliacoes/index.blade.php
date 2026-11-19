
@extends('layouts.app')

@section('titulo', 'Avaliações')

@push('estilos')
<style>
    /* ===== IDENTIDADE VISUAL ===== */

    .avaliacoes-page {
        width: 100%;
        max-width: 1240px;
        margin: 0 auto;
        color: #203247;
    }

    .avaliacoes-page * {
        box-sizing: border-box;
    }

    .avaliacoes-page h1 {
        font-size: 30px;
        font-weight: 750;
        color: #172f46;
        margin: 0 0 8px;
        letter-spacing: -0.7px;
    }

    .avaliacoes-page h2 {
        font-size: 21px;
        font-weight: 700;
        color: #20384e;
        margin: 30px 0 18px;
    }

    .avaliacoes-subtitulo {
        color: #718096;
        font-size: 14px;
        margin: 0 0 26px;
    }

    /* ===== CABEÇALHO ===== */

    .avaliacoes-header {
        margin-bottom: 26px;
    }

    /* ===== INDICADORES ===== */

    .avaliacoes-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin: 22px 0 32px;
    }

    .avaliacoes-kpi {
        background: #ffffff;
        border: 1px solid #e5edf3;
        border-radius: 16px;
        padding: 23px;
        box-shadow: 0 4px 15px rgba(28, 55, 80, 0.045);
        transition: transform .2s ease, box-shadow .2s ease;
        position: relative;
        overflow: hidden;
    }

    .avaliacoes-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(28, 55, 80, 0.09);
    }

    .avaliacoes-kpi::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: #159fc9;
    }

    .avaliacoes-kpi:nth-child(2)::before {
        background: #087b82;
    }

    .avaliacoes-kpi:nth-child(3)::before {
        background: #43a6d1;
    }

    .kpi-label {
        display: block;
        font-size: 14px;
        color: #718096;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .kpi-valor {
        display: block;
        font-size: 27px;
        font-weight: 750;
        color: #20384e;
        line-height: 1.2;
    }

    .kpi-estrelas {
        color: #e6a321;
        font-size: 18px;
        margin-top: 7px;
        letter-spacing: 2px;
    }

    /* ===== DISTRIBUIÇÃO DAS NOTAS ===== */

    .distribuicao-card {
        background: #fff;
        border: 1px solid #e5edf3;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(28, 55, 80, 0.035);
    }

    .distribuicao-card h2 {
        margin: 0 0 20px;
    }

    .distribuicao-table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
        margin: 0;
    }

    .distribuicao-table th {
        color: #718096;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        background: #f7fafc;
        padding: 13px 15px;
        border: none;
        text-align: left;
    }

    .distribuicao-table td {
        padding: 14px 15px;
        border: none;
        border-bottom: 1px solid #edf1f5;
        color: #40546a;
        font-size: 14px;
    }

    .distribuicao-table tr:last-child td {
        border-bottom: none;
    }

    .nota-estrelas {
        color: #e6a321;
        letter-spacing: 2px;
        white-space: nowrap;
    }

    .barra-container {
        height: 10px;
        width: 100%;
        background: #edf2f7;
        border-radius: 20px;
        overflow: hidden;
    }

    .barra-nota {
        height: 100%;
        min-width: 0;
        background: linear-gradient(90deg, #159fc9, #087b82);
        border-radius: 20px;
        transition: width .3s ease;
    }

    /* ===== FILTROS ===== */

    .filtros-card {
        background: #fff;
        border: 1px solid #e5edf3;
        border-radius: 16px;
        padding: 22px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(28, 55, 80, 0.035);
    }

    .filtros {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        gap: 17px;
    }

    .filtro-campo {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .filtro-campo > label {
        color: #526579;
        font-size: 13px;
        font-weight: 650;
        margin: 0;
    }

    .filtro-campo input[type="date"],
    .filtro-campo select {
        width: 175px;
        max-width: 100%;
        height: 44px;
        padding: 0 12px;
        border: 1px solid #dce5ed;
        border-radius: 9px;
        background: #fff;
        color: #34495e;
        font-size: 14px;
        outline: none;
        transition: border .2s, box-shadow .2s;
    }

    .filtro-campo input:focus,
    .filtro-campo select:focus {
        border-color: #159fc9;
        box-shadow: 0 0 0 3px rgba(21, 159, 201, .12);
    }

    .filtro-checkbox {
        display: flex;
        align-items: center;
        gap: 9px;
        height: 44px;
        color: #526579;
        font-size: 14px;
        white-space: nowrap;
    }

    .filtro-checkbox input {
        width: 17px;
        height: 17px;
        accent-color: #159fc9;
        cursor: pointer;
    }

    .filtro-acoes {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-filtrar {
        height: 44px;
        padding: 0 23px;
        border: none;
        border-radius: 9px;
        background: #159fc9;
        color: white;
        font-size: 14px;
        font-weight: 650;
        cursor: pointer;
        transition: all .2s ease;
    }

    .btn-filtrar:hover {
        background: #087b9f;
        transform: translateY(-1px);
    }

    .btn-limpar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 44px;
        padding: 0 16px;
        border: 1px solid #dce5ed;
        border-radius: 9px;
        color: #526579;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .btn-limpar:hover {
        background: #f1f7fa;
        color: #087b82;
        border-color: #159fc9;
    }

    /* ===== LISTA DE AVALIAÇÕES ===== */

    .lista-avaliacoes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .avaliacao {
        background: #fff;
        border: 1px solid #e5edf3;
        border-left: 5px solid #159fc9;
        border-radius: 14px;
        padding: 21px;
        margin: 0;
        box-shadow: 0 4px 15px rgba(28, 55, 80, 0.04);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .avaliacao:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(28, 55, 80, .08);
    }

    .avaliacao.alta {
        border-left-color: #159b75;
    }

    .avaliacao.media {
        border-left-color: #e6a321;
    }

    .avaliacao.baixa {
        border-left-color: #d65b5b;
    }

    .avaliacao-topo {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .avaliacao h3 {
        margin: 0;
        color: #e6a321;
        font-size: 19px;
        letter-spacing: 2px;
    }

    .avaliacao h3 small {
        color: #718096;
        font-size: 12px;
        letter-spacing: 0;
        font-weight: 600;
    }

    .avaliacao-nota-badge {
        background: #fff8e8;
        color: #a87313;
        border-radius: 20px;
        padding: 6px 11px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .avaliacao-comentario {
        color: #34495e;
        font-size: 14px;
        line-height: 1.7;
        margin: 0 0 17px;
        overflow-wrap: anywhere;
    }

    .avaliacao-sem-comentario {
        color: #9aa7b4;
        font-style: italic;
    }

    .avaliacao-divisor {
        height: 1px;
        background: #edf1f5;
        margin: 14px 0;
    }

    .avaliacao .meta {
        color: #718096;
        font-size: 12px;
        line-height: 1.7;
        margin: 5px 0;
        overflow-wrap: anywhere;
    }

    .avaliacao .meta strong {
        color: #40546a;
    }

    .avaliacao-pedido {
        display: inline-block;
        color: #087b9f;
        background: #e9f7fc;
        padding: 5px 9px;
        border-radius: 7px;
        font-weight: 700;
    }

    /* ===== ESTADO VAZIO ===== */

    .avaliacoes-vazio {
        background: #fff;
        border: 1px dashed #cbd8e3;
        border-radius: 16px;
        padding: 45px 20px;
        text-align: center;
        grid-column: 1 / -1;
    }

    .vazio-icone {
        font-size: 38px;
        margin-bottom: 12px;
    }

    .avaliacoes-vazio h3 {
        color: #34495e;
        font-size: 17px;
        margin: 0 0 8px;
    }

    .avaliacoes-vazio p {
        color: #8493a3;
        font-size: 13px;
        margin: 0;
        line-height: 1.6;
    }

    /* ===== ERROS ===== */

    .avaliacoes-page .erros {
        background: #fff0f0;
        color: #b83232;
        border: 1px solid #f5cccc;
        border-radius: 12px;
        padding: 15px 20px 15px 38px;
        margin-bottom: 20px;
    }

    /* ===== PAGINAÇÃO ===== */

    .avaliacoes-paginacao {
        margin-top: 25px;
    }

    .avaliacoes-paginacao nav {
        margin-top: 0;
    }

    /* ===== RESPONSIVIDADE ===== */

    @media (max-width: 900px) {
        .lista-avaliacoes {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .avaliacoes-page h1 {
            font-size: 25px;
        }

        .avaliacoes-kpis {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .avaliacoes-kpi {
            padding: 19px;
        }

        .filtros {
            align-items: stretch;
            flex-direction: column;
        }

        .filtro-campo input[type="date"],
        .filtro-campo select {
            width: 100%;
        }

        .filtro-campo {
            width: 100%;
        }

        .filtro-checkbox {
            height: auto;
            padding: 8px 0;
            white-space: normal;
        }

        .filtro-acoes {
            width: 100%;
        }

        .filtro-acoes button,
        .filtro-acoes a {
            flex: 1;
        }

        .distribuicao-card,
        .filtros-card {
            padding: 16px;
        }

        .distribuicao-table th,
        .distribuicao-table td {
            padding: 10px 7px;
            font-size: 12px;
        }

        .avaliacao {
            padding: 17px;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="avaliacoes-page">

    <div class="avaliacoes-header">
        <h1>Avaliações dos clientes</h1>
        <p class="avaliacoes-subtitulo">
            Acompanhe a satisfação dos seus clientes e veja o que estão falando da sua loja.
        </p>
    </div>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    {{-- INDICADORES --}}

    <div class="avaliacoes-kpis">

        <div class="avaliacoes-kpi">
            <span class="kpi-label">Nota média</span>

            <strong class="kpi-valor">
                {{ $total > 0 ? number_format($media, 1, ',', '.') . ' / 5' : 'Sem avaliações' }}
            </strong>

            @if ($total > 0)
                <div class="kpi-estrelas">
                    {{ str_repeat('★', (int) round($media)) }}{{ str_repeat('☆', 5 - (int) round($media)) }}
                </div>
            @endif
        </div>

        <div class="avaliacoes-kpi">
            <span class="kpi-label">Total de avaliações</span>
            <strong class="kpi-valor">{{ $total }}</strong>
        </div>

        <div class="avaliacoes-kpi">
            <span class="kpi-label">Com comentário</span>
            <strong class="kpi-valor">{{ $totalComComentario }}</strong>
        </div>

    </div>

    {{-- DISTRIBUIÇÃO DAS NOTAS --}}

    @if ($total > 0)

        <div class="distribuicao-card">

            <h2>Distribuição das notas</h2>

            <table class="distribuicao-table">
                <thead>
                    <tr>
                        <th>Nota</th>
                        <th>Quantidade</th>
                        <th>Percentual</th>
                        <th style="width: 40%;">Distribuição</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ([5, 4, 3, 2, 1] as $nota)

                        @php
                            $qtd = $distribuicao[$nota] ?? 0;
                            $pct = round($qtd / $total * 100);
                        @endphp

                        <tr>
                            <td>
                                <span class="nota-estrelas">
                                    {{ str_repeat('★', $nota) }}
                                </span>
                            </td>

                            <td>
                                <strong>{{ $qtd }}</strong>
                            </td>

                            <td>{{ $pct }}%</td>

                            <td>
                                <div class="barra-container">
                                    <div
                                        class="barra-nota"
                                        style="width: {{ $pct }}%"
                                    ></div>
                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        </div>

    @endif

    {{-- FILTROS --}}

    <h2>Filtrar avaliações</h2>

    <div class="filtros-card">

        <form
            action="{{ route('avaliacoes.index') }}"
            method="GET"
            class="filtros"
        >

            <div class="filtro-campo">
                <label for="nota">Nota</label>

                <select id="nota" name="nota">
                    <option value="">Todas</option>

                    @foreach ([5, 4, 3, 2, 1] as $n)
                        <option
                            value="{{ $n }}"
                            @selected(request('nota') == $n)
                        >
                            {{ $n }} {{ str_repeat('★', $n) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filtro-campo">
                <label for="de">Data inicial</label>

                <input
                    type="date"
                    id="de"
                    name="de"
                    value="{{ request('de') }}"
                >
            </div>

            <div class="filtro-campo">
                <label for="ate">Data final</label>

                <input
                    type="date"
                    id="ate"
                    name="ate"
                    value="{{ request('ate') }}"
                >
            </div>

            <label class="filtro-checkbox">
                <input
                    type="checkbox"
                    name="comentario"
                    value="1"
                    @checked(request()->boolean('comentario'))
                >
                Só com comentário
            </label>

            <div class="filtro-acoes">
                <button type="submit" class="btn-filtrar">
                    Filtrar
                </button>

                <a
                    href="{{ route('avaliacoes.index') }}"
                    class="btn-limpar"
                >
                    Limpar
                </a>
            </div>

        </form>

    </div>

    {{-- LISTA DE AVALIAÇÕES --}}

    <h2>Opiniões dos clientes</h2>

    <div class="lista-avaliacoes">

        @forelse ($avaliacoes as $p)

            <article class="avaliacao {{ $p->nota >= 4 ? 'alta' : ($p->nota == 3 ? 'media' : 'baixa') }}">

                <div class="avaliacao-topo">

                    <h3>
                        {{ str_repeat('★', $p->nota) }}{{ str_repeat('☆', 5 - $p->nota) }}
                        <small>{{ $p->nota }}/5</small>
                    </h3>

                    <span class="avaliacao-nota-badge">
                        {{ $p->nota >= 4 ? 'Excelente' : ($p->nota == 3 ? 'Regular' : 'Atenção') }}
                    </span>

                </div>

                @if ($p->comentario_avaliacao)

                    <p class="avaliacao-comentario">
                        "{{ $p->comentario_avaliacao }}"
                    </p>

                @else

                    <p class="avaliacao-comentario avaliacao-sem-comentario">
                        Cliente não deixou nenhum comentário.
                    </p>

                @endif

                <div class="avaliacao-divisor"></div>

                <p class="meta">
                    <span class="avaliacao-pedido">
                        Pedido #{{ $p->id }}
                    </span>
                </p>

                <p class="meta">
                    <strong>Cliente:</strong> {{ $p->cliente->nome }}
                </p>

                <p class="meta">
                    <strong>Data:</strong> {{ $p->created_at->format('d/m/Y H:i') }}
                </p>

                <p class="meta">
                    <strong>Itens:</strong>
                    {{ $p->itens->map(fn ($i) => $i->quantidade . 'x ' . $i->nome)->implode(', ') }}
                </p>

            </article>

        @empty

            <div class="avaliacoes-vazio">

                <div class="vazio-icone">⭐</div>

                <h3>Nenhuma avaliação encontrada</h3>

                <p>
                    As avaliações aparecerão aqui depois que os clientes
                    avaliarem os pedidos entregues.
                </p>

            </div>

        @endforelse

    </div>

    {{-- PAGINAÇÃO --}}

    <div class="avaliacoes-paginacao">
        {{ $avaliacoes->links() }}
    </div>

</div>

@endsection
