<?php

namespace App\Http\Controllers;

use App\Hotel;
use App\Seasons;
use App\SeasonsPricesRoomTypeHotels;
use App\SeasonTypes;
use App\HotelAgreementsRoomTypeHotels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helper\HelperTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Country;
use App\City;

use Illuminate\Support\Facades\DB;
use PDF;


class ExportController extends Controller
{
    use HelperTrait;

    /**
     * export all data for services index(Export All)
     * @param  Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $exp = $request->type;
        $search = $request->search;
        $service = $this->createNamespace($request->service_name);
        $columns = json_decode($request->column);
        $table = with(new $service)->getTable();
        if ($request->service_name === 'Flight' || $request->service_name === 'Cruises') {
            $data = $this->getAnotherData($table, $search);
        } elseif($request->service_name === 'Hotel'){
            $data = $this->getDataHotel($table, $search);
        }else {
            $data = $this->getData($table, $search);
        }
        $data = json_decode(json_encode($data), true);
        if ($exp !== 'pdf') {
            return $this->exportExcelCsv($service, $data, $exp);
        };
        ini_set('memory_limit', '800M');
        $service_length = count($data);
        if ($service_length > 300) return back()->with('export_all', 'Please, choose less items to export.');
        view()->share('data', $data);
        PDF::setOptions(['isHtml5ParserEnabled' => true]);
        $pdf = PDF::loadView('component.pdf_export_services');
        return $pdf->download($request->service_name === 'Cruises' ? $request->service_name . '.pdf' : $request->service_name . 's.pdf');
    }

    public function exportSeasons(Request $request)
    {
        ini_set('memory_limit', '800M');
        set_time_limit(0);
        $data = Hotel::distinct()->leftJoin('countries', 'countries.alias', '=', 'hotels.country')
            ->leftJoin('cities', 'cities.id', '=', 'hotels.city')
            ->select(
                [
                    'hotels.id',
                    'hotels.name',
                    'hotels.address_first',
                    'cities.name as city',
                    'countries.name as country',
                    'hotels.work_phone',
                    'hotels.contact_email'
                ]
            )->whereNull('hotels.deleted_at')->orderBy('id');

        $exp = $request->type;

        $cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize' => '800M');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        return Excel::create("seasons", function ($excel) use ($data) {
            $excel->sheet('hotels seasons prices', function ($sheet) use ($data) {

                $sheet->appendRow(array(
                    'id', 'Name', 'Address', 'Country', 'Work Phone', 'Contact Email'
                ));

                $sheet->setWidth('A', 20);
                $sheet->setWidth('B', 20);
                $sheet->setWidth('C', 20);
                $sheet->setWidth('D', 20);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 20);

                $data->chunk(100, function ($rows) use ($sheet) {
                    foreach ($rows as $row) {
                        $sheet->appendRow(array(
                            $row->id, $row->name, $row->address_first, $row->country, $row->work_phone, $row->contact_email
                        ));

                        $sheet->appendRow(array(
                            '', 'id', 'Name', 'From date', 'To date', 'Type'
                        ));

                        if (count($row->seasons) == 0) {

                            $seasons_types = SeasonTypes::all();

                            foreach ($seasons_types as $season_type) {

                                $season = new Seasons();
                                $season->hotel_id = $row->id;
                                $season->name = $season_type->name;
                                $season->type = $season_type->id;

                                switch ($season_type->name) {
	                                case 'Low' :
	                                	$season->start_date = Carbon::now()->format('Y').'-11-01';
	                                	$season->end_date = Carbon::now()->format('Y').'-04-15';
	                                	break;
	                                case 'Middle' :
		                                $season->start_date = Carbon::now()->format('Y').'-04-16';
		                                $season->end_date = Carbon::now()->format('Y').'-06-15';
		                                break;
	                                case 'Extra' :
		                                $season->start_date = Carbon::now()->format('Y').'-06-16';
		                                $season->end_date = Carbon::now()->format('Y').'-09-15';
		                                break;
	                                case 'High' :
		                                $season->start_date = Carbon::now()->format('Y').'-09-16';
		                                $season->end_date = Carbon::now()->format('Y').'-10-31';
		                                break;
	                                default :
		                                $season->start_date = '';
		                                $season->end_date =  '';
                                }

                                $season->save();

                                $sheet->appendRow(array(
                                    '', $season->id, $season->name, $season->start_date, $season->end_date, $season_type->name
                                ));

                                $sheet->appendRow(array(
                                    '', '', '', 'id', 'Room', 'Price'
                                ));

                                $room = new SeasonsPricesRoomTypeHotels();
                                $room->season_id = $season->id;
                                $room->count = 0; //0
                                $room->room_type_id = 2; //double
                                $room->price = 0; //price 0
                                $room->save();

                                $sheet->appendRow(array(
                                    '', '', '', $room->id, 'Double', $room->price
                                ));
                            }

                        } else {

                            foreach ($row->seasons as $season) {

                                ($season->getType($season->type)) ? $type = $season->getType($season->type)->name : $type = '';

                                $sheet->appendRow(array(
                                    '', $season->id, $season->name, $season->start_date, $season->end_date, $type
                                ));

                                $sheet->appendRow(array(
                                    '', '', '', 'id', 'Room', 'Price'
                                ));

                                foreach ($season->seasons_room_types as $item) {
                                    $sheet->appendRow(array(
                                        '', '', '', $item->id, $season->getRoom($item->room_type_id)->name, $item->price
                                    ));
                                }

                            }

                        }

                    }
                });
            });

        })->download($exp);
    }

    /**
     * export to excel or csv
     * @param  $service service namespace
     * @param  $data    data for export
     * @param  $exp     extension for file
     * @return mixed
     */
    public function exportExcelCsv($service, $data, $exp)
    {
        return Excel::create("{$service}", function ($excel) use ($data) {
            $excel->sheet('Service', function ($sheet) use ($data) {
                // $sheet->row(1, function($row) use ($sheet){

                // // call cell manipulation methods
                // $sheet->getColumnDimension($row)->setAutoSize(true);

                // });


                $sheet->fromArray($data);
            });
        })->export($exp);
    }

