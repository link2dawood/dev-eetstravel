<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Holidaycalendarday;
use App\TourPackage;
use Illuminate\Http\Request;
use URL;
use View;
use Yajra\Datatables\Datatables;

class HolidayController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function getButton($id, $holidaycalendarday)
    {
        $url = array('show'       => route('holiday.show', ['id' => $id]),
            'edit'       => route('holiday.edit', ['id' => $id]),
            'delete_msg' => "/holiday/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $holidaycalendarday);
    }

    public function data(Request $request)
    {
        
        return Datatables::of(Holidaycalendarday::where('user_id', \Illuminate\Support\Facades\Auth::id())->get()
        )
            ->addColumn('action', function ($holidaycalendarday) {
                return $this->getButton($holidaycalendarday->id, $holidaycalendarday);
            })
            ->rawColumns(['action'])

            ->make(true);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - Calendar holidays';
        $holidaycalendarday = \App\Holidaycalendarday::query()->orderBy('start_time', 'asc')->paginate(10);
        return view('holidaycalendar.index', compact('holidaycalendarday', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Holiday';
        
        return view('holidaycalendar.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateCalendar($request);
        $holidaycalendarday                     = new Holidaycalendarday();
        $holidaycalendarday->user_id            = \Illuminate\Support\Facades\Auth::id();
        $holidaycalendarday->name               = $request->get('name');
        $holidaycalendarday->start_time         = $request->get('start_time');
        $holidaycalendarday->backgroundcolor    = $request->get('backgroundcolor', null);
        $holidaycalendarday->status             = 'active';
        $holidaycalendarday->save();

        LaravelFlashSessionHelper::setFlashMessage("Holiday $holidaycalendarday->name created", 'success');

        return $request->get('modal_create') == 1 ?  redirect(URL::to('home')) : redirect(URL::to('holiday'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - holidays';

        $holidaycalendarday = Holidaycalendarday::findOrfail($id);
        if($holidaycalendarday == null){
            return abort(404);
        }

        return view('holidaycalendar.show', compact('title', 'holidaycalendarday'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - holidays';
        if ($request->ajax()) {
            return URL::to('holiday/' . $id . '/edit');
        }

        $holidaycalendarday = Holidaycalendarday::findOrfail($id);
        
        return view('holidaycalendar.edit', compact('title', 'holidaycalendarday'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateCalendar($request);

        $holidaycalendarday     = Holidaycalendarday::findOrfail($id);
        $holidaycalendarday->user_id            = \Illuminate\Support\Facades\Auth::id();;
        $holidaycalendarday->name               = $request->get('name');
        $holidaycalendarday->start_time         = $request->get('start_time');
        $holidaycalendarday->backgroundcolor    = $request->get('backgroundcolor', null);
        $holidaycalendarday->status             = 'active';        
        $holidaycalendarday->save();
        
        
        LaravelFlashSessionHelper::setFlashMessage("Holiday $holidaycalendarday->name edited", 'success');

        return redirect(URL::to('holiday'));
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/holiday/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/holiday/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holidaycalendarday = Holidaycalendarday::query()->where('id', $id)->first();
        
        LaravelFlashSessionHelper::setFlashMessage("Holiday $holidaycalendarday->name deleted", 'success');
            $holidaycalendarday->delete();
            
            return URL::to('holiday');
    }

    public function validateCalendar($request){

        $this->validate($request, [
            'name'              => 'required',
            'start_time'        => 'required',
            'backgroundcolor'   => 'required'
        ]);
    }
}
