<?php

namespace App\Http\Controllers;

use App\City;
use App\Country;
use App\Criteria;
use App\GooglePlaces;
use App\Helper\CitiesHelper;
use App\Helper\FileTrait;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\TransferRepository\EloquentTransferRepository;
use App\Comment;
use App\ServicesHasCriteria;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Transfer;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

/**
 * Class TransferController.
 *
 * @author  The scaffold-interface created at 2017-04-12 01:02:32pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class TransferController extends Controller
{
    use FileTrait;

    protected $transfers;

    public function __construct(EloquentTransferRepository $transferRepository)
    {
        $this->middleware('permissions.required');
        $this->transfers = $transferRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function getButton($id, $transfer)
    {
        $url = array('show'       => route('transfer.show', ['transfer' => $id]),
                     'edit'       => route('transfer.edit', ['transfer' => $id]),
                     'delete_msg' => "/transfer/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url, false, $transfer);
    }

    public function data(Request $request)
    {
        return Datatables::of(
            Transfer::distinct()->leftJoin('countries', 'countries.alias', '=', 'transfers.country')
                ->leftJoin('cities', 'cities.id', '=', 'transfers.city')
                ->select(
                    [
                        'transfers.id',
                        'transfers.name',
                        'transfers.address_first',
                        'cities.name as city',
                        'countries.name as country',
                        'transfers.work_phone',
                        'transfers.contact_name'
                    ]
                )
        )->addColumn('action', function ($transfer) {
            return $this->getButton($transfer->id, $transfer);
        })->addColumn('select', function($transfer){
            return DatatablesHelperController::getSelectButton($transfer->id, $transfer->name);
        })->rawColumns(['action', 'select'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - transfer';
        $transfers = Transfer::leftJoin('countries', 'countries.alias', '=', 'transfers.country')
            ->leftJoin('cities', 'cities.id', '=', 'transfers.city')
            ->select([
                'transfers.id',
                'transfers.name',
                'transfers.address_first',
                'cities.name as city_name',
                'countries.name as country_name',
                'transfers.work_phone',
                'transfers.contact_name'
            ])
            ->paginate(15);
        return view('transfer.index', compact('transfers', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Bus Company';
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'transfer')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        return view('transfer.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreTransferRequest $request)
    {

        $request = CitiesHelper::setCityGeneral($request);
        $transfer = $this->transfers->create($request->except('attach', 'place_id', 'criterias'));
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $transfer->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['transfer'];
                $services_has_criteria->save();
            }
        }


        LaravelFlashSessionHelper::setFlashMessage("Bus Company $transfer->name created", 'success');

        $this->addFile($request, $transfer);
        $data = ['route' => route('transfer.index')];
		return redirect()->back();
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Bus Company';

        if ($request->ajax()) {
            return URL::to('transfer/' . $id);
        }

        $transfer = Transfer::leftJoin('rates', 'rates.id', '=', 'transfers.rate')
            ->select('transfers.*', 'rates.name as rate_name')
            ->where('transfers.id', $id)
            ->first();
        $transfer->getCriterias();
        if($transfer == null){
            return abort(404);
        }
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'transfer')
            ->get();
        $files = $this->parseAttach($transfer);
        $comments = Comment::where('reference_type', 7)
            ->where('reference_id', $id)->where('parent', 0)->get()->toArray();

        foreach ($comments as &$comment) {
            $comment['children'] = Comment::where('parent', $comment['id'])->get()->toArray();
        }
        return view(
            'transfer.show',
            ['comments' => $comments, 'title' => $title, 'transfer' => $transfer, 'files' => $files,
                'criterias' => $criterias]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Bus Company';
        if ($request->ajax()) {
            return URL::to('transfer/' . $id . '/edit');
        }


        $transfer = Transfer::findOrfail($id);
        $transfer->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'transfer')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        $files = $this->parseAttach($transfer);
        return view('transfer.edit', ['title' => $title, 'transfer' => $transfer, 'files' => $files,
            'criterias' => $criterias,
            'rates' => $rates]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function update($id, UpdateTransferRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
        $this->transfers->updateById($id, $request->except('attach', 'place_id', 'criterias'));
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['transfer'])
            ->delete();
        $transfer = $this->transfers->getById($id);
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $transfer->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['transfer'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Bus Company $transfer->name edited", 'success');

        $this->addFile($request, $transfer);    
        $data = ['route' => route('transfer.index')];
        return response()->json($data);
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/transfer/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/transfer/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $transfer = $this->transfers->getById($id);
        $message = $deleteModel->check($transfer, 'Transfer');
        if ($message){
            Session::flash('message', $message);
        } else {
            $this->removeFile($transfer);
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['transfer'])
                ->delete();
            Comment::query()->where('reference_type', Comment::$services['transfer'])->where('reference_id', $id)->delete();
            $this->transfers->deleteById($id);
            LaravelFlashSessionHelper::setFlashMessage("Bus Company $transfer->name deleted", 'success');
        }


        return URL::to('transfer');
    }
}