    /**
     * get base data for service
     * @param  $table  name of service
     * @param  $search search value
     * @return mixed
     */
    public function getData($table, $search)
    {
        $datas = DB::table($table)->leftJoin('cities', "{$table}.city", '=', 'cities.id')
            ->leftJoin('countries', "{$table}.country", '=', 'countries.alias')
            ->where("{$table}.name", 'like', "%{$search}%")
            ->orWhere("{$table}.address_first", 'like', "%{$search}%")
            ->orWhere('cities.name', 'like', "%{$search}%")
            ->orWhere('countries.name', 'like', "%{$search}%")
            ->select("{$table}.*", 'cities.name as city', 'countries.name as country')
            ->orderBy("${table}.id")
            ->get();
        $data = [];
        foreach ($datas as $d) {
            if (!$d->deleted_at) array_push($data, $d);
        }
        return $data;
    }

    /**
     * get data for services that have different fields compare to base services
     * @param  $table  name of service
     * @param  $search search value for columns
     * @return mixed
     */
    public function getAnotherData($table, $search)
    {
        return $datas = DB::table($table)
            ->leftJoin('cities as to', "{$table}.city_to", '=', 'to.id')
            ->leftJoin('cities as from', "{$table}.city_from", '=', 'from.id')
            ->leftJoin('countries as ct_to', "{$table}.country_from", '=', 'ct_to.alias')
            ->leftJoin('countries as ct_from', "{$table}.country_to", '=', 'ct_from.alias')
            ->where("{$table}.name", 'like', "%{$search}%")
            ->orWhere("ct_from.name", 'like', "%{$search}%")
            ->orWhere('ct_to.name', 'like', "%{$search}%")
            ->orWhere('to.name', 'like', "%{$search}%")
            ->orWhere('from.name', 'like', "%{$search}%")
            ->select("{$table}.*", 'to.name as city_to', 'from.name as city_from', 'ct_to.name as country_to', 'ct_from.name as country_from')->get();
        // $data = [];
        // foreach ($datas as $d) {
        //     if (!$d->deleted_at) array_push($data, $d);
        // }
        // return $data;
    }

    public function getDataHotel($table, $search)
    {
        $datas = DB::table($table)->leftJoin('cities', "{$table}.city", '=', 'cities.id')
            ->leftJoin('countries', "{$table}.country", '=', 'countries.alias')
            ->where("{$table}.name", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.address_first", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.work_email", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.work_phone", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.work_fax", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.contact_name", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.contact_phone", 'LIKE', "%{$search}%")
            ->orWhere("{$table}.contact_email", 'LIKE', "%{$search}%")
            ->orWhere('cities.name', 'LIKE', "%{$search}%")
            ->orWhere('countries.name', 'LIKE', "%{$search}%")
            ->select("{$table}.*", 'cities.name as city', 'countries.name as country')
            ->orderBy("${table}.id")
            ->get();
        $data = [];
        foreach ($datas as $d) {
           if (!$d->deleted_at) array_push($data, $d);
        }
        return $data;
    }
}
