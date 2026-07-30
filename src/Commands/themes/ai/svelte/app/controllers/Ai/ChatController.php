<?php

namespace App\Controllers\Ai;

use Anthropic\Client;
use Anthropic\Messages\RawContentBlockDeltaEvent;
use Anthropic\Messages\TextDelta;

class ChatController extends Controller
{
    public function show()
    {
        response()->inertia('ai');
    }

    public function chat()
    {
        $apiKey = _env('ANTHROPIC_API_KEY');

        if (!$apiKey) {
            return response()->status(500)->json([
                'error' => 'ANTHROPIC_API_KEY is not set. Add it to your .env file.',
            ]);
        }

        $data = request()->validate([
            'messages' => 'array',
        ]);

        if (!$data) {
            return response()->status(400)->json(['error' => 'messages array is required']);
        }

        // only keep the fields the API expects
        $messages = array_map(function ($message) {
            return [
                'role' => $message['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => (string) $message['content'],
            ];
        }, $data['messages']);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        $client = new Client(apiKey: $apiKey);

        try {
            $stream = $client->messages->createStream(
                model: 'claude-opus-5',
                maxTokens: 4096,
                system: 'You are a helpful assistant. Keep answers clear and reasonably short.',
                messages: $messages,
            );

            foreach ($stream as $event) {
                if ($event instanceof RawContentBlockDeltaEvent && $event->delta instanceof TextDelta) {
                    echo 'data: ' . json_encode(['text' => $event->delta->text]) . "\n\n";

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                }
            }
        } catch (\Throwable $th) {
            echo 'data: ' . json_encode(['error' => $th->getMessage()]) . "\n\n";
        }

        echo "data: [DONE]\n\n";
        exit;
    }
}
