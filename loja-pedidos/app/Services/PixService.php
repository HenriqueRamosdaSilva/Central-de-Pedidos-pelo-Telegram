<?php

namespace App\Services;

use Illuminate\Support\Str;

// Gera o "Pix copia e cola" (BR Code estático, com valor), sem integração com banco.
class PixService
{
    public static function copiaECola(?string $chave, ?string $nome, ?string $cidade, float $valor): ?string
    {
        $chave = trim((string) $chave);
        $nome = self::limpar((string) $nome, 25);
        $cidade = self::limpar((string) $cidade, 15);

        if ($chave === '' || $nome === '' || $cidade === '' || $valor <= 0) {
            return null;
        }

        $conta = self::campo('00', 'BR.GOV.BCB.PIX') . self::campo('01', $chave);

        $payload = self::campo('00', '01')
            . self::campo('01', '12')
            . self::campo('26', $conta)
            . self::campo('52', '0000')
            . self::campo('53', '986')
            . self::campo('54', number_format($valor, 2, '.', ''))
            . self::campo('58', 'BR')
            . self::campo('59', $nome)
            . self::campo('60', $cidade)
            . self::campo('62', self::campo('05', '***'))
            . '6304';

        return $payload . self::crc16($payload);
    }

    // Formato de cada campo: id (2 dígitos) + tamanho (2 dígitos) + valor.
    private static function campo(string $id, string $valor): string
    {
        return $id . str_pad((string) strlen($valor), 2, '0', STR_PAD_LEFT) . $valor;
    }

    // Nome e cidade: sem acento, só letras, números e espaço, em maiúsculas.
    private static function limpar(string $texto, int $max): string
    {
        $texto = strtoupper(Str::ascii($texto));
        $texto = preg_replace('/[^A-Z0-9 ]/', '', $texto);

        return trim(substr($texto, 0, $max));
    }

    // CRC16/CCITT-FALSE (polinômio 0x1021, valor inicial 0xFFFF), exigido pelo padrão do Pix.
    private static function crc16(string $dados): string
    {
        $crc = 0xFFFF;

        for ($i = 0, $n = strlen($dados); $i < $n; $i++) {
            $crc ^= ord($dados[$i]) << 8;

            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
