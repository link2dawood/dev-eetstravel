<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    public function __construct()
    {
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

	public $notificationMessages = [
		'store' => "Menu <a target='_blank' href='%s'>#%s </a> created",
		'update' => "Menu <a target='_blank' href='%s'>#%s </a> edited",
		'delete' => "Menu <a target='_blank' href='%s'>#%s </a> deleted"
	];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
	    $serviceType = $request->type;
	    $serviceId = $request->id;
        return view('menu.create', compact('serviceType', 'serviceId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $this->validate($request, [
		    'name' => 'required',
		    'description' => 'required',
		    'price' => 'required|numeric',
	    ]);
	    $newMenu = new Menu();
	    $newMenu->name = $request->name;
	    $newMenu->price = $request->price;
	    $newMenu->description = $request->description;
	    if ($request->serviceType == 'hotel') {
		    $newMenu->hotel_id = $request->serviceId;
		    $route = ['route' => route('hotel.show', ['hotel' => $request->serviceId])];
	    }
	    if ($request->serviceType == 'restaurant') {
		    $newMenu->restaurant_id = $request->serviceId;
		    $route = ['route' => route('restaurant.show', ['id' => $request->serviceId])];
	    }
	    $newMenu->save();
	    LaravelFlashSessionHelper::setFlashMessage("Menu $newMenu->name created", 'success');


	    return response($route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::find($id);

        return view('menu.show', compact('menu'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::find($id);
        return view('menu.edit', compact('menu'));
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
	    $this->validate($request, [
		    'name' => 'required',
		    'description' => 'required',
		    'price' => 'required|numeric',
	    ]);
        $menu = Menu::query()->where('id', $id)->first();

	    Menu::query()->findOrFail($id)->update($request->only(['name', 'price', 'description']));

        LaravelFlashSessionHelper::setFlashMessage("Menu $menu->name edited", 'success');;

	    $data = ['route' => route('menu.show', ['id' => $id])];
		return response($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
    	$menu = Menu::find($id);
        $parentRoute= $menu->getParentRoute();

	    Menu::destroy($id);
        LaravelFlashSessionHelper::setFlashMessage("Menu $menu->name deleted", 'success');
	    return $parentRoute;
    }

    public function delete($id, Request $request)
    {
//	    $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove this menu?', route('menu.destroy_menu', ['id' => $id], false));
	    $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!', trans('main.Wouldyouliketoremovethismenu').'?', route('menu.destroy_menu', ['id' => $id], false));

	    if ($request->ajax()) {
		    return $msg;
	    }
    }
}
