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

    public function sendPhoto($photoPath, $caption)
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::error('Telegram Error: TELEGRAM_BOT_TOKEN atau TELEGRAM_CHAT_ID di .env masih kosong!');
            return false;
        }

        try {
            if (!file_exists($photoPath)) {
                return $this->sendMessage($caption); // Fallback ke teks biasa jika foto tidak ditemukan
            }

            $response = Http::withoutVerifying()->attach(
                'photo', file_get_contents($photoPath), basename($photoPath)
            )->post("https://api.telegram.org/bot{$this->botToken}/sendPhoto", [
                'chat_id'    => $this->chatId,
                'caption'    => $caption,
                'parse_mode' => 'HTML',
            ]);

            if ($response->failed()) {
                Log::error('Telegram API Photo Error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram Exception (Photo): ' . $e->getMessage());
            return false;
        }
    }
}