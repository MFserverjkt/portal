<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId   = env('TELEGRAM_CHAT_ID');
    }

    public function sendMessage($message)
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::error('Telegram Error: TELEGRAM_BOT_TOKEN atau TELEGRAM_CHAT_ID di .env masih kosong!');
            return false;
        }

        try {
            // Gunakan verify(false) untuk menghindari masalah SSL di XAMPP/Windows
            $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id'    => $this->chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->failed()) {
                Log::error('Telegram API Error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram Exception: ' . $e->getMessage());
            return false;
        }
    }
}