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
            'name' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9_]+$/', 'unique:settings,name'],
            'description' => 'required|string|max:1000',
            'value' => Setting::valueRules($request->get('name'))],
            ['name.regex' => 'Use lowercase letters, numbers and _ only, e.g. tour_service_notification.']);

        // Only the form fields; id and timestamps must not come from the request.
        Setting::create($request->only(['name', 'description', 'value']));

        LaravelFlashSessionHelper::setFlashMessage("Setting {$request->description} created", 'success');

        return redirect(route('settings.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);

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
        $setting = Setting::findOrFail($id);

        $this->validate($request, [
            'desc' => 'required|string|max:1000',
            'value' => Setting::valueRules($setting->name)
        ], [], ['desc' => 'description']);

        $setting->description = $request->get('desc');
        $setting->value = $request->get('value');
        $setting->save();

        LaravelFlashSessionHelper::setFlashMessage("Setting {$setting->description} edited", 'success');

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
        $setting = Setting::findOrFail($id);

        if ($setting->isSystem()) {
            LaravelFlashSessionHelper::setFlashMessage("Setting {$setting->name} is used by scheduled jobs and cannot be deleted", 'error');
            return redirect(route('settings.index'));
        }

        $setting->delete();

        LaravelFlashSessionHelper::setFlashMessage("Setting {$setting->description} deleted", 'success');

        return redirect(route('settings.index'));
    }
    /**
     * return datatables data
     */
    public function getButton($id)
    {
        $url = ['edit'       => route('settings.edit', ['id' => $id])];

        return DatatablesHelperController::getEditButton($url);
    }
}
