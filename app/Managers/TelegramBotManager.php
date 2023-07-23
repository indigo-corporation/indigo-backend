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
        $link = 'https://indigofilms.online/' . $film->category . '/' . $film->slug
            . '?utm_source=social&utm_medium=tg_bot&utm_campaign=indigofilms';

//        $this->bot->sendMessage($chatId, $link);

        $keyboard = array(
            'inline_keyboard' => array(
                array(
                    array('text' => 'Смотреть', 'url' => $link)
                )
            )
        );

        $replyMarkup = urlencode(json_encode($keyboard));

        $overview = strlen($film->overview) < 250
            ? $film->overview
            : mb_substr($film->overview, 0, 250) . '...';

        $caption = $film->title . PHP_EOL . PHP_EOL . $overview;

        $this->bot->send($chatId, $film->poster_medium, 'photo', $caption, $replyMarkup);
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
