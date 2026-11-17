<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['taxa_entrega', 'pedido_minimo'];

    protected function casts(): array
    {
        return [
            'taxa_entrega' => 'decimal:2',
            'pedido_minimo' => 'decimal:2',
        ];
    }

    public static function atual(): self
    {
        return self::firstOrCreate([], ['taxa_entrega' => 0, 'pedido_minimo' => 0]);
    }
}
