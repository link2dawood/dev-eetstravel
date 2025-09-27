<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Currencies;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Illuminate\Http\Request;
use URL;

class CurrenciesController extends Controller
{
    use FileTrait;



    public function getButton($id, $currency_rate)
    {
        $url = array('show'       => route('currencies.show', ['currency' => $id]),
            'edit'       => route('currencies.edit', ['currency' => $id]),
            'delete_msg' => "/currencies/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $currency_rate);
    }

    public function data(Request $request)
    {
        $query = Currencies::query();

        // Get pagination parameters
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;

        // Get total count
        $total = $query->count();

        // Apply pagination
        $currencies = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Process each currency
        foreach($currencies as $currency) {
            $currency->action = $this->getButton($currency->id, $currency);
        }

        return response()->json([
            'data' => $currencies,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
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
        $title = 'Index - Currencies';
        $currencies = Currencies::paginate(10);
        return view('currencies.index', compact('currencies', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Currencies';
        $currencies = Currencies::all();

        return view('currencies.create', compact('title', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateCurrencies($request);
        $currencies = new Currencies();
        $currencies->name = $request->get('name');
        $currencies->code = $request->get('code');
        $currencies->symbol = $request->get('symbol');
        $currencies->cent = $request->get('cent');
        $currencies->symbol_cent = $request->get('symbol_cent');
        $currencies->save();

        LaravelFlashSessionHelper::setFlashMessage("Currency $currencies->name created", 'success');

        return $request->get('modal_create') == 1 ? redirect('home') : redirect('currencies');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Currencies';

        if ($request->ajax()) {
            return URL::to('currencies/' . $id);
        }

        $currencies = Currencies::findOrfail($id);
        if($currencies == null){
            return abort(404);
        }
        return view('currencies.show', compact('title', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Currencies';
        if ($request->ajax()) {
            return URL::to('currencies/' . $id . '/edit');
        }

        $currencies = Currencies::findOrfail($id);

        return view('currencies.edit', compact('title', 'currencies'));
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
        $this->validateCurrencies($request);

        $currencies = Currencies::findOrfail($id);
        $currencies->name = $request->get('name');
        $currencies->code = $request->get('code');
        $currencies->symbol = $request->get('symbol');
        $currencies->cent = $request->get('cent');
        $currencies->symbol_cent = $request->get('symbol_cent');
        $currencies->save();

        LaravelFlashSessionHelper::setFlashMessage("Currency $currencies->name edited", 'success');

        return redirect('currencies');
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/currencies/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/currencies/' . $id . '/delete');

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
        $currencies = Currencies::findOrfail($id);
        $currencies->delete();
        Comment::query()->where('reference_type', Comment::$services['currencies'])->where('reference_id', $id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Currency $currencies->name deleted", 'success');

        return URL::to('currencies');
    }

    public function validateCurrencies($request){

        $this->validate($request, [
            'name'    => 'required',
            'code'    => 'required',
            'symbol' => 'required'
        ]);
    }
}
