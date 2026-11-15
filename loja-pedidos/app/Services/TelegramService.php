<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    private function api(string $metodo, array $dados = [], int $timeout = 10): Response
    {
        $url = 'https://api.telegram.org/bot' . config('telegram.token') . '/' . $metodo;

        return Http::timeout($timeout)->post($url, $dados);
    }

    public function enviar(int $chat, string $texto, array $botoes = []): void
    {
        $dados = ['chat_id' => $chat, 'text' => $texto];

        if ($botoes) {
            $dados['reply_markup'] = ['inline_keyboard' => $botoes];
        }

        try {
            $this->api('sendMessage', $dados);
        } catch (ConnectionException $e) {
            report($e);
        }
    }

    public function responderCallback(string $id, ?string $texto = null): void
    {
        try {
            $this->api('answerCallbackQuery', array_filter([
                'callback_query_id' => $id,
                'text' => $texto,
            ]));
        } catch (ConnectionException $e) {
            report($e);
        }
    }

    public function buscarAtualizacoes(int $offset): array
    {
        try {
            $resposta = $this->api('getUpdates', ['offset' => $offset, 'timeout' => 25], 35);
        } catch (ConnectionException $e) {
            sleep(3);

            return [];
        }

        if ($resposta->failed()) {
            sleep(3);

            return [];
        }

        return $resposta->json('result') ?? [];
    }
}
