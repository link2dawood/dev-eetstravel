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
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request)
    {
        $title = 'Index - Currency Rate';
        $search = trim((string) $request->input('search'));
        // newest rates first; searchable by currency code or date
        $currency_rates = CurrencyRate::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('currency', 'like', "%{$search}%")->orWhere('date', 'like', "%{$search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('currency', 'asc')
            ->paginate(15)
            ->withQueryString();
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
        return view('currency_rate.create', compact('title'));
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
        CurrencyRate::findOrFail($id);

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
        CurrencyRate::findOrFail($id);
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
        $currency_rate = CurrencyRate::findOrfail($id);
        $this->validateCurrencyRate($request, $currency_rate);
        $currency_rate->currency = $request->get('currency');
        $currency_rate->rate = $request->get('rate');
        $currency_rate->date = $request->get('date');
        $currency_rate->save();

        LaravelFlashSessionHelper::setFlashMessage("Currency rate $currency_rate->currency edited", 'success');

        return redirect('currency_rate');
    }


    public function DeleteMsg($id, Request $request)
    {
        CurrencyRate::findOrFail($id);
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
        return DB::transaction(function () use ($id) {
            $currency_rate = CurrencyRate::query()->lockForUpdate()->findOrFail($id);
            $currency_rate->delete();
            Comment::query()->where('reference_type', Comment::$services['currency_rate'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Currency rate $currency_rate->currency deleted", 'success');
            return request()->expectsJson() ? response()->json(['route' => route('currency_rate.index')]) : redirect('currency_rate');
        });
    }

    public function validateCurrencyRate($request, CurrencyRate $rate = null){
        if (is_string($request->input('currency'))) $request->merge(['currency' => strtoupper(trim($request->input('currency')))]);
        $this->validate($request, [
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/', 'exists:currencies,code'],
            'rate' => ['required', 'numeric', 'gt:0', 'max:999999', 'regex:/^(?:0|[1-9][0-9]{0,5})(?:\.[0-9]{1,6})?$/'],
            'date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', \Illuminate\Validation\Rule::unique('currency_rates', 'date')->where(function ($query) use ($request) {
                $currency = is_string($request->input('currency')) ? $request->input('currency') : '';
                $query->whereRaw('UPPER(currency) = ?', [$currency]);
            })->ignore($rate ? $rate->id : null)],
        ]);
    }
}
