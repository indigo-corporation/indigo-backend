<?php

namespace App\Services;

class TelegramBot
{
    public function __construct(private string $token) {}

    public function sendMessage($chatID = 244260227, $message = 'test', $reply_markup = [])
    {
        $url = "sendMessage?chat_id=" . $chatID;
        $url .= "&text=" . urlencode($message);

        if ($reply_markup) {
            $url = $url . "&reply_markup=" . $reply_markup;
        }

        return $this->curl($url);
    }

    public function send($chatID, $content, $type, $caption = null, $reply_markup = [])
    {
        switch ($type):
            case 'text':
                $action = 'sendMessage';
                break;
            case 'photo':
                $action = 'sendPhoto';
                break;
            case 'video':
                $action = 'sendVideo';
                break;
            case 'audio':
                $action = 'sendAudio';
                break;
            case 'voice':
                $action = 'sendVoice';
                break;
            case 'document':
                $action = 'sendDocument';
                break;
            case 'media':
                $action = 'sendMediaGroup';
                break;
            default:
                die();
        endswitch;

        $content = (is_string($content))
            ? urlencode($content)
            : json_encode($content);

        $url = $action . "?chat_id=" . $chatID;
        $url .= "&" . $type . "=" . $content;
        $url .= ($caption) ? "&caption=" . urlencode($caption) : '';

        if ($reply_markup) {
            $url = $url . "&reply_markup=" . $reply_markup;
        }

        return $this->curl($url);
    }

    public function deleteMessage($chat_id, $mess_id)
    {
        $url = "deleteMessage?chat_id=" . $chat_id
            . "&message_id=" . $mess_id;

        return $this->curl($url);
    }

    public function editMessageCaption($chat_id, $mess_id, $text, $reply_markup = [])
    {
        $url = "editMessageCaption?chat_id=" . $chat_id
            . "&message_id=" . $mess_id
            . "&caption=" . urlencode($text);

        if ($reply_markup) {
            $url = $url . "&reply_markup=" . $reply_markup;
        }

        return $this->curl($url);
    }

    public function editMessage($chat_id, $mess_id, $text, $reply_markup = [], $type = 'caption')
    {
        $url = "editMessage" . ucfirst($type) . "?chat_id=" . $chat_id
            . "&message_id=" . $mess_id
            . "&" . $type . "=" . urlencode($text);

        if ($reply_markup) {
            $url = $url . "&reply_markup=" . $reply_markup;
        }

        return $this->curl($url);
    }

    public function getKeyboard($butts)
    {
        $inline_keyboard = [];

        foreach ($butts as $key => $butt) {
            $i = ($key) ? floor($key / 3) : 0;
            $i = (int)$i;
            $key = $key - $i * 3;

            $inline_keyboard[$i][$key]['text'] = $butt['text'];
            $inline_keyboard[$i][$key]['callback_data'] = $butt['value'];
        }

        $keyboard = [
            "resize_keyboard" => true,
            "inline_keyboard" => $inline_keyboard,
        ];

        return json_encode($keyboard);
    }

    private function curl($url)
    {
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token . '/' . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['HTTP/1.1 200 OK']
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
