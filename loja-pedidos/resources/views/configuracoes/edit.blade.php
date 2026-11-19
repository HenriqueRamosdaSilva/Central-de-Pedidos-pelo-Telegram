
@extends('layouts.app')

@section('titulo', 'Configurações')

@push('estilos')
<style>
    .configuracoes-page {
        --cor-principal: #078cba;
        --cor-principal-escura: #087b9f;
        --cor-clara: #e5f5fc;
        --cor-texto: #203247;
        --cor-secundaria: #68798b;
        --cor-borda: #e1e9ef;

        color: var(--cor-texto);
        max-width: 1050px;
    }

    .configuracoes-page h1 {
        font-size: 2rem;
        font-weight: 750;
        letter-spacing: -0.7px;
        margin: 0 0 8px;
    }

    .configuracoes-intro {
        color: var(--cor-secundaria);
        margin: 0 0 26px;
    }

    .config-card {
        background: #fff;
        border: 1px solid var(--cor-borda);
        border-radius: 16px;
        padding: 24px;
        margin: 20px 0;
        box-shadow: 0 3px 12px rgba(31, 56, 78, .045);
    }

    .config-card h2 {
        font-size: 1.25rem;
        font-weight: 750;
        margin: 0 0 8px;
        letter-spacing: -.3px;
    }

    .config-card .descricao {
        color: var(--cor-secundaria);
        font-size: .92rem;
        line-height: 1.6;
        margin: 0 0 22px;
    }

    .config-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .config-field {
        margin-bottom: 18px;
        min-width: 0;
    }

    .config-field label {
        display: block;
        margin: 0 0 8px;
        font-size: .92rem;
        font-weight: 650;
        color: #35495e;
    }

    .config-field input,
    .config-field select {
        width: 100%;
        max-width: 100%;
        min-height: 45px;
        padding: 10px 13px;
        border: 1px solid #d6e0e8;
        border-radius: 9px;
        background: #fbfdff;
        color: var(--cor-texto);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .config-field input:focus,
    .config-field select:focus {
        border-color: var(--cor-principal);
        box-shadow: 0 0 0 3px rgba(7, 140, 186, .12);
    }

    .config-field small {
        display: block;
        color: var(--cor-secundaria);
        font-size: .82rem;
        margin-top: 6px;
    }

    .config-divisor {
        border: 0;
        border-top: 1px solid var(--cor-borda);
        margin: 24px 0;
    }

    .config-botao {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 10px 19px;
        border: 0;
        border-radius: 9px;
        background: var(--cor-principal);
        color: #fff;
        font-size: .94rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s, transform .2s;
    }

    .config-botao:hover {
        background: var(--cor-principal-escura);
        transform: translateY(-1px);
    }

    .config-botao.secundario {
        background: #eaf4f8;
        color: #087b9f;
    }

    .config-botao.secundario:hover {
        background: #d9edf5;
    }

    .config-botao.perigo {
        background: #fff0ef;
        color: #c0392b;
    }

    .config-botao.perigo:hover {
        background: #ffe0dd;
    }

    .config-acoes {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .config-tabela-wrapper {
        overflow-x: auto;
        border: 1px solid var(--cor-borda);
        border-radius: 11px;
    }

    .config-tabela {
        width: 100%;
        min-width: 580px;
        margin: 0;
        border-collapse: collapse;
        background: #fff;
    }

    .config-tabela th {
        background: #edf6fa;
        color: #365268;
        font-size: .88rem;
        font-weight: 700;
        padding: 14px 16px;
        border: 0;
        border-bottom: 1px solid var(--cor-borda);
    }

    .config-tabela td {
        padding: 12px 16px;
        border: 0;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
        font-size: .92rem;
    }

    .config-tabela tbody tr:last-child td {
        border-bottom: 0;
    }

    .config-tabela tbody tr:hover {
        background: #f8fbfd;
    }

    .config-tabela input[type="time"] {
        min-height: 40px;
        max-width: 150px;
        padding: 7px 10px;
        border: 1px solid #d6e0e8;
        border-radius: 8px;
        background: #fbfdff;
    }

    .config-tabela input[type="checkbox"],
    .config-checkbox input {
        width: 18px;
        height: 18px;
        accent-color: var(--cor-principal);
        cursor: pointer;
        vertical-align: middle;
    }

    .config-tabela .dia-nome {
        font-weight: 650;
    }

    .config-pix-destaque {
        padding: 15px;
        border-radius: 11px;
        background: #edf8fc;
        color: #376077;
        font-size: .9rem;
        line-height: 1.6;
        margin-bottom: 22px;
    }

    .config-codigo {
        display: inline-block;
        padding: 10px 15px;
        background: #edf6fa;
        border: 1px dashed #80b9cc;
        border-radius: 9px;
        color: #087b9f;
        font-size: 1rem;
        font-weight: 750;
        letter-spacing: .5px;
        overflow-wrap: anywhere;
    }

    .config-comando {
        color: #087b9f;
        font-weight: 750;
        background: #edf6fa;
        padding: 3px 7px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .config-vazio {
        padding: 22px;
        text-align: left;
        border: 1px dashed #d4e1e9;
        border-radius: 11px;
        background: #f9fcfe;
        color: var(--cor-secundaria);
    }

    .config-erros {
        background: #fff0ef;
        border: 1px solid #f3c8c4;
        border-radius: 11px;
        color: #a52c25;
        padding: 15px 20px 15px 38px;
        margin-bottom: 20px;
    }

    .config-chat-nome {
        font-weight: 700;
    }

    .config-subtitulo {
        margin-top: 0 !important;
        margin-bottom: 14px !important;
    }

    @media (max-width: 650px) {
        .configuracoes-page h1 {
            font-size: 1.65rem;
        }

        .config-card {
            padding: 17px;
            border-radius: 13px;
        }

        .config-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .config-acoes {
            align-items: stretch;
        }

        .config-acoes .config-botao {
            width: 100%;
        }
    }
</style>
@endpush

@section('conteudo')

<div class="configuracoes-page">

    <h1>Configurações da loja</h1>
    <p class="configuracoes-intro">
        Gerencie as informações, horários de funcionamento e integrações da sua loja.
    </p>

    @if ($errors->any())
        <ul class="config-erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('configuracoes.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ENTREGA E PEDIDO --}}
        <section class="config-card">

            <h2>Entrega e pedido</h2>
            <p class="descricao">
                Defina os valores de entrega e as condições mínimas para realizar um pedido.
            </p>

            <div class="config-grid">

                <div class="config-field">
                    <label for="taxa_entrega">Taxa de entrega (R$)</label>
                    <input
                        type="number"
                        id="taxa_entrega"
                        name="taxa_entrega"
                        step="0.01"
                        min="0"
                        value="{{ old('taxa_entrega', $config->taxa_entrega) }}"
                    >
                    <small>Informe 0 para oferecer entrega gratuita.</small>
                </div>

                <div class="config-field">
                    <label for="pedido_minimo">Pedido mínimo (R$)</label>
                    <input
                        type="number"
                        id="pedido_minimo"
                        name="pedido_minimo"
                        step="0.01"
                        min="0"
                        value="{{ old('pedido_minimo', $config->pedido_minimo) }}"
                    >
                    <small>Valor mínimo sem considerar a taxa de entrega. Informe 0 para não exigir mínimo.</small>
                </div>

            </div>

            <hr class="config-divisor">

            <h2>Estoque</h2>
            <p class="descricao">
                Configure quando o sistema deverá avisar sobre produtos com poucas unidades.
            </p>

            <div class="config-field">
                <label for="estoque_minimo">
                    Quantidade mínima para alerta
                </label>
                <input
                    type="number"
                    id="estoque_minimo"
                    name="estoque_minimo"
                    min="0"
                    value="{{ old('estoque_minimo', $config->estoque_minimo) }}"
                >
                <small>O sistema avisará quando o produto atingir essa quantidade ou menos.</small>
            </div>

        </section>

        {{-- PIX --}}
        <section class="config-card">

            <h2>Pix (copia e cola)</h2>
            <p class="descricao">
                Configure os dados que serão utilizados pelo bot para gerar o código Pix dos pedidos.
            </p>

            <div class="config-pix-destaque">
                <strong>Como funciona?</strong><br>
                Quando o cliente escolhe Pix, o bot envia um código copia e cola com o valor do pedido.
                Preencha os três campos abaixo. Se a chave ficar vazia, nenhum código será enviado.
            </div>

            <div class="config-field">
                <label for="chave_pix">
                    Chave Pix
                </label>
                <input
                    type="text"
                    id="chave_pix"
                    name="chave_pix"
                    value="{{ old('chave_pix', $config->chave_pix) }}"
                    placeholder="CPF, CNPJ, e-mail, celular ou chave aleatória"
                >
            </div>

            <div class="config-grid">

                <div class="config-field">
                    <label for="pix_nome">
                        Nome do recebedor
                    </label>
                    <input
                        type="text"
                        id="pix_nome"
                        name="pix_nome"
                        maxlength="25"
                        value="{{ old('pix_nome', $config->pix_nome) }}"
                        placeholder="Nome do titular"
                    >
                    <small>Máximo de 25 caracteres.</small>
                </div>

                <div class="config-field">
                    <label for="pix_cidade">
                        Cidade do recebedor
                    </label>
                    <input
                        type="text"
                        id="pix_cidade"
                        name="pix_cidade"
                        maxlength="15"
                        value="{{ old('pix_cidade', $config->pix_cidade) }}"
                        placeholder="Cidade"
                    >
                    <small>Máximo de 15 caracteres.</small>
                </div>

            </div>

        </section>

        {{-- HORÁRIOS --}}
        <section class="config-card">

            <h2>Horário de funcionamento</h2>
            <p class="descricao">
                Configure os dias e horários em que a loja aceita pedidos.
                Fora desses horários, o bot não finaliza pedidos.
                O fechamento precisa ser depois da abertura no mesmo dia.
            </p>

            <div class="config-tabela-wrapper">
                <table class="config-tabela">
                    <thead>
                        <tr>
                            <th>Dia da semana</th>
                            <th>Aberto</th>
                            <th>Horário de abertura</th>
                            <th>Horário de fechamento</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($ordem as $dia)
                            @php
                                $h = $horarios[$dia];
                            @endphp

                            <tr>
                                <td class="dia-nome">
                                    {{ $dias[$dia] }}
                                </td>

                                <td>
                                    <input
                                        type="checkbox"
                                        name="horarios[{{ $dia }}][aberto]"
                                        value="1"
                                        @checked(
                                            $errors->any()
                                                ? !empty(old("horarios.$dia.aberto"))
                                                : $h->aberto
                                        )
                                        aria-label="Abrir {{ $dias[$dia] }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="horarios[{{ $dia }}][abre]"
                                        value="{{ old("horarios.$dia.abre", substr($h->abre, 0, 5)) }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="horarios[{{ $dia }}][fecha]"
                                        value="{{ old("horarios.$dia.fecha", substr($h->fecha, 0, 5)) }}"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="config-acoes">
                <button type="submit" class="config-botao">
                    Salvar configurações
                </button>
            </div>

        </section>

    </form>

    {{-- TELEGRAM --}}
    <section class="config-card">

        <h2>Avisos para o vendedor no Telegram</h2>
        <p class="descricao">
            Vincule uma conversa privada ou um grupo do Telegram para receber notificações dos novos pedidos.
        </p>

        <div class="config-pix-destaque">
            <strong>Como ativar os avisos?</strong><br>
            Abra o bot no Telegram (ou adicione o bot a um grupo da equipe)
            e envie o comando abaixo:
        </div>

        <div style="margin-bottom: 18px;">
            <span class="config-codigo">
                /vendedor {{ $config->codigo_vendedor }}
            </span>
        </div>

        <p class="descricao">
            Quem recebe os avisos também pode aceitar, concluir ou cancelar pedidos pelos botões da mensagem.
            Para parar de receber notificações, envie
            <span class="config-comando">/sairvendedor</span>
            no mesmo chat.
        </p>

        <hr class="config-divisor">

        <h2 class="config-subtitulo">Gerenciar código de acesso</h2>

        <p class="descricao">
            Se necessário, você pode gerar um novo código.
            O código anterior deixará de funcionar, mas os chats que já estão vinculados continuarão recebendo notificações.
        </p>

        <form
            action="{{ route('configuracoes.codigo') }}"
            method="POST"
            onsubmit="return confirm('Gerar um novo código? O código atual deixa de funcionar (chats já vinculados continuam recebendo).')"
        >
            @csrf

            <button type="submit" class="config-botao secundario">
                Gerar novo código
            </button>
        </form>

        <hr class="config-divisor">

        <h2 class="config-subtitulo">Chats recebendo os avisos</h2>

        <p class="descricao">
            Confira quais conversas e grupos estão vinculados atualmente.
        </p>

        @forelse ($vendedores as $vendedor)

            @if ($loop->first)
                <div class="config-tabela-wrapper">
                    <table class="config-tabela">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo de conversa</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
            @endif

                            <tr>
                                <td class="config-chat-nome">
                                    {{ $vendedor->nome }}
                                </td>

                                <td>
                                    {{ $vendedor->tipo === 'private' ? 'Conversa privada' : 'Grupo' }}
                                </td>

                                <td>
                                    <form
                                        action="{{ route('configuracoes.vendedores.remover', $vendedor) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remover {{ $vendedor->nome }}?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="config-botao perigo">
                                            Remover
                                        </button>
                                    </form>
                                </td>
                            </tr>

            @if ($loop->last)
                        </tbody>
                    </table>
                </div>
            @endif

        @empty

            <div class="config-vazio">
                <strong>Nenhum chat vinculado ainda.</strong>
                <p style="margin-bottom: 0;">
                    Quando um vendedor enviar o comando de vinculação no Telegram,
                    a conversa aparecerá aqui.
                </p>
            </div>

        @endforelse

    </section>

</div>

@endsection
