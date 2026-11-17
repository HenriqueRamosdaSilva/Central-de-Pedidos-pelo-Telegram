<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\HorarioFuncionamento;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConfiguracaoController extends Controller
{
    public function edit()
    {
        return view('configuracoes.edit', [
            'config' => Configuracao::atual(),
            'horarios' => HorarioFuncionamento::garantir(),
            'dias' => HorarioFuncionamento::DIAS,
            'ordem' => HorarioFuncionamento::ORDEM,
        ]);
    }

    public function update(Request $request)
    {
        $dados = $request->validate([
            'taxa_entrega' => ['required', 'numeric', 'min:0'],
            'pedido_minimo' => ['required', 'numeric', 'min:0'],
            'horarios' => ['required', 'array'],
            'horarios.*.aberto' => ['nullable', 'boolean'],
            'horarios.*.abre' => ['required', 'date_format:H:i'],
            'horarios.*.fecha' => ['required', 'date_format:H:i'],
        ]);

        foreach ($dados['horarios'] as $dia => $h) {
            if (! empty($h['aberto']) && $h['fecha'] <= $h['abre']) {
                throw ValidationException::withMessages([
                    "horarios.$dia.fecha" => 'Em ' . (HorarioFuncionamento::DIAS[$dia] ?? 'um dos dias') . ', o fechamento precisa ser depois da abertura.',
                ]);
            }
        }

        Configuracao::atual()->update([
            'taxa_entrega' => $dados['taxa_entrega'],
            'pedido_minimo' => $dados['pedido_minimo'],
        ]);

        foreach ($dados['horarios'] as $dia => $h) {
            HorarioFuncionamento::where('dia_semana', $dia)->update([
                'aberto' => ! empty($h['aberto']),
                'abre' => $h['abre'],
                'fecha' => $h['fecha'],
            ]);
        }

        return redirect()->route('configuracoes.edit')
            ->with('success', 'Configurações salvas.');
    }
}
