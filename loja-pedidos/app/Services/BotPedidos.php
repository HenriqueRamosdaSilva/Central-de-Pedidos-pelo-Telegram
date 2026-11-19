<?php

namespace App\Services;

use App\Models\ChatVendedor;
use App\Models\Cliente;
use App\Models\Configuracao;
use App\Models\HorarioFuncionamento;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class BotPedidos
{
    public function __construct(
        private TelegramService $telegram,
        private NotificacaoPedidos $avisos,
    ) {
    }

    // Recebe uma atualização do Telegram (mensagem de texto ou clique em botão).
    public function processar(array $update): void
    {
        if (isset($update['callback_query'])) {
            $this->aoClicar($update['callback_query']);
        } elseif (isset($update['message']['text'])) {
            $this->aoReceberTexto($update['message']);
        }
    }

    private function reais(float|string $valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    private function aoReceberTexto(array $mensagem): void
    {
        $chat = $mensagem['chat']['id'];
        $texto = trim($mensagem['text']);

        // Comandos do vendedor (funcionam também em grupos).
        if (preg_match('/^\/vendedor(?:@\w+)?(?:\s+(\S+))?\s*$/i', $texto, $m)) {
            $this->vincularVendedor($mensagem, $m[1] ?? null);

            return;
        }

        if (preg_match('/^\/sairvendedor(?:@\w+)?\s*$/i', $texto)) {
            $this->desvincularVendedor($chat);

            return;
        }

        // Em grupos o bot só atende os comandos acima.
        if (($mensagem['chat']['type'] ?? 'private') !== 'private') {
            return;
        }

        // Comandos sempre cancelam o que estava sendo preenchido (cadastro ou finalização do pedido).
        if (str_starts_with($texto, '/')) {
            Cache::forget("cadastro:$chat");
            Cache::forget("checkout:$chat");
            Cache::forget("avaliacao:$chat");

            match (true) {
                str_starts_with($texto, '/start') => $this->boasVindas($mensagem['from'], $chat),
                str_starts_with($texto, '/cardapio') => $this->mostrarCardapio($chat),
                str_starts_with($texto, '/carrinho') => $this->mostrarCarrinho($chat),
                str_starts_with($texto, '/cadastro') => $this->iniciarCadastro($chat),
                str_starts_with($texto, '/meuspedidos') => $this->meusPedidos($chat),
                str_starts_with($texto, '/repetir') => $this->repetirUltimoPedido($chat),
                default => $this->telegram->enviar($chat, 'Comando não reconhecido. Use /cardapio, /carrinho, /meuspedidos, /repetir ou /cadastro.'),
            };

            return;
        }

        if ($cadastro = Cache::get("cadastro:$chat")) {
            $this->continuarCadastro($chat, $texto, $cadastro);

            return;
        }

        $checkout = Cache::get("checkout:$chat");

        if ($checkout && ! empty($checkout['etapa'])) {
            $this->continuarCheckout($mensagem['from'], $chat, $texto, $checkout);

            return;
        }

        if ($pedidoId = Cache::pull("avaliacao:$chat")) {
            $this->salvarComentario($chat, (int) $pedidoId, $texto);

            return;
        }

        $this->telegram->enviar($chat, 'Use /cardapio para ver os produtos, /carrinho para ver seu pedido ou /meuspedidos para acompanhar seus pedidos.');
    }

    private function aoClicar(array $clique): void
    {
        $chat = $clique['message']['chat']['id'];
        $dados = $clique['data'] ?? '';

        // Loja fechada: não deixa adicionar itens (o cardápio pode ter sido aberto antes de fechar).
        if (str_starts_with($dados, 'add:') && ! HorarioFuncionamento::abertoAgora()) {
            $this->telegram->responderCallback($clique['id'], 'A loja está fechada no momento');
            $this->avisarLojaFechada($chat);

            return;
        }

        $aviso = null;

        if (preg_match('/^add:(\d+)$/', $dados, $partes)) {
            $produto = Produto::where('ativo', true)->where('quantidade', '>', 0)->find($partes[1]);

            if ($produto) {
                $carrinho = Cache::get("carrinho:$chat", []);
                $naCesta = $carrinho[$produto->id] ?? 0;

                if ($naCesta >= $produto->quantidade) {
                    $aviso = "Só temos {$produto->quantidade} em estoque";
                } else {
                    $carrinho[$produto->id] = $naCesta + 1;
                    Cache::put("carrinho:$chat", $carrinho, now()->addDay());
                    $aviso = "{$produto->nome} adicionado ({$carrinho[$produto->id]})";
                }
            } else {
                $aviso = 'Produto indisponível';
            }
        }

        $this->telegram->responderCallback($clique['id'], $aviso);

        match (true) {
            $dados === 'cardapio' => $this->mostrarCardapio($chat),
            $dados === 'carrinho' => $this->mostrarCarrinho($chat),
            $dados === 'limpar' => $this->limparCarrinho($chat),
            $dados === 'finalizar' => $this->iniciarCheckout($chat),
            $dados === 'tipo:retirada' => $this->escolherTipo($chat, 'retirada'),
            $dados === 'tipo:entrega' => $this->escolherTipo($chat, 'entrega'),
            str_starts_with($dados, 'pag:') => $this->escolherPagamento($chat, substr($dados, 4)),
            $dados === 'obs:nenhuma' => $this->confirmar($clique['from'], $chat),
            preg_match('/^cancelar:(\d+)$/', $dados, $m) === 1 => $this->pedirConfirmacaoDeCancelamento($chat, (int) $m[1]),
            preg_match('/^cancelar_sim:(\d+)$/', $dados, $m) === 1 => $this->cancelarPedido($chat, (int) $m[1]),
            $dados === 'cancelar_nao' => $this->telegram->enviar($chat, 'Tudo certo, o pedido continua em andamento.'),
            preg_match('/^nota:(\d+):([1-5])$/', $dados, $m) === 1 => $this->registrarNota($chat, (int) $m[1], (int) $m[2]),
            $dados === 'pular_comentario' => $this->pularComentario($chat),
            $dados === 'repetir' => $this->repetirUltimoPedido($chat),
            str_starts_with($dados, 'v:') => $this->acaoDoVendedor($clique, $dados),
            default => null,
        };
    }

    // ---------- Boas-vindas, cardápio e carrinho ----------

    private function boasVindas(array $usuario, int $chat): void
    {
        $nome = $usuario['first_name'] ?? 'tudo bem';
        $texto = "Olá, {$nome}! Aqui você faz seu pedido pelo Telegram.";

        if (! Cliente::where('telegram_id', $chat)->whereNotNull('endereco')->exists()) {
            $texto .= "\n\nUse /cadastro para salvar seus dados e agilizar as próximas entregas.";
        }

        $this->telegram->enviar($chat, $texto);
        $this->mostrarCardapio($chat);
    }

    private function mostrarCardapio(int $chat): void
    {
        if (! HorarioFuncionamento::abertoAgora()) {
            $this->avisarLojaFechada($chat);

            return;
        }

        $botoes = Produto::where('ativo', true)
            ->where('quantidade', '>', 0)
            ->orderBy('nome')
            ->get()
            ->map(fn ($p) => [[
                'text' => "{$p->nome} · " . $this->reais($p->preco),
                'callback_data' => "add:{$p->id}",
            ]])
            ->all();

        if (! $botoes) {
            $this->telegram->enviar($chat, 'Nenhum produto disponível no momento.');

            return;
        }

        $botoes[] = [['text' => '🛒 Ver carrinho', 'callback_data' => 'carrinho']];
        $this->telegram->enviar($chat, 'Toque num produto para adicionar ao carrinho:', $botoes);
    }

    private function textoLojaFechada(): string
    {
        if (Configuracao::atual()->loja_pausada) {
            return '⏸ A loja está temporariamente fechada. Volte a nos chamar em instantes!';
        }

        $proxima = HorarioFuncionamento::proximaAbertura();

        return '⏰ A loja está fechada no momento.' . ($proxima ? " Abrimos {$proxima}." : '')
            . "\n\nHorário de funcionamento:\n\n" . HorarioFuncionamento::resumo();
    }

    private function avisarLojaFechada(int $chat): void
    {
        $this->telegram->enviar($chat, $this->textoLojaFechada());
    }

    private function itensDoCarrinho(int $chat): Collection
    {
        $carrinho = Cache::get("carrinho:$chat", []);

        return Produto::whereIn('id', array_keys($carrinho))
            ->get()
            ->map(fn ($p) => $p->setAttribute('qtd', $carrinho[$p->id]));
    }

    private function totalDe(Collection $itens): float
    {
        return round($itens->sum(fn ($p) => $p->preco * $p->qtd), 2);
    }

    private function mostrarCarrinho(int $chat): void
    {
        if (! HorarioFuncionamento::abertoAgora()) {
            $this->avisarLojaFechada($chat);

            return;
        }

        $itens = $this->itensDoCarrinho($chat);

        if ($itens->isEmpty()) {
            $this->telegram->enviar($chat, 'Seu carrinho está vazio.', [
                [['text' => 'Ver cardápio', 'callback_data' => 'cardapio']],
            ]);

            return;
        }

        $linhas = $itens->map(fn ($p) => "{$p->qtd}x {$p->nome} — " . $this->reais($p->preco * $p->qtd));
        $texto = "Seu carrinho:\n\n" . $linhas->implode("\n")
            . "\n\nTotal dos itens: " . $this->reais($this->totalDe($itens));

        $minimo = (float) Configuracao::atual()->pedido_minimo;

        if ($minimo > 0) {
            $texto .= "\nPedido mínimo: " . $this->reais($minimo);
        }

        $this->telegram->enviar($chat, $texto, [
            [['text' => '✅ Finalizar pedido', 'callback_data' => 'finalizar']],
            [
                ['text' => '➕ Adicionar mais', 'callback_data' => 'cardapio'],
                ['text' => '🗑 Limpar', 'callback_data' => 'limpar'],
            ],
        ]);
    }

    private function limparCarrinho(int $chat): void
    {
        Cache::forget("carrinho:$chat");
        $this->mostrarCardapio($chat);
    }

    private function iniciarCadastro(int $chat): void
    {
        Cache::put("cadastro:$chat", ['etapa' => 'nome', 'dados' => []], now()->addHour());
        $this->telegram->enviar($chat, 'Vamos fazer o seu cadastro. Qual é o seu nome completo?');
    }

    private function continuarCadastro(int $chat, string $texto, array $estado): void
    {
        $etapa = $estado['etapa'];

        if ($etapa === 'telefone' && strlen(preg_replace('/\D/', '', $texto)) < 10) {
            $this->telegram->enviar($chat, 'Telefone inválido. Digite com DDD, por exemplo: (34) 99999-9999');

            return;
        }

        $estado['dados'][$etapa] = mb_substr($texto, 0, 300);

        $proxima = ['nome' => 'telefone', 'telefone' => 'endereco'][$etapa] ?? null;

        if ($proxima) {
            $estado['etapa'] = $proxima;
            Cache::put("cadastro:$chat", $estado, now()->addHour());
            $this->telegram->enviar($chat, $proxima === 'telefone'
                ? 'Qual é o seu telefone (com DDD)?'
                : 'Qual é o seu endereço de entrega (rua, número, bairro)?');

            return;
        }

        Cache::forget("cadastro:$chat");
        Cliente::updateOrCreate(['telegram_id' => $chat], $estado['dados']);

        $this->telegram->enviar(
            $chat,
            "Cadastro salvo, {$estado['dados']['nome']}! Nas próximas entregas não vou pedir o endereço de novo. Para mudar seus dados, use /cadastro.",
            [[['text' => 'Ver cardápio', 'callback_data' => 'cardapio']]]
        );
    }

    private function problemaNaCompra(int $chat): ?string
    {
        $itens = $this->itensDoCarrinho($chat);

        if ($itens->isEmpty()) {
            return 'Seu carrinho está vazio.';
        }

        if (! HorarioFuncionamento::abertoAgora()) {
            return $this->textoLojaFechada();
        }

        $minimo = (float) Configuracao::atual()->pedido_minimo;
        $subtotal = $this->totalDe($itens);

        if ($minimo > 0 && $subtotal < $minimo) {
            return 'O pedido mínimo é ' . $this->reais($minimo) . '. Seu carrinho está em ' . $this->reais($subtotal)
                . ', faltam ' . $this->reais($minimo - $subtotal) . '.';
        }

        return null;
    }

    private function salvarCheckout(int $chat, array $estado): void
    {
        Cache::put("checkout:$chat", $estado, now()->addHour());
    }

    private function taxaDe(string $tipo): float
    {
        return $tipo === 'entrega' ? (float) Configuracao::atual()->taxa_entrega : 0.0;
    }

    private function iniciarCheckout(int $chat): void
    {
        if ($erro = $this->problemaNaCompra($chat)) {
            $this->telegram->enviar($chat, $erro, [[['text' => '➕ Ver cardápio', 'callback_data' => 'cardapio']]]);

            return;
        }

        $this->salvarCheckout($chat, ['etapa' => null]);

        $this->telegram->enviar($chat, 'Como você quer receber?', [[
            ['text' => '🏪 Retirar na loja', 'callback_data' => 'tipo:retirada'],
            ['text' => '🛵 Entrega', 'callback_data' => 'tipo:entrega'],
        ]]);
    }

    private function escolherTipo(int $chat, string $tipo): void
    {
        $estado = Cache::get("checkout:$chat");

        if (! $estado) {
            $this->mostrarCarrinho($chat);

            return;
        }

        $estado['tipo'] = $tipo;
        $estado['endereco'] = null;

        if ($tipo === 'entrega') {
            // Cliente cadastrado: usa o endereço salvo. Sem cadastro: pergunta.
            $endereco = Cliente::where('telegram_id', $chat)->value('endereco');

            if (! $endereco) {
                $estado['etapa'] = 'endereco';
                $this->salvarCheckout($chat, $estado);
                $this->telegram->enviar($chat, 'Digite o endereço de entrega (rua, número, bairro):');

                return;
            }

            $estado['endereco'] = $endereco;
        }

        $this->salvarCheckout($chat, $estado);
        $this->perguntarPagamento($chat, $estado);
    }

    private function perguntarPagamento(int $chat, array $estado): void
    {
        $subtotal = $this->totalDe($this->itensDoCarrinho($chat));
        $taxa = $this->taxaDe($estado['tipo']);

        $texto = 'Total dos itens: ' . $this->reais($subtotal);

        if ($estado['tipo'] === 'entrega') {
            $texto .= "\nEntrega em: {$estado['endereco']}";
            $texto .= "\nTaxa de entrega: " . ($taxa > 0 ? $this->reais($taxa) : 'grátis');
        }

        $texto .= "\nTotal: " . $this->reais($subtotal + $taxa);
        $texto .= "\n\nQual a forma de pagamento? O pagamento é feito na "
            . ($estado['tipo'] === 'entrega' ? 'entrega.' : 'retirada.');

        $botoes = [];

        foreach (Pedido::PAGAMENTOS as $chave => $rotulo) {
            $botoes[] = ['text' => $rotulo, 'callback_data' => "pag:$chave"];
        }

        $this->telegram->enviar($chat, $texto, [$botoes]);
    }

    private function escolherPagamento(int $chat, string $forma): void
    {
        $estado = Cache::get("checkout:$chat");

        if (! $estado || empty($estado['tipo']) || ! isset(Pedido::PAGAMENTOS[$forma])) {
            $this->mostrarCarrinho($chat);

            return;
        }

        $estado['pagamento'] = $forma;
        $estado['troco'] = null;

        if ($forma === 'dinheiro') {
            $estado['etapa'] = 'troco';
            $this->salvarCheckout($chat, $estado);
            $this->telegram->enviar($chat, "Vai precisar de troco? Digite com quanto você vai pagar (ex.: 100) ou responda \"não\" se não precisar.");

            return;
        }

        $this->perguntarObservacao($chat, $estado);
    }

    private function perguntarObservacao(int $chat, array $estado): void
    {
        $estado['etapa'] = 'observacao';
        $this->salvarCheckout($chat, $estado);

        $this->telegram->enviar(
            $chat,
            'Quer deixar alguma observação? (ex.: sem cebola, tocar a campainha). Digite o texto ou toque no botão.',
            [[['text' => 'Sem observação', 'callback_data' => 'obs:nenhuma']]]
        );
    }

    private function continuarCheckout(array $usuario, int $chat, string $texto, array $estado): void
    {
        $etapa = $estado['etapa'];

        if ($etapa === 'endereco') {
            $estado['endereco'] = mb_substr($texto, 0, 300);
            $estado['etapa'] = null;
            $this->salvarCheckout($chat, $estado);
            $this->perguntarPagamento($chat, $estado);
        } elseif ($etapa === 'troco') {
            if (! str_starts_with(mb_strtolower($texto), 'n')) {
                $limpo = preg_replace('/[^\d,.]/', '', $texto);

                if (str_contains($limpo, ',')) {
                    $limpo = str_replace('.', '', $limpo);
                }

                $valor = (float) str_replace(',', '.', $limpo);
                $total = $this->totalDe($this->itensDoCarrinho($chat)) + $this->taxaDe($estado['tipo']);

                if ($valor < $total) {
                    $this->telegram->enviar($chat, 'O valor precisa ser pelo menos ' . $this->reais($total) . ". Digite com quanto você vai pagar ou responda \"não\".");

                    return;
                }

                $estado['troco'] = $valor;
            }

            $this->perguntarObservacao($chat, $estado);
        } elseif ($etapa === 'observacao') {
            $estado['observacao'] = mb_substr($texto, 0, 300);
            $this->salvarCheckout($chat, $estado);
            $this->confirmar($usuario, $chat);
        }
    }

    private function confirmar(array $usuario, int $chat): void
    {
        $estado = Cache::get("checkout:$chat");

        if (! $estado || empty($estado['tipo']) || empty($estado['pagamento'])) {
            $this->mostrarCarrinho($chat);

            return;
        }

        if ($erro = $this->problemaNaCompra($chat)) {
            Cache::forget("checkout:$chat");
            $this->telegram->enviar($chat, $erro, [[['text' => '🛒 Ver carrinho', 'callback_data' => 'carrinho']]]);

            return;
        }

        $itens = $this->itensDoCarrinho($chat);
        $taxa = $this->taxaDe($estado['tipo']);
        $semEstoque = null;
        $minimoEstoque = (int) Configuracao::atual()->estoque_minimo;
        $estoqueBaixo = [];

        $pedido = DB::transaction(function () use ($usuario, $chat, $estado, $itens, $taxa, $minimoEstoque, &$semEstoque, &$estoqueBaixo) {
            // Trava as linhas dos produtos: dois clientes não conseguem comprar o último item ao mesmo tempo.
            $estoque = Produto::whereIn('id', $itens->pluck('id'))->lockForUpdate()->get()->keyBy('id');

            foreach ($itens as $p) {
                $atual = $estoque->get($p->id);

                if (! $atual || ! $atual->ativo || $atual->quantidade < $p->qtd) {
                    $semEstoque = $p->nome;

                    return null;
                }
            }

            $cliente = Cliente::firstOrCreate(
                ['telegram_id' => $chat],
                ['nome' => trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? '')) ?: 'Cliente do Telegram']
            );

            $pedido = $cliente->pedidos()->create([
                'tipo' => $estado['tipo'],
                'endereco' => $estado['endereco'] ?? null,
                'taxa_entrega' => $taxa,
                'total' => round($this->totalDe($itens) + $taxa, 2),
                'forma_pagamento' => $estado['pagamento'],
                'troco_para' => $estado['troco'] ?? null,
                'observacao' => $estado['observacao'] ?? null,
            ]);

            foreach ($itens as $p) {
                $pedido->itens()->create([
                    'produto_id' => $p->id,
                    'nome' => $p->nome,
                    'preco' => $p->preco,
                    'quantidade' => $p->qtd,
                ]);

                $produtoNoEstoque = $estoque->get($p->id);
                $antes = $produtoNoEstoque->quantidade;
                $produtoNoEstoque->decrement('quantidade', $p->qtd);

                // Guarda os produtos que acabaram de ficar com estoque baixo (para avisar o vendedor).
                if ($antes > $minimoEstoque && $antes - $p->qtd <= $minimoEstoque) {
                    $estoqueBaixo[] = ['nome' => $p->nome, 'restam' => $antes - $p->qtd];
                }
            }

            return $pedido;
        });

        if (! $pedido) {
            Cache::forget("checkout:$chat");
            $this->telegram->enviar(
                $chat,
                "Não temos estoque suficiente de {$semEstoque} no momento. Ajuste o carrinho e tente de novo.",
                [[['text' => '🛒 Ver carrinho', 'callback_data' => 'carrinho']]]
            );

            return;
        }

        Cache::forget("carrinho:$chat");
        Cache::forget("checkout:$chat");

        $this->telegram->enviar($chat, $this->resumoDoPedido($pedido, $itens, $taxa));

        // Pix copia e cola (se o vendedor cadastrou a chave nas configurações).
        $this->enviarPix($chat, $pedido);

        // Avisa o vendedor no Telegram.
        $this->avisos->novoPedido($pedido);
        $this->avisos->estoqueBaixo($estoqueBaixo);
    }

    private function resumoDoPedido(Pedido $pedido, Collection $itens, float $taxa): string
    {
        $linhas = $itens->map(fn ($p) => "{$p->qtd}x {$p->nome}")->implode("\n");

        $texto = "Pedido #{$pedido->id} recebido! ✅\n\n{$linhas}\n";

        if ($taxa > 0) {
            $texto .= "\nTaxa de entrega: " . $this->reais($taxa);
        }

        $texto .= "\nTotal: " . $this->reais($pedido->total);
        $texto .= "\nPagamento: " . $pedido->rotuloPagamento();

        if ($pedido->troco_para) {
            $texto .= ' (troco para ' . $this->reais($pedido->troco_para) . ')';
        }

        $texto .= $pedido->tipo === 'entrega' ? "\nEntrega em: {$pedido->endereco}" : "\nRetirada na loja";

        if ($pedido->observacao) {
            $texto .= "\nObservação: {$pedido->observacao}";
        }

        $texto .= "\n\nAvisaremos por aqui quando houver novidades. Para acompanhar, use /meuspedidos.";

        $cadastrado = Cliente::where('telegram_id', $pedido->cliente->telegram_id)->whereNotNull('endereco')->exists();

        if ($pedido->tipo === 'entrega' && ! $cadastrado) {
            $texto .= "\n\nDica: use /cadastro para salvar seus dados e não digitar o endereço nas próximas vezes.";
        }

        return $texto;
    }

    private function enviarPix(int $chat, Pedido $pedido): void
    {
        if ($pedido->forma_pagamento !== 'pix') {
            return;
        }

        $config = Configuracao::atual();
        $codigo = PixService::copiaECola($config->chave_pix, $config->pix_nome, $config->pix_cidade, (float) $pedido->total);

        if (! $codigo) {
            return;
        }

        $quando = $pedido->tipo === 'entrega' ? 'entrega' : 'retirada';

        $this->telegram->enviar(
            $chat,
            '💠 Pix copia e cola — ' . $this->reais($pedido->total)
                . "\nToque no código para copiar e cole no app do seu banco. Se preferir, pague na {$quando}.\n\n"
                . '<code>' . e($codigo) . '</code>',
            [],
            true
        );
    }

    private function repetirUltimoPedido(int $chat): void
    {
        if (! HorarioFuncionamento::abertoAgora()) {
            $this->avisarLojaFechada($chat);

            return;
        }

        $cliente = Cliente::where('telegram_id', $chat)->first();
        $ultimo = $cliente
            ? $cliente->pedidos()->where('status', '!=', 'cancelado')->with('itens')->orderByDesc('id')->first()
            : null;

        if (! $ultimo) {
            $this->telegram->enviar($chat, 'Você ainda não tem um pedido anterior para repetir. Use /cardapio para escolher seus produtos.');

            return;
        }

        $carrinho = [];
        $indisponiveis = [];

        foreach ($ultimo->itens as $item) {
            $produto = $item->produto_id ? Produto::where('ativo', true)->find($item->produto_id) : null;
            $disponivel = $produto ? min($item->quantidade, $produto->quantidade) : 0;

            if ($disponivel > 0) {
                $carrinho[$produto->id] = ($carrinho[$produto->id] ?? 0) + $disponivel;
            }

            if ($disponivel < $item->quantidade) {
                $indisponiveis[] = $item->nome;
            }
        }

        if (! $carrinho) {
            $this->telegram->enviar($chat, 'Os itens do seu último pedido não estão disponíveis no momento. Use /cardapio para ver o que temos.');

            return;
        }

        $substituiu = Cache::has("carrinho:$chat");
        Cache::put("carrinho:$chat", $carrinho, now()->addDay());

        if ($indisponiveis) {
            $this->telegram->enviar($chat, 'Estes itens estão sem estoque suficiente e ficaram de fora (ou com menos unidades): ' . implode(', ', $indisponiveis) . '.');
        }

        $this->telegram->enviar($chat, "Montei o carrinho com os itens do pedido #{$ultimo->id}." . ($substituiu ? ' O carrinho que você tinha foi substituído.' : ''));
        $this->mostrarCarrinho($chat);
    }

    private function vincularVendedor(array $mensagem, ?string $codigo): void
    {
        $chat = $mensagem['chat']['id'];

        if (! $codigo) {
            $this->telegram->enviar($chat, 'Para receber os avisos de pedidos, envie /vendedor seguido do código que aparece em Configurações no painel. Exemplo: /vendedor ABCD2345');

            return;
        }

        $chave = "vendedor:$chat";

        if (RateLimiter::tooManyAttempts($chave, 5)) {
            $this->telegram->enviar($chat, 'Muitas tentativas. Aguarde alguns minutos e tente de novo.');

            return;
        }

        if (! hash_equals(strtoupper((string) Configuracao::atual()->codigo_vendedor), strtoupper($codigo))) {
            RateLimiter::hit($chave, 600);
            $this->telegram->enviar($chat, 'Código inválido.');

            return;
        }

        RateLimiter::clear($chave);

        $usuario = $mensagem['from'] ?? [];
        $nome = $mensagem['chat']['title'] ?? trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? ''));

        ChatVendedor::updateOrCreate(
            ['chat_id' => $chat],
            ['nome' => $nome ?: "Chat {$chat}", 'tipo' => $mensagem['chat']['type'] ?? 'private']
        );

        $this->telegram->enviar($chat, 'Pronto! Este chat vai receber os avisos de pedidos novos, cancelamentos e estoque baixo. Pelos botões da mensagem você aceita, conclui ou cancela pedidos. Use /sairvendedor para parar de receber.');
    }

    private function desvincularVendedor(int $chat): void
    {
        $removido = ChatVendedor::where('chat_id', $chat)->delete();

        $this->telegram->enviar($chat, $removido
            ? 'Tudo certo, este chat não recebe mais os avisos do vendedor.'
            : 'Este chat não estava recebendo avisos de vendedor.');
    }

    // Botões da mensagem de pedido enviada ao vendedor (callback "v:ação:id").
    private function acaoDoVendedor(array $clique, string $dados): void
    {
        $chat = $clique['message']['chat']['id'];
        $mensagemId = $clique['message']['message_id'];

        if (! ChatVendedor::where('chat_id', $chat)->exists()) {
            $this->telegram->enviar($chat, 'Este chat não está autorizado a atualizar pedidos.');

            return;
        }

        if (! preg_match('/^v:(em_preparo|pronto|entregue|cancelado|pcancelar|voltar):(\d+)$/', $dados, $m)) {
            return;
        }

        [, $acao, $id] = $m;
        $pedido = Pedido::with(['cliente', 'itens'])->find((int) $id);

        if (! $pedido) {
            $this->telegram->enviar($chat, 'Pedido não encontrado.');

            return;
        }

        // Pede confirmação antes de cancelar.
        if ($acao === 'pcancelar') {
            $this->telegram->editar($chat, $mensagemId, $this->avisos->textoParaVendedor($pedido), $this->avisos->botoesDeConfirmarCancelamento($pedido));

            return;
        }

        if ($acao !== 'voltar') {
            $atualizado = Pedido::mudarStatus($pedido->id, $acao, Pedido::ORIGENS[$acao]);

            if ($atualizado) {
                $pedido = $atualizado->load(['cliente', 'itens']);
                $this->avisos->avisarCliente($pedido);
            } else {
                // Alguém já mexeu no pedido (painel ou outro chat): mostra a situação atual.
                $pedido = $pedido->fresh(['cliente', 'itens']);
                $this->telegram->enviar($chat, "O pedido #{$pedido->id} já tinha sido atualizado (agora: {$pedido->rotuloStatus()}).");
            }
        }

        $this->telegram->editar($chat, $mensagemId, $this->avisos->textoParaVendedor($pedido), $this->avisos->botoesParaVendedor($pedido));
    }
    private function pedidoDoCliente(int $chat, int $id): ?Pedido
    {
        return Pedido::whereHas('cliente', fn ($q) => $q->where('telegram_id', $chat))->find($id);
    }

    private function pedirConfirmacaoDeCancelamento(int $chat, int $id): void
    {
        $pedido = $this->pedidoDoCliente($chat, $id);

        if (! $pedido || ! in_array($pedido->status, ['novo', 'em_preparo'], true)) {
            $this->avisarQueNaoPodeCancelar($chat);

            return;
        }

        $this->telegram->enviar($chat, "Tem certeza que quer cancelar o pedido #{$id}?", [[
            ['text' => 'Sim, cancelar', 'callback_data' => "cancelar_sim:$id"],
            ['text' => 'Não', 'callback_data' => 'cancelar_nao'],
        ]]);
    }

    private function cancelarPedido(int $chat, int $id): void
    {
        $pedido = $this->pedidoDoCliente($chat, $id);

        // Só cancela enquanto o pedido não estiver pronto; o estoque volta junto.
        $cancelado = $pedido ? Pedido::mudarStatus($id, 'cancelado', ['novo', 'em_preparo']) : null;

        if (! $cancelado) {
            $this->avisarQueNaoPodeCancelar($chat);

            return;
        }

        $this->telegram->enviar($chat, "Pedido #{$id} cancelado. Quando quiser fazer outro, é só usar /cardapio.");
        $this->avisos->clienteCancelou($cancelado);
    }

    private function avisarQueNaoPodeCancelar(int $chat): void
    {
        $this->telegram->enviar($chat, 'Este pedido não pode mais ser cancelado: o cancelamento só é possível enquanto ele ainda não está pronto.');
    }

    private function registrarNota(int $chat, int $id, int $nota): void
    {
        $pedido = $this->pedidoDoCliente($chat, $id);

        if (! $pedido || $pedido->status !== 'entregue') {
            $this->telegram->enviar($chat, 'Só dá para avaliar pedidos já entregues.');

            return;
        }

        if ($pedido->nota) {
            $this->telegram->enviar($chat, 'Você já avaliou este pedido. Obrigado!');

            return;
        }

        $pedido->update(['nota' => $nota]);
        Cache::put("avaliacao:$chat", $id, now()->addHour());

        $this->telegram->enviar(
            $chat,
            "Obrigado pela nota {$nota}/5! Quer deixar um comentário? Digite abaixo ou toque em Pular.",
            [[['text' => 'Pular', 'callback_data' => 'pular_comentario']]]
        );
    }

    private function salvarComentario(int $chat, int $id, string $texto): void
    {
        $pedido = $this->pedidoDoCliente($chat, $id);

        if ($pedido && $pedido->nota) {
            $pedido->update(['comentario_avaliacao' => mb_substr($texto, 0, 300)]);
        }

        $this->telegram->enviar($chat, 'Comentário salvo. Obrigado pela avaliação! 💚');
    }

    private function pularComentario(int $chat): void
    {
        Cache::forget("avaliacao:$chat");
        $this->telegram->enviar($chat, 'Tudo certo, obrigado pela avaliação! 💚');
    }

    private function meusPedidos(int $chat): void
    {
        $cliente = Cliente::where('telegram_id', $chat)->first();

        $pedidos = $cliente
            ? $cliente->pedidos()->with('itens')->orderByDesc('id')->limit(5)->get()
            : collect();

        if ($pedidos->isEmpty()) {
            $this->telegram->enviar($chat, 'Você ainda não fez nenhum pedido.', [
                [['text' => 'Ver cardápio', 'callback_data' => 'cardapio']],
            ]);

            return;
        }

        $blocos = $pedidos->map(function ($p) {
            $itens = $p->itens->map(fn ($i) => "{$i->quantidade}x {$i->nome}")->implode(', ');

            return "Pedido #{$p->id} — {$p->rotuloStatus()}\n{$itens}\nTotal: "
                . $this->reais($p->total) . ' · ' . $p->created_at->format('d/m H:i');
        });

        // Só dá para cancelar enquanto o pedido ainda não está pronto.
        $botoes = $pedidos
            ->filter(fn ($p) => in_array($p->status, ['novo', 'em_preparo'], true))
            ->map(fn ($p) => [['text' => "❌ Cancelar pedido #{$p->id}", 'callback_data' => "cancelar:{$p->id}"]])
            ->values()
            ->all();

        $botoes[] = [['text' => '🔁 Repetir último pedido', 'callback_data' => 'repetir']];

        $this->telegram->enviar($chat, "Seus últimos pedidos:\n\n" . $blocos->implode("\n\n"), $botoes);
    }
}
