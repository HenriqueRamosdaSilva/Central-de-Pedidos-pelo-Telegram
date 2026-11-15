<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPedido extends Model
{
    // O nome da tabela não segue a convenção (item_pedidos), então é informado aqui.
    protected $table = 'itens_pedido';

    protected $fillable = ['pedido_id', 'produto_id', 'nome', 'preco', 'quantidade'];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'quantidade' => 'integer',
        ];
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
