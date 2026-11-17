<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\TelegramService;
use Illuminate\Http\Request;

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

        $atualizado = Pedido::mudarStatus($pedido->id, $dados['status']);

        if (! $atualizado) {
            return back()->with('success', 'Este pedido já foi finalizado e não pode mais ser alterado.');
        }

        $chat = $atualizado->cliente->telegram_id;
        $mensagem = $atualizado->mensagemDoStatus();

        if ($atualizado->status === 'entregue') {
            $telegram->enviar(
                $chat,
                $mensagem . "\n\nComo foi o seu pedido? Toque numa nota de 1 a 5:",
                $atualizado->botoesAvaliacao()
            );
        } else {
            $telegram->enviar($chat, $mensagem);
        }

        return back();
    }
}
