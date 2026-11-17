<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'de' => ['nullable', 'date'],
            'ate' => ['nullable', 'date', 'after_or_equal:de'],
        ]);

        $de = ($request->filled('de') ? Carbon::parse($request->input('de')) : now()->startOfMonth())->startOfDay();
        $ate = ($request->filled('ate') ? Carbon::parse($request->input('ate')) : now())->endOfDay();

        $noPeriodo = fn () => Pedido::whereBetween('created_at', [$de, $ate]);
        $vendas = fn () => $noPeriodo()->where('status', '!=', 'cancelado');

        $totalPedidos = $vendas()->count();
        $faturamento = (float) $vendas()->sum('total');
        $ticketMedio = $totalPedidos > 0 ? $faturamento / $totalPedidos : 0;
        $taxasDeEntrega = (float) $vendas()->sum('taxa_entrega');
        $cancelados = $noPeriodo()->where('status', 'cancelado')->count();

        $porDia = $vendas()
            ->selectRaw('DATE(created_at) as dia, COUNT(*) as pedidos, SUM(total) as faturamento')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        $porPagamento = $vendas()
            ->selectRaw('forma_pagamento, COUNT(*) as pedidos, SUM(total) as faturamento')
            ->groupBy('forma_pagamento')
            ->orderByDesc('faturamento')
            ->get();

        $porTipo = $vendas()
            ->selectRaw('tipo, COUNT(*) as pedidos, SUM(total) as faturamento')
            ->groupBy('tipo')
            ->orderByDesc('faturamento')
            ->get();

        $maisVendidos = DB::table('itens_pedido')
            ->join('pedidos', 'pedidos.id', '=', 'itens_pedido.pedido_id')
            ->whereBetween('pedidos.created_at', [$de, $ate])
            ->where('pedidos.status', '!=', 'cancelado')
            ->selectRaw('itens_pedido.nome, SUM(itens_pedido.quantidade) as quantidade, SUM(itens_pedido.quantidade * itens_pedido.preco) as faturamento')
            ->groupBy('itens_pedido.nome')
            ->orderByDesc('quantidade')
            ->limit(10)
            ->get();

        $avaliacao = $vendas()
            ->whereNotNull('nota')
            ->selectRaw('AVG(nota) as media, COUNT(*) as total')
            ->first();

        $atalhos = [
            'Hoje' => ['de' => now()->toDateString(), 'ate' => now()->toDateString()],
            'Últimos 7 dias' => ['de' => now()->subDays(6)->toDateString(), 'ate' => now()->toDateString()],
            'Este mês' => ['de' => now()->startOfMonth()->toDateString(), 'ate' => now()->toDateString()],
            'Mês passado' => [
                'de' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                'ate' => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            ],
        ];

        return view('relatorios.index', compact(
            'de', 'ate', 'totalPedidos', 'faturamento', 'ticketMedio', 'taxasDeEntrega', 'cancelados',
            'porDia', 'porPagamento', 'porTipo', 'maisVendidos', 'avaliacao', 'atalhos'
        ));
    }
}
