<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $termo = trim((string) $request->input('busca', ''));
        $minimo = (int) Configuracao::atual()->estoque_minimo;
        $apenasBaixo = $request->boolean('baixo');

        $produtos = Produto::query()
            ->when($termo !== '', fn ($consulta) => $consulta->where('nome', 'ilike', '%' . $termo . '%'))
            ->when($apenasBaixo, fn ($consulta) => $consulta->where('ativo', true)->where('quantidade', '<=', $minimo))
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('produtos.index', compact('produtos', 'termo', 'minimo', 'apenasBaixo'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        Produto::create($this->validar($request));

        return redirect()->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $produto->update($this->validar($request, $produto));

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso.');
    }

    private function validar(Request $request, ?Produto $produto = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:150', Rule::unique('produtos', 'nome')->ignore($produto)],
            'descricao' => ['required', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'ativo' => ['required', 'boolean'],
        ]);
    }
}
