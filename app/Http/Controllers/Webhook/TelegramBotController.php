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

        $data = json_decode($json);
        \Log::info('telegram', [
            'data' => $data
        ]);

        $message = $data->message;
        $from_id = $message->from->id;
        $text = $message->text;

        if ($text === '/start') {
            $this->telegramManager->sendHello($from_id);
            return;
        }

        if (filter_var($text, FILTER_VALIDATE_INT)) {
            $film = Film::find($text);

            if ($film) {
                $this->telegramManager->sendFilmLink($film, $from_id);
                return;
            }
        }

        $this->telegramManager->sendErrorIdMessage($from_id);
    }
}
