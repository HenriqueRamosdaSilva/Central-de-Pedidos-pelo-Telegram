@extends('layouts.app')

@section('titulo', 'Configurações')

@section('conteudo')
    <h1>Configurações da loja</h1>

    @if ($errors->any())
        <ul class="erros">
            @foreach ($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('configuracoes.update') }}" method="POST">
        @csrf
        @method('PUT')

        <h2>Entrega e pedido</h2>

        <label for="taxa_entrega">Taxa de entrega (R$). Use 0 para entrega grátis.</label>
        <input type="number" id="taxa_entrega" name="taxa_entrega" step="0.01" min="0"
               value="{{ old('taxa_entrega', $config->taxa_entrega) }}">

        <label for="pedido_minimo">Pedido mínimo (R$), sem contar a entrega. Use 0 para não ter mínimo.</label>
        <input type="number" id="pedido_minimo" name="pedido_minimo" step="0.01" min="0"
               value="{{ old('pedido_minimo', $config->pedido_minimo) }}">

        <h2>Horário de funcionamento</h2>
        <p>Fora desses horários o bot não finaliza pedidos. O fechamento precisa ser depois da abertura no mesmo dia.</p>

        <table>
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Aberto</th>
                    <th>Abre</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ordem as $dia)
                    @php $h = $horarios[$dia]; @endphp
                    <tr>
                        <td>{{ $dias[$dia] }}</td>
                        <td>
                            <input type="checkbox" name="horarios[{{ $dia }}][aberto]" value="1"
                                   @checked($errors->any() ? ! empty(old("horarios.$dia.aberto")) : $h->aberto)>
                        </td>
                        <td>
                            <input type="time" name="horarios[{{ $dia }}][abre]"
                                   value="{{ old("horarios.$dia.abre", substr($h->abre, 0, 5)) }}">
                        </td>
                        <td>
                            <input type="time" name="horarios[{{ $dia }}][fecha]"
                                   value="{{ old("horarios.$dia.fecha", substr($h->fecha, 0, 5)) }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><button type="submit">Salvar configurações</button></p>
    </form>
@endsection
