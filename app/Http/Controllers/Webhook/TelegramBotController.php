<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Managers\TelegramBotManager;
use App\Models\Film\Film;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function __construct(private TelegramBotManager $telegramManager) {}

    public function webhook(Request $request)
    {
        $json = file_get_contents('php://input');
        $this->telegramManager->sendTest($json);

        $data = json_decode($json, true);

        $message = $data['message'];
        $from_id = $message['from']['id'];

        if ($message === '/start') {
            $this->telegramManager->sendHello($from_id);
            return;
        }

        if (is_int($message)) {
            $film = Film::find($message);

            if ($film) {
                $this->telegramManager->sendFilmLink($film, $from_id);
            }
        }

        $this->telegramManager->sendErrorIdMessage($from_id);
    }
}
