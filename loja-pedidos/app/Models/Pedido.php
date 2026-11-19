<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Pedido extends Model
{
    public const STATUS = ['novo', 'em_preparo', 'pronto', 'entregue', 'cancelado'];

    public const ROTULOS = [
        'novo' => 'recebido',
        'em_preparo' => 'em preparo',
        'pronto' => 'pronto',
        'entregue' => 'entregue',
        'cancelado' => 'cancelado',
    ];

    public const PAGAMENTOS = ['pix' => 'Pix', 'cartao' => 'Cartão', 'dinheiro' => 'Dinheiro'];

    public const PROXIMO = [
        'novo' => ['em_preparo', 'Aceitar pedido'],
        'em_preparo' => ['pronto', 'Marcar como pronto'],
        'pronto' => ['entregue', 'Marcar como entregue'],
    ];

    public const ORIGENS = [
        'em_preparo' => ['novo'],
        'pronto' => ['em_preparo'],
        'entregue' => ['pronto'],
        'cancelado' => ['novo', 'em_preparo', 'pronto'],
    ];

    protected $fillable = [
        'cliente_id', 'tipo', 'endereco', 'total', 'status',
        'taxa_entrega', 'forma_pagamento', 'troco_para', 'observacao',
        'nota', 'comentario_avaliacao',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'taxa_entrega' => 'decimal:2',
            'troco_para' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    public function proximoStatus(): ?array
    {
        return self::PROXIMO[$this->status] ?? null;
    }

    public function rotuloStatus(): string
    {
        return self::ROTULOS[$this->status] ?? $this->status;
    }

    public function rotuloPagamento(): string
    {
        return self::PAGAMENTOS[$this->forma_pagamento] ?? 'Não informado';
    }

    public static function mudarStatus(int $id, string $novoStatus, array $statusAtuaisPermitidos = ['novo', 'em_preparo', 'pronto']): ?self
    {
        return DB::transaction(function () use ($id, $novoStatus, $statusAtuaisPermitidos) {
            $atual = self::with('itens')->lockForUpdate()->find($id);

            if (! $atual || ! in_array($atual->status, $statusAtuaisPermitidos, true)) {
                return null;
            }

            if ($novoStatus === 'cancelado') {
                foreach ($atual->itens as $item) {
                    if ($item->produto_id) {
                        Produto::whereKey($item->produto_id)->increment('quantidade', $item->quantidade);
                    }
                }
            }

            $atual->update(['status' => $novoStatus]);

            return $atual;
        });
    }

    public function botoesAvaliacao(): array
    {
        $linha = [];

        foreach (range(1, 5) as $nota) {
            $linha[] = ['text' => "{$nota} ⭐", 'callback_data' => "nota:{$this->id}:{$nota}"];
        }

        return [$linha];
    }

    public function mensagemDoStatus(): string
    {
        return match ($this->status) {
            'em_preparo' => "Seu pedido #{$this->id} foi aceito e está em preparo 👨‍🍳",
            'pronto' => $this->tipo === 'entrega'
                ? "Seu pedido #{$this->id} está pronto e já vai sair para entrega 🛵"
                : "Seu pedido #{$this->id} está pronto para retirada 🎉",
            'entregue' => "Pedido #{$this->id} concluído. Obrigado pela preferência!",
            'cancelado' => "Seu pedido #{$this->id} foi cancelado. Fale com a loja se precisar de ajuda.",
            default => "Pedido #{$this->id}: {$this->status}",
        };
    }
}
