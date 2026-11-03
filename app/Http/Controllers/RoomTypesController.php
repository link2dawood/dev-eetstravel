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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use URL;

class RoomTypesController extends Controller
{
    use FileTrait;


    public function getButton($id, $room_types)
    {
        $url = array('show'       => route('room_types.show', ['room_type' => $id]),
            'edit'       => route('room_types.edit', ['room_type' => $id]),
            'delete_msg' => "/room_types/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $room_types);
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
        $title = 'Index - Room Types';
        $search = trim((string) $request->input('search'));
        $room_types = RoomTypes::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order', 'asc')
            ->paginate(15)
            ->withQueryString();
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
		$fileName = $request->hasFile('image') ? $this->storeImage($request->file('image')) : "";

		
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
        RoomTypes::findOrFail($id);

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
        RoomTypes::findOrFail($id);
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
        $room_types = RoomTypes::findOrfail($id);
        $this->validateRoomTypes($request, $room_types);
		
		// keep the current image unless a new one is uploaded (it used to be wiped on every save)
		if ($request->hasFile('image')) {
			$room_types->img = $this->storeImage($request->file('image'));
            // Retain the old asset: failed saves and historical references must not lose it.
		}

        $room_types->name = $request->get('name');
        $room_types->code = $request->get('code');
        $room_types->sort_order = $request->get('sort_order');
        $room_types->save();

        LaravelFlashSessionHelper::setFlashMessage("Room Type $room_types->name edited", 'success');

        return redirect('room_types');
    }


    public function DeleteMsg($id, Request $request)
    {
        RoomTypes::findOrFail($id);
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/room_types/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!', 'Permanently delete this room type? This cannot be undone.', '/room_types/' . $id . '/delete');

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
            $roomType = RoomTypes::query()->lockForUpdate()->findOrFail($id);
            if ($roomType->isSystem()) return $this->blockedDelete('System room types cannot be deleted.');
            // Refuse while hotels, agreements, seasons, offers or tours still use it.
            $references = ['hotel_room_types', 'hotel_agreements_room_type_hotels', 'seasons_prices_room_type_hotels',
                'prices_room_type_hotels', 'tour_room_type_hotels', 'offer_room_prices'];
            foreach ($references as $table) {
                if (Schema::hasTable($table) && DB::table($table)->where('room_type_id', $id)->exists()) {
                    $message = "Room Type {$roomType->name} is used by hotels, seasons, offers or tours and cannot be deleted";
                    return $this->blockedDelete($message);
                }
            }
            $roomType->delete();
            Comment::query()->where('reference_type', Comment::$services['room_types'])->where('reference_id', $id)->delete();
            // Keep image assets so transaction rollback and historical links remain safe.
            LaravelFlashSessionHelper::setFlashMessage("Room Type $roomType->name deleted", 'success');
            return request()->expectsJson() ? response()->json(['route' => route('room_types.index')]) : redirect('room_types');
        });
    }

    private function blockedDelete($message)
    {
        if (request()->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
        LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
        return redirect('room_types');
    }

    public function validateRoomTypes($request, RoomTypes $roomType = null){

        $this->validate($request, [
            'name' => array_merge(\App\Services\ValidationService::nameRules(), [\Illuminate\Validation\Rule::unique('room_types', 'name')->ignore($roomType ? $roomType->id : null)]),
            'code' => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9_-]+$/', \Illuminate\Validation\Rule::unique('room_types', 'code')->ignore($roomType ? $roomType->id : null)],
            'sort_order' => 'required|integer|min:0|max:2147483647',
            // stored in the public assets folder, so images only
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048', function ($attribute, $value, $fail) {
                if (!($value instanceof \Illuminate\Http\UploadedFile) || !@getimagesize($value->getPathname())) {
                    $fail('Choose a PNG, JPEG, GIF or WebP raster image. SVG images are not allowed.');
                }
            }],
        ]);
        if ($roomType && $roomType->isSystem() && $roomType->code !== $request->code) {
            throw \Illuminate\Validation\ValidationException::withMessages(['code' => 'System room type codes cannot be changed.']);
        }
    }

    /** Saves an uploaded room type icon; the extension comes from the content, not the client's name. */
    private function storeImage($file)
    {
        $extension = $file->guessExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $file->move(public_path('assets/img/svg'), $fileName);

        return $fileName;
    }
}
