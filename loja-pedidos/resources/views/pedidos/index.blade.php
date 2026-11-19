
@extends('layouts.app')

@section('titulo', 'Pedidos')

@section('conteudo')

<style>
    /* ÁREA PRINCIPAL */
    .pedidos-page {
        width: 100%;
        padding: 12px 8px 40px;
    }

    .pedidos-header {
        margin-bottom: 25px;
    }

    .pedidos-header h1 {
        color: #171717;
        font-size: 30px;
        font-weight: 750;
        margin: 0 0 7px;
    }

    .pedidos-header p {
        color: #7b8794;
        font-size: 14px;
        margin: 0;
    }

    /* FILTRO */
    .filtro-pedidos {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .filtro-pedidos label {
        margin: 0;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }

    .filtro-pedidos select {
        min-width: 180px;
        padding: 11px 38px 11px 14px;
        border: 1px solid #dce3ea;
        border-radius: 10px;
        background: #ffffff;
        color: #334155;
        font-size: 14px;
        cursor: pointer;
        outline: none;
    }

    .filtro-pedidos select:focus {
        border-color: #299fdb;
        box-shadow: 0 0 0 3px rgba(41, 159, 219, 0.12);
    }

    /* ESTADO DA LOJA */
    .estado-loja {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        justify-content: space-between;
        padding: 17px 20px;
        margin: 15px 0 22px;
        border: 1px solid transparent;
        border-radius: 13px;
        font-size: 14px;
        font-weight: 500;
    }

    .estado-loja span {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .estado-loja.aberta {
        background: #eaf8ef;
        border-color: #c9ecd4;
        color: #18743c;
    }

    .estado-loja.fora {
        background: #eef5fa;
        border-color: #d7e6f1;
        color: #41657e;
    }

    .estado-loja.pausada {
        background: #fff0f0;
        border-color: #f4d0d0;
        color: #b83232;
    }

    .estado-loja form {
        margin: 0;
    }

    .estado-loja button {
        padding: 10px 17px;
        border: none;
        border-radius: 9px;
        background: #299fdb;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.25s ease;
    }

    .estado-loja button:hover {
        background: #1687c0;
        transform: translateY(-1px);
    }

    .estado-loja.pausada button {
        background: #269b59;
    }

    .estado-loja.pausada button:hover {
        background: #187a43;
    }

    /* AVISO DE ESTOQUE */
    .alerta-estoque {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 15px 18px;
        margin: 15px 0 22px;
        border: 1px solid #f3dda0;
        border-radius: 12px;
        background: #fff9e9;
        color: #88620b;
        font-size: 14px;
    }

    .alerta-estoque a {
        color: #1687c0;
        font-weight: 700;
        text-decoration: none;
    }

    .alerta-estoque a:hover {
        text-decoration: underline;
    }

    /* CARTÕES DE PEDIDOS */
    .pedido {
        background: #ffffff;
        border: 1px solid #e5eaf0;
        border-left: 5px solid #8796a3;
        border-radius: 14px;
        padding: 22px;
        margin: 17px 0;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.035);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }

    .pedido:hover {
        box-shadow: 0 7px 20px rgba(0, 0, 0, 0.07);
        transform: translateY(-2px);
    }

    .pedido.novo {
        border-left-color: #e59a00;
    }

    .pedido.em_preparo {
        border-left-color: #299fdb;
    }

    .pedido.pronto {
        border-left-color: #1e9b62;
    }

    .pedido.entregue {
        border-left-color: #64748b;
    }

    .pedido.cancelado {
        border-left-color: #dc4545;
    }

    .pedido h3 {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 0 0 16px;
        color: #17212d;
        font-size: 19px;
    }

    .pedido h3 small {
        color: #8995a3;
        font-size: 13px;
        font-weight: 400;
    }

    .pedido p {
        color: #526071;
        font-size: 14px;
        line-height: 1.7;
        margin: 12px 0;
    }

    .pedido strong {
        color: #202d3b;
    }

    .pedido ul {
        padding: 13px 18px 13px 35px;
        margin: 13px 0;
        border-radius: 9px;
        background: #f6f9fc;
        color: #475569;
        font-size: 14px;
        line-height: 1.9;
    }

    .pedido-status {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 20px;
        background: #edf2f7;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .pedido.novo .pedido-status {
        background: #fff2d5;
        color: #9a6500;
    }

    .pedido.em_preparo .pedido-status {
        background: #e3f3fc;
        color: #1675a7;
    }

    .pedido.pronto .pedido-status {
        background: #e3f6eb;
        color: #187a43;
    }

    .pedido.cancelado .pedido-status {
        background: #fde8e8;
        color: #bd3434;
    }

    .pedido-entrega {
        padding: 12px 14px;
        margin: 14px 0;
        border-radius: 9px;
        background: #f6f9fc;
    }

    .pedido-acoes {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 16px;
        margin-top: 15px;
        border-top: 1px solid #edf0f4;
    }

    .pedido-acoes form {
        display: inline-block;
        margin: 0;
    }

    .pedido-acoes button {
        padding: 10px 16px;
        border: none;
        border-radius: 9px;
        background: #299fdb;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.25s ease;
    }

    .pedido-acoes button:hover {
        background: #1687c0;
    }

    .pedido-acoes .btn-cancelar {
        background: #fff0f0;
        color: #c03939;
        border: 1px solid #f2cccc;
    }

    .pedido-acoes .btn-cancelar:hover {
        background: #fbdede;
    }

    /* PEDIDOS VAZIOS */
    .pedidos-vazios {
        padding: 45px 20px;
        margin-top: 20px;
        border: 1px dashed #d4dee8;
        border-radius: 14px;
        background: #ffffff;
        text-align: center;
    }

    .pedidos-vazios .icone-vazio {
        font-size: 38px;
        margin-bottom: 12px;
    }

    .pedidos-vazios h3 {
        margin: 0 0 8px;
        color: #334155;
        font-size: 18px;
    }

    .pedidos-vazios p {
        margin: 0;
        color: #8793a1;
        font-size: 14px;
    }

    /* PAGINAÇÃO */
    .paginacao {
        margin-top: 25px;
    }

    .paginacao nav {
        display: flex;
        justify-content: center;
    }

    @media (max-width: 600px) {
        .pedidos-page {
            padding: 5px 0 30px;
        }

        .pedidos-header h1 {
            font-size: 25px;
        }

        .estado-loja {
            align-items: flex-start;
        }

        .estado-loja form,
        .estado-loja button {
            width: 100%;
        }

        .pedido {
            padding: 17px;
        }

        .pedido-acoes {
            flex-direction: column;
        }

        .pedido-acoes form,
        .pedido-acoes button {
            width: 100%;
        }

        .filtro-pedidos {
            flex-wrap: wrap;
        }
    }
</style>

<div class="pedidos-page">

    <div class="pedidos-header">
        <h1>Pedidos</h1>
        <p>Acompanhe e gerencie os pedidos recebidos pela sua loja.</p>
    </div>

    <form
        action="{{ route('pedidos.index') }}"
        method="GET"
        class="filtro-pedidos"
    >
        <label for="status">Mostrar:</label>

        <select
            id="status"
            name="status"
            onchange="this.form.submit()"
        >
            @foreach ([
                'abertos' => 'Em aberto',
                'novo' => 'Novos',
                'em_preparo' => 'Em preparo',
                'pronto' => 'Prontos',
                'entregue' => 'Entregues',
                'cancelado' => 'Cancelados',
                'todos' => 'Todos'
            ] as $valor => $rotulo)

                <option
                    value="{{ $valor }}"
                    @selected($status === $valor)
                >
                    {{ $rotulo }}
                </option>

            @endforeach
        </select>

        <noscript>
            <button type="submit">Filtrar</button>
        </noscript>
    </form>

    <div id="lista">

        @include('pedidos._estado_loja')

        @include('pedidos._lista')

    </div>

</div>

@endsection

@push('scripts')
<script>
    // Atualiza os pedidos automaticamente a cada 8 segundos.
    setInterval(async () => {
        try {
            const resposta = await fetch(location.href);

            if (!resposta.ok) return;

            const doc = new DOMParser().parseFromString(
                await resposta.text(),
                'text/html'
            );

            const nova = doc.getElementById('lista');
            const atual = document.getElementById('lista');

            if (nova && atual) {
                atual.innerHTML = nova.innerHTML;
            }

        } catch (erro) {
            console.error('Erro ao atualizar os pedidos:', erro);
        }
    }, 8000);
</script>
@endpush
