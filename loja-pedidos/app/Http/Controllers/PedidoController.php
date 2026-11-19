<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\HorarioFuncionamento;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\NotificacaoPedidos;
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

        $config = Configuracao::atual();
        $dentroDoHorario = HorarioFuncionamento::dentroDoHorario();
        $estoqueBaixo = Produto::where('ativo', true)->where('quantidade', '<=', $config->estoque_minimo)->count();

        return view('pedidos.index', compact('pedidos', 'status', 'config', 'dentroDoHorario', 'estoqueBaixo'));
    }

    public function atualizarStatus(Request $request, Pedido $pedido, NotificacaoPedidos $avisos)
    {
        $dados = $request->validate([
            'status' => ['required', 'in:em_preparo,pronto,entregue,cancelado'],
        ]);

        $atualizado = Pedido::mudarStatus($pedido->id, $dados['status'], Pedido::ORIGENS[$dados['status']]);

        if (! $atualizado) {
            return back()->with('success', 'Este pedido já foi atualizado e não pode mais mudar para esse status.');
        }

        $avisos->avisarCliente($atualizado);

        return back();
    }
}
