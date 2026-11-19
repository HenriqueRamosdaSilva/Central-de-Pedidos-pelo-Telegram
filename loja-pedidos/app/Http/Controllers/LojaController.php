<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;

class LojaController extends Controller
{
    public function alternarPausa()
    {
        $config = Configuracao::atual();
        $config->update(['loja_pausada' => ! $config->loja_pausada]);

        return back()->with('success', $config->loja_pausada
            ? 'Loja pausada: o bot não está aceitando pedidos.'
            : 'Loja reaberta: o bot volta a seguir o horário de funcionamento.');
    }
}
