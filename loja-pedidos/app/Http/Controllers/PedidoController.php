<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'abertos');

        $consulta = Pedido::with(['cliente', 'itens']);

        if ($status === 'abertos') {
            $consulta->whereIn('status', ['novo', 'em_preparo', 'pronto']);
        } elseif (in_array($status, Pedido::STATUS, true)) {
            $consulta->where('status', $status);
        }

        $pedidos = $consulta->orderByDesc('id')->paginate(10)->withQueryString();

        return view('pedidos.index', compact('pedidos', 'status'));
    }

    public function atualizarStatus(Request $request, Pedido $pedido, TelegramService $telegram)
    {
        $dados = $request->validate([
            'status' => ['required', 'in:em_preparo,pronto,entregue,cancelado'],
        ]);

        $atualizado = DB::transaction(function () use ($pedido, $dados) {
            $atual = Pedido::with('itens')->lockForUpdate()->find($pedido->id);

            if (in_array($atual->status, ['entregue', 'cancelado'], true)) {
                return null;
            }

            if ($dados['status'] === 'cancelado') {
                foreach ($atual->itens as $item) {
                    if ($item->produto_id) {
                        Produto::whereKey($item->produto_id)->increment('quantidade', $item->quantidade);
                    }
                }
            }

            $atual->update(['status' => $dados['status']]);

            return $atual;
        });

        if (! $atualizado) {
            return back()->with('success', 'Este pedido já foi finalizado e não pode mais ser alterado.');
        }

        $telegram->enviar($atualizado->cliente->telegram_id, $atualizado->mensagemDoStatus());

        return back();
    }
}
