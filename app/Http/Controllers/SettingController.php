<?php

namespace App\Http\Controllers;

use App\Helper\LaravelFlashSessionHelper;
use App\Helper\Trackable;
use Illuminate\Http\Request;
use App\Repository\Contracts\SettingsRepository;
use App\Setting;
use Yajra\Datatables\Datatables;

class SettingController extends Controller
{
    use Trackable;

    /**
     * settings Repository
     */
    public $settings;
    
    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.index', ['settings' => $this->settings->all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.create');
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
            'name' => 'required|unique:settings',
            'description' => 'required',
            'value' => 'required']);

        Setting::create($request->all());

        LaravelFlashSessionHelper::setFlashMessage("Setting {$request->description} created", 'success');

        return redirect(route('settings.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::query()->where('id', $id)->first();
        if(!$setting){
            return abort(404);
        }

        return view('settings.edit', compact('setting'));
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
            'desc' => 'required',
            'value' => 'required'
        ]);

        $setting = $this->settings->getById($id);
        $setting->description = $request->get('desc');
        $setting->value = $request->get('value');
        $setting->save();

        LaravelFlashSessionHelper::setFlashMessage("Setting {$setting->desc} edited", 'success');

        return redirect(route('settings.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = Setting::query()->where('id', $id)->first();
        $setting->delete();
        return redirect(route('settings.index'));
    }
    /**
     * return datatables data
     */
    public function data(Request $request)
    {
        return Datatables::of($this->settings->all())
            ->addColumn('action', function ($setting) {
                return $this->getButton($setting->id);
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function getButton($id)
    {
        $url = ['edit'       => route('settings.edit', ['id' => $id])];

        return DatatablesHelperController::getEditButton($url);
    }
}
