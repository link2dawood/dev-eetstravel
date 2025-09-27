<?php

namespace App\Http\Controllers\ScaffoldInterface;

use Amranidev\Ajaxis\Ajaxis;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use URL;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::all();

        return view('scaffold-interface.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('scaffold-interface.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        Role::create(['name' => $request->name]);

        LaravelFlashSessionHelper::setFlashMessage("Role {$request->name} created", 'success');

        return redirect('roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {

        $role = Role::findOrFail($id);
        $rolePermissions = $role->permissions->pluck('alias', 'name');
        $permissions = Permission::all()->pluck('alias', 'name');
        $permissions = $permissions->diff($rolePermissions);

        return view('scaffold-interface.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $role = Role::findOrFail($request->role_id);

        $role->name = $request->name;

        $role->update();

        LaravelFlashSessionHelper::setFlashMessage("Role {$role->name} edited", 'success');

        return redirect('roles');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        LaravelFlashSessionHelper::setFlashMessage("Role {$role->name} deleted", 'success');

      return redirect('roles');
    }


    /**
     * Assign Permission to a user.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function addPermission(Request $request)
    {

        $role = Role::findorfail($request->role_id);
        if ($request->permission_name) {
            foreach ($request->permission_name as $item){
                $role->givePermissionTo($item);
            }
        }

        return redirect('roles/'.$request->role_id.'/edit');
    }

    /**
     * revoke Permission to a user.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function revokePermission($permission, $role_id)
    {
        $role = Role::findorfail($role_id);

        $role->revokePermissionTo($permission);

        return redirect('roles/'.$role_id.'/edit');
    }

    public function deleteMsg($id, Request $request){
//        $msg = Ajaxis::BtDeleting('Warning!!','Would you like to remove This?','/roles/'. $id . '/delete');
        $msg = Ajaxis::BtDeleting( trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?','/roles/'. $id . '/delete');

        if($request->ajax())
        {
            return $msg;
        }
    }
	
	
	
	
}
