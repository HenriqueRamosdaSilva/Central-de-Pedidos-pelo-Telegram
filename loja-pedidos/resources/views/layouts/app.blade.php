<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Loja')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #eef2f5; color: #1b2733; }
        header { display: flex; gap: 20px; align-items: center; padding: 14px 24px; background: #fff; border-bottom: 1px solid #ccd5df; }
        header a { color: #0e6b73; text-decoration: none; font-weight: bold; }
        main { max-width: 1100px; margin: 24px auto; padding: 0 18px; }
        label { display: block; margin: 12px 0 4px; }
        input, select, textarea, button { padding: 8px; font: inherit; }
        input[type=text], input[type=number], textarea { width: 100%; max-width: 420px; }
        button { cursor: pointer; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 16px; }
        th, td { border: 1px solid #ccd5df; padding: 9px; text-align: left; }
        th { background: #e3eaf0; }
        .aviso { background: #d9f2e4; padding: 10px; border-radius: 4px; }
        .erros { background: #fde4e1; padding: 10px 10px 10px 28px; border-radius: 4px; }
        .pedido { background: #fff; border-left: 6px solid #8796a3; border-radius: 4px; padding: 10px 16px; margin-top: 14px; }
        .pedido.novo { border-color: #e59a00; }
        .pedido.em_preparo { border-color: #2f5fd0; }
        .pedido.pronto { border-color: #1e8e5a; }
        .pedido.cancelado { border-color: #c0392b; opacity: .7; }
        .pedido h3 { margin: 4px 0; }
        .pedido form { display: inline-block; margin-right: 8px; }
        nav svg { width: 1.1rem; height: 1.1rem; }
        nav[role=navigation] { margin-top: 18px; }
    </style>
</head>
<body>
    <header>
        <strong>Painel da loja</strong>
        <a href="{{ route('pedidos.index') }}">Pedidos</a>
        <a href="{{ route('produtos.index') }}">Produtos</a>
    </header>

    <main>
        @if (session('success'))
            <p class="aviso">{{ session('success') }}</p>
        @endif

        @yield('conteudo')
    </main>

    @stack('scripts')
</body>
</html>
