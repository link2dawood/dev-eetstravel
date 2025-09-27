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

    public function data(Request $request)
    {
        return Datatables::of(Criteria::leftJoin('criteria_types', 'criteria_types.id', '=', 'criterias.criteria_type')
            ->select('criterias.id','criterias.name','criterias.short_name','criterias.icon','criteria_types.name as criteria_type')
        )
            ->addColumn('action', function ($criteria) {
                return $this->getButton($criteria->id, $criteria);
            })
            ->addColumn('icon', function ($criteria){
                return View::make('component.icon_for_criteria', ['icon' => $criteria->icon])->render();
            })
            ->rawColumns(['action', 'icon'])

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
        $title = 'Index - Criteria';

        // Get all criteria with their related data (same as the AJAX data method)
        $criteriaData = Criteria::leftJoin('criteria_types', 'criteria_types.id', '=', 'criterias.criteria_type')
            ->select('criterias.id','criterias.name','criterias.short_name','criterias.icon','criteria_types.name as criteria_type')
            ->get();

        // Add action buttons and formatted icons to each criteria
        $criteriaData->each(function ($criteria) {
            $criteria->action_buttons = $this->getButton($criteria->id, $criteria);
            $criteria->formatted_icon = View::make('component.icon_for_criteria', ['icon' => $criteria->icon])->render();
        });

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
        $criteria = Criteria::all();
        $criteria_types = CriteriaType::all();

        return view('criteria.create', compact('title', 'criteria', 'criteria_types'));
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
        $this->validateCriteria($request);

        $criteria = Criteria::findOrfail($criterion);
        $criteria->name = $request->get('name');
        $criteria->short_name = $request->get('short_name');
        $criteria->icon = $request->get('icon');
        $criteria->criteria_type = $request->get('criteria_type');
        $criteria->save();

        LaravelFlashSessionHelper::setFlashMessage("Criteria $criteria->name edited", 'success');

        return redirect('criteria');
    }


    public function DeleteMsg($id, Request $request)
    {
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
        $criteria = Criteria::findOrfail($criterion);
        $message = $deleteModel->check($criteria, 'Criteria');
        if ($message){
            Session::flash('message', $message);
        } else {
            LaravelFlashSessionHelper::setFlashMessage("Criteria $criteria->name deleted", 'success');
            $criteria->delete();
            Comment::query()->where('reference_type', Comment::$services['criteria'])->where('reference_id', $criterion)->delete();
        }

        return URL::to('criteria');
    }

    public function validateCriteria($request){
        $this->validate($request, [
            'name'    => 'required',
            'short_name'    => 'required',
            'criteria_type' => 'required'
        ]);
    }
}
