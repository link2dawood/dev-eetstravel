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
use Illuminate\Support\Facades\DB;

class CurrenciesController extends Controller
{
    use FileTrait;



    public function getButton($id, $currency)
    {
        $url = array('show'       => route('currencies.show', ['currency' => $id]),
            'edit'       => route('currencies.edit', ['currency' => $id]),
            'delete_msg' => "/currencies/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $currency);
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
        $title = 'Index - Currencies';
        $search = trim((string) $request->input('search'));
        $currencies = Currencies::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();
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
        return view('currencies.create', compact('title'));
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
        Currencies::findOrFail($id);

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
        Currencies::findOrFail($id);
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
        DB::transaction(function () use ($request, $id) {
            $currency = Currencies::query()->lockForUpdate()->findOrFail($id);
            $this->validateCurrencies($request, $currency);
            $currency->name = $request->get('name');
            $currency->code = $request->get('code');
            $currency->symbol = $request->get('symbol');
            $currency->cent = $request->get('cent');
            $currency->symbol_cent = $request->get('symbol_cent');
            $currency->save();
            LaravelFlashSessionHelper::setFlashMessage("Currency $currency->name edited", 'success');
        });

        return redirect('currencies');
    }


    public function DeleteMsg($id, Request $request)
    {
        Currencies::findOrFail($id);
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
        return DB::transaction(function () use ($id) {
            $currency = Currencies::query()->lockForUpdate()->findOrFail($id);
            if ($currency->isInUse()) {
                $message = "Currency {$currency->code} is in use and cannot be deleted.";
                if (request()->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
                LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
                return redirect('currencies');
            }
            $currency->delete();
            Comment::query()->where('reference_type', Comment::$services['currencies'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Currency $currency->name deleted", 'success');
            return request()->expectsJson() ? response()->json(['route' => route('currencies.index')]) : redirect('currencies');
        });
    }

    public function validateCurrencies($request, Currencies $currency = null){
        if (is_string($request->input('code'))) $request->merge(['code' => strtoupper(trim($request->input('code')))]);
        $this->validate($request, [
            'name' => \App\Services\ValidationService::nameRules(),
            'code' => ['required', 'string', 'regex:/^[A-Z]{3}$/', function ($attribute, $value, $fail) use ($currency) {
                if (!is_string($value)) return;
                $query = Currencies::whereRaw('UPPER(code) = ?', [$value]);
                if ($currency) $query->where('id', '<>', $currency->id);
                if ($query->exists()) $fail('This currency code has already been taken.');
            }],
            'symbol' => \App\Services\ValidationService::nameRules(true, 10, 1),
            'cent' => \App\Services\ValidationService::nameRules(false, 10, 1),
            'symbol_cent' => 'nullable|integer|min:1|max:2147483647',
        ]);
        if ($currency && $request->code !== $currency->code && $currency->isInUse()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['code' => 'The code of a currency in use cannot be changed.']);
        }
    }
}
