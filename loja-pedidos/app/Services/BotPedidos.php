<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BotPedidos
{
    public function __construct(private TelegramService $telegram)
    {
    }

    public function processar(array $update): void
    {
        if (isset($update['callback_query'])) {
            $this->aoClicar($update['callback_query']);
        } elseif (isset($update['message']['text'])) {
            $this->aoReceberTexto($update['message']);
        }
    }

    private function aoReceberTexto(array $mensagem): void
    {
        $chat = $mensagem['chat']['id'];
        $texto = trim($mensagem['text']);

        if (str_starts_with($texto, '/')) {
            Cache::forget("aguardando_endereco:$chat");

            match (true) {
                str_starts_with($texto, '/start') => $this->boasVindas($mensagem['from'], $chat),
                str_starts_with($texto, '/cardapio') => $this->mostrarCardapio($chat),
                str_starts_with($texto, '/carrinho') => $this->mostrarCarrinho($chat),
                default => $this->telegram->enviar($chat, 'Comando não reconhecido. Use /cardapio ou /carrinho.'),
            };

            return;
        }

        if (Cache::pull("aguardando_endereco:$chat")) {
            $this->confirmar($mensagem['from'], $chat, 'entrega', mb_substr($texto, 0, 300));

            return;
        }

        $this->telegram->enviar($chat, 'Use /cardapio para ver os produtos ou /carrinho para ver seu pedido.');
    }

    private function aoClicar(array $clique): void
    {
        $chat = $clique['message']['chat']['id'];
        $dados = $clique['data'] ?? '';
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
            $dados === 'finalizar' => $this->perguntarTipo($chat),
            $dados === 'tipo:retirada' => $this->confirmar($clique['from'], $chat, 'retirada', null),
            $dados === 'tipo:entrega' => $this->pedirEndereco($chat),
            default => null,
        };
    }

    private function boasVindas(array $usuario, int $chat): void
    {
        $nome = $usuario['first_name'] ?? 'tudo bem';
        $this->telegram->enviar($chat, "Olá, {$nome}! Aqui você faz seu pedido pelo Telegram.");
        $this->mostrarCardapio($chat);
    }

    private function mostrarCardapio(int $chat): void
    {
        $botoes = Produto::where('ativo', true)
            ->where('quantidade', '>', 0)
            ->orderBy('nome')
            ->get()
            ->map(fn ($p) => [[
                'text' => "{$p->nome} · R$ " . number_format($p->preco, 2, ',', '.'),
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
        $itens = $this->itensDoCarrinho($chat);

        if ($itens->isEmpty()) {
            $this->telegram->enviar($chat, 'Seu carrinho está vazio.', [
                [['text' => 'Ver cardápio', 'callback_data' => 'cardapio']],
            ]);

            return;
        }

        $linhas = $itens->map(fn ($p) => "{$p->qtd}x {$p->nome} — R$ " . number_format($p->preco * $p->qtd, 2, ',', '.'));
        $texto = "Seu carrinho:\n\n" . $linhas->implode("\n")
            . "\n\nTotal: R$ " . number_format($this->totalDe($itens), 2, ',', '.');

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

    private function perguntarTipo(int $chat): void
    {
        if ($this->itensDoCarrinho($chat)->isEmpty()) {
            $this->mostrarCarrinho($chat);

            return;
        }

        $this->telegram->enviar($chat, 'Como você quer receber?', [[
            ['text' => '🏪 Retirar na loja', 'callback_data' => 'tipo:retirada'],
            ['text' => '🛵 Entrega', 'callback_data' => 'tipo:entrega'],
        ]]);
    }

    private function pedirEndereco(int $chat): void
    {
        Cache::put("aguardando_endereco:$chat", true, now()->addHour());
        $this->telegram->enviar($chat, 'Digite o endereço de entrega (rua, número, bairro):');
    }

    private function confirmar(array $usuario, int $chat, string $tipo, ?string $endereco): void
    {
        $itens = $this->itensDoCarrinho($chat);

        if ($itens->isEmpty()) {
            $this->mostrarCarrinho($chat);

            return;
        }

        $semEstoque = null;

        $pedido = DB::transaction(function () use ($usuario, $chat, $tipo, $endereco, $itens, &$semEstoque) {
            $estoque = Produto::whereIn('id', $itens->pluck('id'))->lockForUpdate()->get()->keyBy('id');

            foreach ($itens as $p) {
                $atual = $estoque->get($p->id);

                if (! $atual || ! $atual->ativo || $atual->quantidade < $p->qtd) {
                    $semEstoque = $p->nome;

                    return null;
                }
            }

            $cliente = Cliente::updateOrCreate(
                ['telegram_id' => $chat],
                ['nome' => trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? '')) ?: 'Cliente do Telegram']
            );

            $pedido = $cliente->pedidos()->create([
                'tipo' => $tipo,
                'endereco' => $endereco,
                'total' => $this->totalDe($itens),
            ]);

            foreach ($itens as $p) {
                $pedido->itens()->create([
                    'produto_id' => $p->id,
                    'nome' => $p->nome,
                    'preco' => $p->preco,
                    'quantidade' => $p->qtd,
                ]);

                $estoque->get($p->id)->decrement('quantidade', $p->qtd);
            }

            return $pedido;
        });

        if (! $pedido) {
            $this->telegram->enviar(
                $chat,
                "Não temos estoque suficiente de {$semEstoque} no momento. Ajuste o carrinho e tente de novo.",
                [[['text' => '🛒 Ver carrinho', 'callback_data' => 'carrinho']]]
            );

            return;
        }

        Cache::forget("carrinho:$chat");

        $this->telegram->enviar(
            $chat,
            "Pedido #{$pedido->id} recebido! Total: R$ " . number_format($pedido->total, 2, ',', '.')
            . "\nAvisaremos por aqui quando houver novidades."
        );
    }
}
