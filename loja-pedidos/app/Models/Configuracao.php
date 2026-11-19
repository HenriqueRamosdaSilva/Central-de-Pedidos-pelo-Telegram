<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'taxa_entrega', 'pedido_minimo', 'loja_pausada', 'estoque_minimo',
        'chave_pix', 'pix_nome', 'pix_cidade', 'codigo_vendedor',
    ];

    protected function casts(): array
    {
        return [
            'taxa_entrega' => 'decimal:2',
            'pedido_minimo' => 'decimal:2',
            'loja_pausada' => 'boolean',
            'estoque_minimo' => 'integer',
        ];
    }

    public static function atual(): self
    {
        $config = self::firstOrCreate([], ['taxa_entrega' => 0, 'pedido_minimo' => 0]);

        if (! $config->codigo_vendedor) {
            $config->update(['codigo_vendedor' => self::gerarCodigo()]);
        }

        return $config;
    }

    public static function gerarCodigo(): string
    {
        $alfabeto = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $codigo = '';

        for ($i = 0; $i < 8; $i++) {
            $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
        }

        return $codigo;
    }
}
