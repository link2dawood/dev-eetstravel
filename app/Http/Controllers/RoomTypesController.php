<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\HotelRoomTypes;
use App\RoomTypes;
use Illuminate\Http\Request;
use URL;

class RoomTypesController extends Controller
{
    use FileTrait;


    public function getButton($id, $room_types)
    {
        $url = array('show'       => route('room_types.show', ['id' => $id]),
            'edit'       => route('room_types.edit', ['id' => $id]),
            'delete_msg' => "/room_types/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $room_types);
    }

    public function data(Request $request)
    {
        $query = RoomTypes::query();

        // Get pagination parameters
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;

        // Get total count
        $total = $query->count();

        // Apply pagination
        $roomTypes = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Process each room type
        foreach($roomTypes as $roomType) {
            $roomType->action = $this->getButton($roomType->id, $roomType);
        }

        return response()->json([
            'data' => $roomTypes,
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
        $title = 'Index - Room Types';
        $room_types = RoomTypes::query()
            ->orderBy('sort_order', 'asc')
            ->paginate(15);
        return view('room_types.index', compact('room_types', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Room Types';

        return view('room_types.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateRoomTypes($request);
		$fileName = "";
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$fileName = time() . '.' . $request->image->extension();
			
			$url = $request->image->move('assets/img/svg/', $fileName);
		}

		
        $status = new RoomTypes();
        $status->name = $request->get('name');
        $status->code = $request->get('code');
        $status->sort_order = $request->get('sort_order');
		$status->img = $fileName;
        $status->save();

        LaravelFlashSessionHelper::setFlashMessage("Room Type $status->name created", 'success');

        return $request->get('modal_create') == 1 ? redirect('home') : redirect('room_types');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Room Types';

        if ($request->ajax()) {
            return URL::to('room_types/' . $id);
        }

        $room_types = RoomTypes::findOrfail($id);
        if($room_types == null){
            return abort(404);
        }
        return view('room_types.show', compact('title', 'room_types'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Room Types';
        if ($request->ajax()) {
            return URL::to('room_types/' . $id . '/edit');
        }

        $room_types = RoomTypes::findOrfail($id);
        return view('room_types.edit', compact('title', 'room_types'));
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
        $this->validateRoomTypes($request);

        $room_types = RoomTypes::findOrfail($id);
		
		$fileName = "";
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$fileName = time() . '.' . $request->image->extension();
			
			 if ($room_types->img) {
            $oldImagePath = public_path('ssets/img/svg/' . $room_types->img);
				if (file_exists($oldImagePath)) {
					unlink($oldImagePath);
				}
        	}
			$url = $request->image->move('assets/img/svg/', $fileName);
		}

        $room_types->name = $request->get('name');
        $room_types->code = $request->get('code');
        $room_types->sort_order = $request->get('sort_order');
		$room_types->img = $fileName;
        $room_types->save();

        LaravelFlashSessionHelper::setFlashMessage("Room Type $room_types->name edited", 'success');

        return redirect('room_types');
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/room_types/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/room_types/' . $id . '/delete');

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
        $status = RoomTypes::findOrfail($id);
        HotelRoomTypes::query()->where('room_type_id', $id)->delete();
        $status->delete();
        Comment::query()->where('reference_type', Comment::$services['room_types'])->where('reference_id', $id)->delete();

        LaravelFlashSessionHelper::setFlashMessage("Room Type $status->name deleted", 'success');

        return URL::to('room_types');
    }

    public function validateRoomTypes($request){

        $this->validate($request, [
            'name'    => 'required',
            'code' => 'required',
            'sort_order'  => 'required|numeric'
        ]);
    }
}
