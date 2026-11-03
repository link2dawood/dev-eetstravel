<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\RateType;
use Illuminate\Http\Request;
use URL;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;
use Illuminate\Support\Facades\DB;


class RateController extends Controller
{
    use FileTrait;



    public function getButton($id, $rate)
    {
        $url = array('show'       => route('rate.show', ['rate' => $id]),
            'edit'       => route('rate.edit', ['rate' => $id]),
            'delete_msg' => "/rate/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $rate);
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
        $title = 'Index - Rate';
        $search = trim((string) $request->input('search'));
        $rates = Rate::leftJoin('rate_types', 'rate_types.id', '=', 'rates.rate_type')
            ->select('rates.id', 'rates.name', 'rates.mark', 'rates.sort_order', 'rate_types.name as rate_type')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('rates.name', 'like', "%{$search}%")->orWhere('rate_types.name', 'like', "%{$search}%");
                });
            })
            ->orderBy('rates.sort_order', 'asc')
            ->paginate(15)
            ->withQueryString();
        return view('rate.index', compact('rates', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Rate';
        $rate_types = RateType::all();

        return view('rate.create', compact('title', 'rate_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateRate($request);
        $status = new Rate();
        $status->name = $request->get('name');
        $status->mark = $request->get('mark');
        $status->rate_type = $request->get('rate_type');
        $status->sort_order = $request->get('sort_order');
        $status->save();

        LaravelFlashSessionHelper::setFlashMessage("Rate $status->name created", 'success');

        return $request->get('modal_create') == 1 ? redirect('home') : redirect('rate');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Rate';
        Rate::findOrFail($id);

        if ($request->ajax()) {
            return URL::to('rate/' . $id);
        }

        $rate = Rate::findOrfail($id);
        if($rate == null){
            return abort(404);
        }
        $rate_type = RateType::where('id', $rate->rate_type)->first();

        return view('rate.show', compact('title', 'rate', 'rate_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Rate';
        Rate::findOrFail($id);
        if ($request->ajax()) {
            return URL::to('rate/' . $id . '/edit');
        }

        $rate = Rate::findOrfail($id);
        $rate_types = RateType::all();
        return view('rate.edit', compact('title', 'rate_types', 'rate'));
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
        $rate = Rate::findOrfail($id);
        $this->validateRate($request, $rate);
        $rate->name = $request->get('name');
        $rate->mark = $request->get('mark');
        $rate->rate_type = $request->get('rate_type');
        $rate->sort_order = $request->get('sort_order');
        $rate->save();

        LaravelFlashSessionHelper::setFlashMessage("Rate $rate->name edited", 'success');

        return redirect('rate');
    }


    public function DeleteMsg($id, Request $request)
    {
        Rate::findOrFail($id);
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/rate/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/rate/' . $id . '/delete');

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
    public function destroy($id, DeleteModel $deleteModel)
    {
        return DB::transaction(function () use ($id, $deleteModel) {
            $rate = Rate::query()->lockForUpdate()->findOrFail($id);
            $message = $deleteModel->check($rate, 'Rate');
            if ($message) {
                if (request()->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
                LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
                return redirect('rate');
            }
            $rate->delete();
            Comment::query()->where('reference_type', Comment::$services['rate'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Rate $rate->name deleted", 'success');
            return request()->expectsJson() ? response()->json(['route' => route('rate.index')]) : redirect('rate');
        });
    }

    public function validateRate($request, Rate $rate = null){

        $this->validate($request, [
            'name' => array_merge(\App\Services\ValidationService::nameRules(), [\Illuminate\Validation\Rule::unique('rates', 'name')->ignore($rate ? $rate->id : null)]),
            'mark' => \App\Services\ValidationService::nameRules(true, 191, 1),
            'rate_type' => 'required|integer|exists:rate_types,id',
            'sort_order' => 'required|integer|min:0|max:2147483647',
        ]);
    }
}
