<?php

namespace App\Console\Commands;

use Anthropic\Anthropic;
use Illuminate\Console\Command;

class AskClaude extends Command
{
    protected $signature = 'claude:ask {question}';
    protected $description = 'Ask Claude a question';

    public function handle()
    {
        $question = $this->argument('question');
        
        $client = new Anthropic([
            'apiKey' => env('ANTHROPIC_API_KEY'),
        ]);
        
        $response = $client->messages()->create([
            'model' => 'claude-opus-4-1-20250805',
            'max_tokens' => 2048,
            'messages' => [
                ['role' => 'user', 'content' => $question],
            ],
        ]);
        
        $this->info($response->content[0]->text);
    }
}