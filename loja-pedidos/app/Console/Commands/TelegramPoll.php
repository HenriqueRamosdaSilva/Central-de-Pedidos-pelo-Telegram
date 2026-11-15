<?php

namespace App\Console\Commands;

use App\Services\BotPedidos;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';

    protected $description = 'Escuta as mensagens do bot do Telegram (long polling)';

    public function handle(TelegramService $telegram, BotPedidos $bot): int
    {
        if (! config('telegram.token')) {
            $this->error('Defina TELEGRAM_BOT_TOKEN no arquivo .env.');

            return self::FAILURE;
        }

        $this->info('Bot escutando... (Ctrl+C para parar)');
        $offset = 0;

        while (true) {
            foreach ($telegram->buscarAtualizacoes($offset) as $update) {
                $offset = $update['update_id'] + 1;

                try {
                    $bot->processar($update);
                } catch (\Throwable $e) {
                    report($e);
                    $this->error($e->getMessage());
                }
            }
        }
    }
}
