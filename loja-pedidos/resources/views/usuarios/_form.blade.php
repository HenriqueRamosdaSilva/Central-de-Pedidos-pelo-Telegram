@csrf

@if ($errors->any())
    <ul class="erros">
        @foreach ($errors->all() as $erro)
            <li>{{ $erro }}</li>
        @endforeach
    </ul>
@endif

<label for="name">Nome</label>
<input type="text" id="name" name="name" value="{{ old('name', $usuario->name ?? '') }}">

<label for="email">E-mail</label>
<input type="email" id="email" name="email" value="{{ old('email', $usuario->email ?? '') }}">

<label for="perfil">Perfil</label>
<select id="perfil" name="perfil">
    <option value="atendente" @selected(old('perfil', $usuario->perfil ?? 'atendente') === 'atendente')>Atendente (só pedidos)</option>
    <option value="admin" @selected(old('perfil', $usuario->perfil ?? 'atendente') === 'admin')>Administrador (acesso total)</option>
</select>

<label for="password">Senha {{ isset($usuario) ? '(deixe em branco para manter a atual)' : '(mínimo 8 caracteres)' }}</label>
<input type="password" id="password" name="password" autocomplete="new-password">

<label for="password_confirmation">Confirmar senha</label>
<input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">

<p>
    <button type="submit">Salvar</button>
    <a href="{{ route('usuarios.index') }}">Voltar</a>
</p>
