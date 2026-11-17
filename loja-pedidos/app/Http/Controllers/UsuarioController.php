<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'perfil' => ['required', 'in:admin,atendente'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->salvar(new User(), $dados);

        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario)],
            'perfil' => ['required', 'in:admin,atendente'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($this->ehUltimoAdmin($usuario) && $dados['perfil'] !== 'admin') {
            return back()->withInput()->withErrors(['perfil' => 'Precisa existir pelo menos um administrador.']);
        }

        $this->salvar($usuario, $dados);

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->is(auth()->user())) {
            return back()->withErrors(['usuario' => 'Você não pode excluir o seu próprio usuário.']);
        }

        if ($this->ehUltimoAdmin($usuario)) {
            return back()->withErrors(['usuario' => 'Precisa existir pelo menos um administrador.']);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso.');
    }

    private function ehUltimoAdmin(User $usuario): bool
    {
        return $usuario->perfil === 'admin' && User::where('perfil', 'admin')->count() <= 1;
    }

    private function salvar(User $usuario, array $dados): void
    {
        $usuario->name = $dados['name'];
        $usuario->email = $dados['email'];
        $usuario->perfil = $dados['perfil'];

        if (! empty($dados['password'])) {
            $usuario->password = Hash::make($dados['password']);
        }

        $usuario->save();
    }
}
