<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    public const STATUS = ['novo', 'em_preparo', 'pronto', 'entregue', 'cancelado'];

    // Para cada status: [próximo status, texto do botão]
    public const PROXIMO = [
        'novo' => ['em_preparo', 'Aceitar pedido'],
        'em_preparo' => ['pronto', 'Marcar como pronto'],
        'pronto' => ['entregue', 'Marcar como entregue'],
    ];

    protected $fillable = ['cliente_id', 'tipo', 'endereco', 'total', 'status'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
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

    // Mensagem enviada ao cliente no Telegram quando o status muda.
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
