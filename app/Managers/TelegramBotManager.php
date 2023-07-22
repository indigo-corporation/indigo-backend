<?php

namespace App\Managers;


use App\Models\Film\Film;
use App\Services\TelegramBot;

class TelegramBotManager
{

    private TelegramBot $bot;

    public function __construct()
    {
        $this->bot = new TelegramBot(env('TELEGRAM_TOKEN'));
    }

    public function sendFilmLink(Film $film, int $chatId): void
    {
        $link = 'https://indigofilms.online/' . $film->category . '/' . $film->slug;

        $this->bot->sendMessage($chatId, $link);
    }

    public function sendErrorIdMessage(int $chatId): void
    {
        $this->bot->sendMessage($chatId, 'Неккоректный код фильма, проверьте ещё раз');
    }

    public function sendHello(int $chatId): void
    {
        $this->bot->sendMessage($chatId, 'Здравствуйте! Можете отправить мне код фильма и я кину вам ссылку)');
    }

    public function sendTest(string $message): void
    {
        $this->bot->sendMessage(null, $message);
    }
}
