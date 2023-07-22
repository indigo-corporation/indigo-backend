<?php

namespace App\Managers;


use App\Models\Film\Film;
use App\Services\TelegramBot;

class TelegramBotManager {

    private TelegramBot $bot;

    public function __construct()
    {
        $this->bot = new TelegramBot(env('TELEGRAM_TOKEN'));
    }

    public function sendFilmLink(Film $film, int $chatId): void
    {
        $this->bot->sendMessage($chatId, $film->slug);
    }

    public function sendErrorIdMessage(int $chatId) :void
    {
        $this->bot->sendMessage($chatId, 'Неккоректный код фильма, проверьте ещё раз');
    }

    public function sendHello(int $chatId) :void
    {
        $this->bot->sendMessage($chatId, 'Здравствуйте! Можете отправить мне код фильма и я кину вам ссылку)');
    }
}
