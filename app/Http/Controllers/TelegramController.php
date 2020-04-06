<?php

namespace App\Http\Controllers;

use React\EventLoop\Factory;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\TgLog;

class TelegramController extends Controller
{
    public static function broadcastMessage($message)
    {
        $loop = Factory::create();
        $tgLog = new TgLog(env('TELEGRAM_KEY', null), new HttpClientRequestHandler($loop));
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = env('TELEGRAM_BROADCAST_ID', null);
        $sendMessage->text = $message;
        $sendMessage->parse_mode = 'html';
        $promise = $tgLog->performApiRequest($sendMessage);
        $promise->then(function ($response) {

        }, function (\Exception $exception) {
            echo 'Exception ' . get_class($exception) . ' caught, message: ' . $exception->getMessage();
        }
        );
        $loop->run();
    }
}
