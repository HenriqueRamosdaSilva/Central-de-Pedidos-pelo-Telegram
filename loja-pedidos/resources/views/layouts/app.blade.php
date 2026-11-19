
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('titulo', 'Loja')</title>

    <link rel="icon" type="image/png" href="{{ asset('logo_do_projeto.png') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f7fa;
            color: #1b2733;
        }

        /* =========================
           MENU PRINCIPAL
        ========================= */

        header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            min-height: 76px;
            padding: 12px 30px;
            background: #ffffff;
            border-bottom: 1px solid #e5eaf0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }

        .menu-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-right: 15px;
            text-decoration: none;
            color: #17212b;
            flex-shrink: 0;
        }

        .menu-logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .menu-logo strong {
            font-size: 17px;
            font-weight: 700;
            white-space: nowrap;
        }

        .menu-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
        }

        .menu-links a {
            display: inline-flex;
            align-items: center;
            padding: 10px 13px;
            border-radius: 9px;
            color: #465463;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .menu-links a:hover {
            background: #eaf6fc;
            color: #168fca;
        }

        .menu-links a.active {
            background: #e3f3fc;
            color: #168fca;
        }

        .menu-user {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .menu-user .usuario {
            color: #536273;
            font-size: 14px;
            font-weight: 600;
        }

        .menu-logout {
            padding: 9px 17px;
            border: none;
            border-radius: 9px;
            background: #f0f3f6;
            color: #354454;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-logout:hover {
            background: #e3eaf0;
            color: #168fca;
        }

        /* =========================
           CONTEÚDO DAS PÁGINAS
        ========================= */

        main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 22px;
        }

        label {
            display: block;
            margin: 12px 0 4px;
        }

        input, select, textarea, button {
            padding: 8px;
            font: inherit;
        }

        input[type=text],
        input[type=email],
        input[type=password],
        input[type=number],
        textarea {
            width: 100%;
            max-width: 420px;
        }

        button {
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 16px;
        }

        th, td {
            border: 1px solid #ccd5df;
            padding: 9px;
            text-align: left;
        }

        th {
            background: #e3eaf0;
        }

        .aviso {
            background: #d9f2e4;
            padding: 12px 16px;
            border-radius: 9px;
        }

        .erros {
            background: #fde4e1;
            padding: 12px 16px 12px 28px;
            border-radius: 9px;
        }

        /* =========================
           PEDIDOS
        ========================= */

        .pedido {
            background: #fff;
            border-left: 6px solid #8796a3;
            border-radius: 10px;
            padding: 16px;
            margin-top: 14px;
        }

        .pedido.novo {
            border-color: #e59a00;
        }

        .pedido.em_preparo {
            border-color: #2f5fd0;
        }

        .pedido.pronto {
            border-color: #1e8e5a;
        }

        .pedido.cancelado {
            border-color: #c0392b;
            opacity: .7;
        }

        .pedido h3 {
            margin: 4px 0;
        }

        .pedido form {
            display: inline-block;
            margin-right: 8px;
        }

        nav svg {
            width: 1.1rem;
            height: 1.1rem;
        }

        nav[role=navigation] {
            margin-top: 18px;
        }

        /* =========================
           LOGIN
        ========================= */

        body.login-body {
            background: #ffffff;
            min-height: 100vh;
        }

        body.login-body header {
            display: none;
        }

        body.login-body main.login-main {
            max-width: none;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        /* =========================
           RESPONSIVIDADE
        ========================= */

        @media (max-width: 900px) {
            header {
                padding: 14px 18px;
            }

            .menu-user {
                margin-left: 0;
            }
        }

        @media (max-width: 600px) {
            header {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }

            .menu-links {
                width: 100%;
                gap: 4px;
            }

            .menu-links a {
                padding: 9px 10px;
                font-size: 13px;
            }

            .menu-user {
                width: 100%;
                justify-content: space-between;
            }

            main {
                margin: 20px auto;
                padding: 0 14px;
            }
        }
    </style>

    @stack('estilos')
</head>

<body class="{{ request()->is('login') ? 'login-body' : '' }}">

    @if (!request()->is('login'))

        <header>

            <!-- LOGO E NOME DO SISTEMA -->
            <a href="{{ route('pedidos.index') }}" class="menu-logo">

                <img
                    src="{{ asset('logo_do_projeto.png') }}"
                    alt="Logo do projeto"
                >

                <strong>Painel da loja</strong>

            </a>

            @auth

                <!-- LINKS DO MENU -->
                <nav class="menu-links">

                    <a
                        href="{{ route('pedidos.index') }}"
                        class="{{ request()->routeIs('pedidos.*') ? 'active' : '' }}"
                    >
                        Pedidos
                    </a>

                    @if (auth()->user()->perfil === 'admin')

                        <a
                            href="{{ route('relatorios.index') }}"
                            class="{{ request()->routeIs('relatorios.*') ? 'active' : '' }}"
                        >
                            Relatórios
                        </a>

                        <a
                            href="{{ route('avaliacoes.index') }}"
                            class="{{ request()->routeIs('avaliacoes.*') ? 'active' : '' }}"
                        >
                            Avaliações
                        </a>

                        <a
                            href="{{ route('produtos.index') }}"
                            class="{{ request()->routeIs('produtos.*') ? 'active' : '' }}"
                        >
                            Produtos
                        </a>

                        <a
                            href="{{ route('configuracoes.edit') }}"
                            class="{{ request()->routeIs('configuracoes.*') ? 'active' : '' }}"
                        >
                            Configurações
                        </a>

                        <a
                            href="{{ route('usuarios.index') }}"
                            class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                        >
                            Usuários
                        </a>

                    @endif

                </nav>

                <!-- USUÁRIO E SAIR -->
                <div class="menu-user">

                    <span class="usuario">
                        {{ auth()->user()->name }}
                    </span>

                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >
                        @csrf

                        <button type="submit" class="menu-logout">
                            Sair
                        </button>

                    </form>

                </div>

            @endauth

        </header>

    @endif

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="{{ request()->is('login') ? 'login-main' : '' }}">

        @if (session('success'))
            <p class="aviso">
                {{ session('success') }}
            </p>
        @endif

        @yield('conteudo')

    </main>

    @stack('scripts')

</body>
</html>
