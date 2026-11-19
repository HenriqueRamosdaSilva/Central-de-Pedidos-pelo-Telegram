<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AvaliacaoController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'nota' => ['nullable', 'integer', 'between:1,5'],
            'de' => ['nullable', 'date'],
            'ate' => ['nullable', 'date', 'after_or_equal:de'],
        ]);

        // Período opcional (vazio = todas as avaliações). Vale a data em que o pedido foi feito.
        $de = $request->filled('de') ? Carbon::parse($request->input('de'))->startOfDay() : null;
        $ate = $request->filled('ate') ? Carbon::parse($request->input('ate'))->endOfDay() : null;

        $avaliados = fn () => Pedido::whereNotNull('nota')
            ->when($de, fn ($q) => $q->where('created_at', '>=', $de))
            ->when($ate, fn ($q) => $q->where('created_at', '<=', $ate));

        $comComentario = fn ($q) => $q->whereNotNull('comentario_avaliacao')->where('comentario_avaliacao', '!=', '');

        // Resumo do período (não muda com o filtro de nota).
        $total = $avaliados()->count();
        $media = (float) $avaliados()->avg('nota');
        $totalComComentario = $avaliados()->where($comComentario)->count();
        $distribuicao = $avaliados()->selectRaw('nota, COUNT(*) as total')->groupBy('nota')->pluck('total', 'nota');

        // Lista com os filtros.
        $avaliacoes = $avaliados()
            ->when($request->filled('nota'), fn ($q) => $q->where('nota', (int) $request->input('nota')))
            ->when($request->boolean('comentario'), $comComentario)
            ->with(['cliente', 'itens'])
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('avaliacoes.index', compact(
            'avaliacoes', 'total', 'media', 'totalComComentario', 'distribuicao'
        ));
    }
}
