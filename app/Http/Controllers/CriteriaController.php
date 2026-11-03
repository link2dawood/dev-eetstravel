<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Criteria;
use App\CriteriaType;

use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Illuminate\Http\Request;
use URL;
use View;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;
use Illuminate\Support\Facades\DB;


class CriteriaController extends Controller
{
    use FileTrait;

    public function getButton($id, $criteria)
    {
        $url = array('show'       => route('criteria.show', ['criterion' => $id]),
            'edit'       => route('criteria.edit', ['criterion' => $id]),
            'delete_msg' => "/criteria/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $criteria);
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
        $title = 'Index - Criteria';

        $criteriaData = Criteria::leftJoin('criteria_types', 'criteria_types.id', '=', 'criterias.criteria_type')
            ->select('criterias.id','criterias.name','criterias.short_name','criterias.icon','criteria_types.name as criteria_type')
            ->when(trim((string)$request->input('search')) !== '', function ($query) use ($request) {
                $search = '%' . trim((string)$request->input('search')) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('criterias.name', 'like', $search)->orWhere('criterias.short_name', 'like', $search)->orWhere('criteria_types.name', 'like', $search);
                });
            })
            ->orderBy('criterias.name')->orderBy('criterias.id')->paginate(25)->withQueryString();

        return view('criteria.index', compact('criteriaData', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Criteria';
        $criteria_types = CriteriaType::all();

        return view('criteria.create', compact('title', 'criteria_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateCriteria($request);
        $criteria = new Criteria();
        $criteria->name = $request->get('name');
        $criteria->short_name = $request->get('short_name');
        $criteria->icon = $request->get('icon');
        $criteria->criteria_type = $request->get('criteria_type');
        $criteria->save();

        LaravelFlashSessionHelper::setFlashMessage("Criteria $criteria->name created", 'success');

        return $request->get('modal_create') == 1 ? redirect('home') : redirect('criteria');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($criterion, Request $request)
    {
        $title = 'Show - Criteria';
        Criteria::findOrFail($criterion);

        if ($request->ajax()) {
            return URL::to('criteria/' . $criterion);
        }

        $criteria = Criteria::findOrfail($criterion);

        if($criteria == null){
            return abort(404);
        }

        $criteria_type = CriteriaType::where('id', $criteria->criteria_type)->first();

        return view('criteria.show', compact('title', 'criteria', 'criteria_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($criterion, Request $request)
    {
        $title = 'Edit - Criteria';
        Criteria::findOrFail($criterion);
        if ($request->ajax()) {
            return URL::to('criteria/' . $criterion . '/edit');
        }

        $criteria = Criteria::findOrfail($criterion);
        $criteria_types = CriteriaType::all();

        return view('criteria.edit', compact('title', 'criteria', 'criteria_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $criterion)
    {
        DB::transaction(function () use ($request, $criterion) {
            $criteria = Criteria::query()->lockForUpdate()->findOrFail($criterion);
            $this->validateCriteria($request, $criteria);
            $criteria->name = $request->get('name');
            $criteria->short_name = $request->get('short_name');
            $criteria->icon = $request->get('icon');
            $criteria->criteria_type = $request->get('criteria_type');
            $criteria->save();
            LaravelFlashSessionHelper::setFlashMessage("Criteria $criteria->name edited", 'success');
        });

        return redirect('criteria');
    }


    public function DeleteMsg($id, Request $request)
    {
        Criteria::findOrFail($id);
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/criteria/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/criteria/' . $id . '/delete');

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
    public function destroy($criterion, DeleteModel $deleteModel)
    {
        return DB::transaction(function () use ($criterion, $deleteModel) {
            $criteria = Criteria::query()->lockForUpdate()->findOrFail($criterion);
            $message = $deleteModel->check($criteria, 'Criteria');
            if ($message) {
                if (request()->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
                LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
                return redirect('criteria');
            }
            LaravelFlashSessionHelper::setFlashMessage("Criteria $criteria->name deleted", 'success');
            $criteria->delete();
            Comment::query()->where('reference_type', Comment::$services['criteria'])->where('reference_id', $criterion)->delete();
            return request()->expectsJson() ? response()->json(['route' => route('criteria.index')]) : redirect('criteria');
        });
    }

    public function validateCriteria($request, Criteria $criteria = null){
        $type = is_scalar($request->input('criteria_type')) ? $request->input('criteria_type') : null;
        $this->validate($request, [
            'name' => array_merge(\App\Services\ValidationService::nameRules(), [\Illuminate\Validation\Rule::unique('criterias', 'name')->where('criteria_type', $type)->ignore($criteria ? $criteria->id : null)]),
            'short_name' => \App\Services\ValidationService::nameRules(true, 191, 1),
            'icon' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9-]+$/'],
            'criteria_type' => 'required|integer|exists:criteria_types,id',
        ]);
        if ($criteria && (string)$request->criteria_type !== (string)$criteria->criteria_type && \App\ServicesHasCriteria::where('criteria_id', $criteria->id)->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['criteria_type' => 'The type of a criterion in use cannot be changed.']);
        }
    }
}
