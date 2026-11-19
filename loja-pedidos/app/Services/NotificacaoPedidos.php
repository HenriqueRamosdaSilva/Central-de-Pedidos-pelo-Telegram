<?php

namespace App\Services;

use App\Models\ChatVendedor;
use App\Models\Pedido;

// Mensagens do Telegram sobre pedidos: para o vendedor (chats vinculados) e para o cliente.
class NotificacaoPedidos
{
    public function __construct(private TelegramService $telegram)
    {
    }

    private function reais(float|string $valor): string
    {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    private function aoVendedor(string $texto, array $botoes = []): void
    {
        foreach (ChatVendedor::all() as $chat) {
            $this->telegram->enviar($chat->chat_id, $texto, $botoes);
        }
    }

    // ---------- Para o vendedor ----------

    public function textoParaVendedor(Pedido $pedido): string
    {
        $pedido->loadMissing(['cliente', 'itens']);

        $icone = [
            'novo' => '🆕', 'em_preparo' => '👨‍🍳', 'pronto' => '✅', 'entregue' => '📦', 'cancelado' => '❌',
        ][$pedido->status] ?? '📋';

        $cliente = $pedido->cliente->nome . ($pedido->cliente->telefone ? " ({$pedido->cliente->telefone})" : '');
        $itens = $pedido->itens->map(fn ($i) => "{$i->quantidade}x {$i->nome}")->implode("\n");

        $texto = "{$icone} Pedido #{$pedido->id} — {$pedido->rotuloStatus()}\n\nCliente: {$cliente}\n{$itens}\n";

        if ($pedido->taxa_entrega > 0) {
            $texto .= "\nTaxa de entrega: " . $this->reais($pedido->taxa_entrega);
        }

        $texto .= "\nTotal: " . $this->reais($pedido->total);
        $texto .= "\nPagamento: " . $pedido->rotuloPagamento();

        if ($pedido->troco_para) {
            $texto .= ' (troco para ' . $this->reais($pedido->troco_para)
                . ', devolver ' . $this->reais($pedido->troco_para - $pedido->total) . ')';
        }

        $texto .= $pedido->tipo === 'entrega' ? "\nEntrega em: {$pedido->endereco}" : "\nRetirada na loja";

        if ($pedido->observacao) {
            $texto .= "\nObservação: {$pedido->observacao}";
        }

        return $texto;
    }

    // Botões: avançar para o próximo status e cancelar (com confirmação).
    public function botoesParaVendedor(Pedido $pedido): array
    {
        $linhas = [];

        if ($proximo = $pedido->proximoStatus()) {
            $linhas[] = [['text' => $proximo[1], 'callback_data' => "v:{$proximo[0]}:{$pedido->id}"]];
        }

        if (in_array($pedido->status, ['novo', 'em_preparo'], true)) {
            $linhas[] = [['text' => '❌ Cancelar pedido', 'callback_data' => "v:pcancelar:{$pedido->id}"]];
        }

        return $linhas;
    }

    public function botoesDeConfirmarCancelamento(Pedido $pedido): array
    {
        return [[
            ['text' => 'Sim, cancelar', 'callback_data' => "v:cancelado:{$pedido->id}"],
            ['text' => 'Voltar', 'callback_data' => "v:voltar:{$pedido->id}"],
        ]];
    }

    public function novoPedido(Pedido $pedido): void
    {
        $this->aoVendedor($this->textoParaVendedor($pedido), $this->botoesParaVendedor($pedido));
    }

    public function clienteCancelou(Pedido $pedido): void
    {
        $pedido->loadMissing('cliente');

        $this->aoVendedor("❌ O cliente {$pedido->cliente->nome} cancelou o pedido #{$pedido->id}. O estoque foi devolvido.");
    }

    // $produtos: lista de ['nome' => ..., 'restam' => ...]
    public function estoqueBaixo(array $produtos): void
    {
        if (! $produtos) {
            return;
        }

        $linhas = collect($produtos)->map(fn ($p) => "• {$p['nome']}: restam {$p['restam']}")->implode("\n");

        $this->aoVendedor("⚠️ Estoque baixo:\n{$linhas}");
    }

    // ---------- Para o cliente ----------

    // Avisa o cliente da mudança de status (e pede a avaliação quando o pedido é entregue).
    public function avisarCliente(Pedido $pedido): void
    {
        $pedido->loadMissing('cliente');

        $chat = $pedido->cliente->telegram_id;
        $mensagem = $pedido->mensagemDoStatus();

        if ($pedido->status === 'entregue') {
            $this->telegram->enviar(
                $chat,
                $mensagem . "\n\nComo foi o seu pedido? Toque numa nota de 1 a 5:",
                $pedido->botoesAvaliacao()
            );

            return;
        }

        $this->telegram->enviar($chat, $mensagem);
    }
}
