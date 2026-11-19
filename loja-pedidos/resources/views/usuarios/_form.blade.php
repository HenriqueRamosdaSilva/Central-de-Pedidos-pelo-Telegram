
@push('estilos')
<style>
    .form-usuario-card {
        background: #fff;
        border: 1px solid #e1e9ef;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 5px 18px rgba(25, 55, 75, .05);
        max-width: 760px;
    }

    .form-usuario-card .campo {
        margin-bottom: 20px;
    }

    .form-usuario-card label {
        display: block;
        margin: 0 0 8px;
        color: #294357;
        font-weight: 600;
        font-size: .94rem;
    }

    .form-usuario-card input,
    .form-usuario-card select {
        width: 100%;
        max-width: none;
        min-height: 46px;
        padding: 11px 14px;
        border: 1px solid #d5e0e8;
        border-radius: 10px;
        background: #fafdff;
        color: #243746;
        outline: none;
        transition: .2s;
    }

    .form-usuario-card input:focus,
    .form-usuario-card select:focus {
        border-color: #087e91;
        box-shadow: 0 0 0 3px rgba(8, 126, 145, .12);
        background: #fff;
    }

    .form-usuario-card .ajuda {
        display: block;
        margin-top: 7px;
        color: #778895;
        font-size: .83rem;
    }

    .form-usuario-card .acoes-formulario {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 28px;
    }

    .form-usuario-card .btn-salvar {
        border: none;
        border-radius: 11px;
        padding: 12px 24px;
        background: #087e91;
        color: #fff;
        font-weight: bold;
        cursor: pointer;
        transition: .2s;
    }

    .form-usuario-card .btn-salvar:hover {
        background: #066574;
        transform: translateY(-1px);
    }

    .form-usuario-card .btn-voltar {
        display: inline-block;
        padding: 12px 20px;
        border-radius: 11px;
        background: #edf3f7;
        color: #344c5e;
        text-decoration: none;
        font-weight: bold;
    }

    .form-usuario-card .btn-voltar:hover {
        background: #dce8ef;
    }

    @media (max-width: 600px) {
        .form-usuario-card {
            padding: 18px;
        }

        .form-usuario-card .acoes-formulario > * {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@csrf

<div class="form-usuario-card">

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <div class="campo">
        <label for="name">Nome completo</label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $usuario->name ?? '') }}"
            placeholder="Digite o nome do usuário"
        >
    </div>

    <div class="campo">
        <label for="email">E-mail</label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $usuario->email ?? '') }}"
            placeholder="exemplo@email.com"
        >
    </div>

    <div class="campo">
        <label for="perfil">Perfil de acesso</label>

        <select id="perfil" name="perfil">

            <option
                value="atendente"
                @selected(old('perfil', $usuario->perfil ?? 'atendente') === 'atendente')
            >
                Atendente (só pedidos)
            </option>

            <option
                value="admin"
                @selected(old('perfil', $usuario->perfil ?? 'atendente') === 'admin')
            >
                Administrador (acesso total)
            </option>

        </select>

        <small class="ajuda">
            Escolha quais áreas do painel esse usuário poderá acessar.
        </small>
    </div>

    <div class="campo">
        <label for="password">
            Senha
            {{ isset($usuario) ? '(deixe em branco para manter a atual)' : '(mínimo 8 caracteres)' }}
        </label>

        <input
            type="password"
            id="password"
            name="password"
            autocomplete="new-password"
            placeholder="Digite a senha"
        >
    </div>

    <div class="campo">
        <label for="password_confirmation">Confirmar senha</label>

        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            autocomplete="new-password"
            placeholder="Digite novamente a senha"
        >
    </div>

    <div class="acoes-formulario">

        <button type="submit" class="btn-salvar">
            Salvar usuário
        </button>

        <a href="{{ route('usuarios.index') }}" class="btn-voltar">
            Voltar
        </a>

    </div>

</div>
