<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App;

class ParseRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $parseRequests = App\ParseRequest::all();
	    foreach ($parseRequests as $parseRequest) {
		    App\ParseRequest::destroy($parseRequest->id);
		    \Artisan::call('email:parse', ['period' => $parseRequest->period, '--period_type' => $parseRequest->period_type, '--email' => $parseRequest->email]);

	    }
    }



	private function runParseRequests()
	{

	}
}
