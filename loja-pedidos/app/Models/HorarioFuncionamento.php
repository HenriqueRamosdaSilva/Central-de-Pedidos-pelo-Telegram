<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HorarioFuncionamento extends Model
{
    protected $table = 'horarios_funcionamento';

    public const DIAS = [
        0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta',
        4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado',
    ];

    public const ORDEM = [1, 2, 3, 4, 5, 6, 0];

    protected $fillable = ['dia_semana', 'aberto', 'abre', 'fecha'];

    protected function casts(): array
    {
        return ['aberto' => 'boolean'];
    }

    public static function garantir(): Collection
    {
        if (self::count() < 7) {
            foreach (self::DIAS as $dia => $nome) {
                self::firstOrCreate(
                    ['dia_semana' => $dia],
                    ['aberto' => $dia !== 0, 'abre' => '08:00', 'fecha' => '18:00']
                );
            }
        }

        return self::orderBy('dia_semana')->get()->keyBy('dia_semana');
    }

    public static function abertoAgora(): bool
    {
        $agora = now();
        $hoje = self::garantir()->get($agora->dayOfWeek);

        if (! $hoje || ! $hoje->aberto) {
            return false;
        }

        $hora = $agora->format('H:i');

        return $hora >= substr($hoje->abre, 0, 5) && $hora < substr($hoje->fecha, 0, 5);
    }

    public static function proximaAbertura(): ?string
    {
        $agora = now();
        $horarios = self::garantir();

        for ($i = 0; $i <= 7; $i++) {
            $data = $agora->copy()->addDays($i);
            $h = $horarios->get($data->dayOfWeek);

            if (! $h || ! $h->aberto) {
                continue;
            }

            $abre = substr($h->abre, 0, 5);

            if ($i === 0 && $agora->format('H:i') >= $abre) {
                continue;
            }

            $quando = match (true) {
                $i === 0 => 'hoje',
                $i === 1 => 'amanhã',
                default => (in_array($data->dayOfWeek, [0, 6], true) ? 'no ' : 'na ') . mb_strtolower(self::DIAS[$data->dayOfWeek]),
            };

            return "{$quando} às {$abre}";
        }

        return null;
    }
    public static function resumo(): string
    {
        $horarios = self::garantir();

        return collect(self::ORDEM)->map(function ($dia) use ($horarios) {
            $h = $horarios[$dia];
            $nome = self::DIAS[$dia];

            return $h->aberto
                ? "{$nome}: " . substr($h->abre, 0, 5) . ' às ' . substr($h->fecha, 0, 5)
                : "{$nome}: fechado";
        })->implode("\n");
    }
}