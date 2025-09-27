<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\CurrencyRate;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Illuminate\Http\Request;
use URL;
use Yajra\Datatables\Datatables;

class CurrencyRateController extends Controller
{
    use FileTrait;


    public function getButton($id, $currency_rate)
    {
        $url = array('show'       => route('currency_rate.show', ['currency_rate' => $id]),
            'edit'       => route('currency_rate.edit', ['currency_rate' => $id]),
            'delete_msg' => "/currency_rate/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $currency_rate);
    }

    public function data(Request $request)
    {
        return Datatables::of(CurrencyRate::query())
            ->addColumn('action', function ($currency_rate) {
                return $this->getButton($currency_rate->id, $currency_rate);
            })
            ->rawColumns(['action'])

            ->make(true);
    }

    /**
     * TaskController constructor.
     */
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->middleware('permissions.required');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - Currency Rate';
        $currency_rates = CurrencyRate::paginate(10);
        return view('currency_rate.index', compact('currency_rates', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Currency Rate';
        $currency_rates = CurrencyRate::all();

        return view('currency_rate.create', compact('title', 'currency_rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateCurrencyRate($request);
        $currency_rate = new CurrencyRate();
        $currency_rate->currency = $request->get('currency');
        $currency_rate->rate = $request->get('rate');
        $currency_rate->date = $request->get('date');
        $currency_rate->save();

        LaravelFlashSessionHelper::setFlashMessage("Currency rate $currency_rate->currency created", 'success');

        return $request->get('modal_create') == 1 ? redirect('home') : redirect('currency_rate');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Currency Rate';

        if ($request->ajax()) {
            return URL::to('currency_rate/' . $id);
        }

        $currency_rate = CurrencyRate::findOrfail($id);
        if($currency_rate == null){
            return abort(404);
        }
        return view('currency_rate.show', compact('title', 'currency_rate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Currency Rate';
        if ($request->ajax()) {
            return URL::to('currency_rate/' . $id . '/edit');
        }

        $currency_rate = CurrencyRate::findOrfail($id);

        return view('currency_rate.edit', compact('title', 'currency_rate'));
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
        $this->validateCurrencyRate($request);

        $currency_rate = CurrencyRate::findOrfail($id);
        $currency_rate->currency = $request->get('currency');
        $currency_rate->rate = $request->get('rate');
        $currency_rate->date = $request->get('date');
        $currency_rate->save();

        LaravelFlashSessionHelper::setFlashMessage("Currency rate $currency_rate->currency edited", 'success');

        return redirect('currency_rate');
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/currency_rate/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/currency_rate/' . $id . '/delete');

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
        $currency_rate = CurrencyRate::findOrfail($id);
        $currency_rate->delete();
        Comment::query()->where('reference_type', Comment::$services['currency_rate'])->where('reference_id', $id)->delete();

        LaravelFlashSessionHelper::setFlashMessage("Currency rate $currency_rate->currency deleted", 'success');
        return URL::to('currency_rate');
    }

    public function validateCurrencyRate($request){

        $this->validate($request, [
            'currency'    => 'required',
            'rate'    => 'required|numeric',
            'date' => 'required|date'
        ]);
    }
}
