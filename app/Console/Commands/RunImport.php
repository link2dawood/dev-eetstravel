<?php

namespace App\Console\Commands;

use App;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use App\SeasonsPricesRoomTypeHotels;

class RunImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'season:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Hotels with Seasons and Prices from Excel';

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
        echo "------------ IMPORTING ----------" . Carbon::now()->format('Y-m-d H:i:00') . "\r\n";

        $parseFrequency = App\Setting::where('name', 'hotel_seasons_date')->first();

        if (Carbon::now()->format('Y-m-d H:i:00') !== $parseFrequency->value) {
            echo "FALSE" . $parseFrequency->value . "\r\n";
            return false;
        }

        echo "------------ PARSING ---------- AT " . $parseFrequency->value . "\r\n";

        $contents = storage_path('app/public/seasons.xlsx');

        Excel::load($contents, function ($reader) {
            foreach ($reader->toArray() as $i => $row) {
                if(is_float($row['country'])){
                    //echo $row['country']."\n\r";
                            try {
                                $room = SeasonsPricesRoomTypeHotels::findOrfail($row['country']);
                                $room->price = $row['contact_email'];
                                $room->save();
                            }
                                catch ( ModelNotFoundException $e ) {
                            }
                }
            }
        });

        echo "------------ END ----------" . Carbon::now() . "\r\n";
    }

}
