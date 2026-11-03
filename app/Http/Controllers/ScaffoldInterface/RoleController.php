<?php

namespace App\Http\Controllers\ScaffoldInterface;

use Amranidev\Ajaxis\Ajaxis;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use URL;

class RoleController extends Controller
{
    /**
     * Roles the application checks by name (hasRole('admin') etc.); renaming or
     * deleting them would silently switch that behaviour off.
     */
    const PROTECTED_ROLES = ['admin', 'user'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->middleware('permissions.required');
    }

    public static function isProtected(Role $role)
    {
        return in_array(strtolower($role->name), self::PROTECTED_ROLES, true);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('scaffold-interface.resource-details', [
            'heading' => 'Role Details', 'listRoute' => 'roles.index',
            'fields' => ['ID' => $role->id, 'Name' => $role->name,
                'Permissions' => $role->permissions->pluck('name')->implode(', ')],
        ]);
    }

    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();

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
            'name' => 'required|string|max:191|unique:roles,name'
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
        $isProtected = self::isProtected($role);

        return view('scaffold-interface.roles.edit', compact('role', 'permissions', 'rolePermissions', 'isProtected'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        // The edit form posts role_id to /roles/update; the resource PUT route carries the id in the URL.
        $role = Role::findOrFail($id ?? $request->role_id);

        $this->validate($request, [
            'name' => 'required|string|max:191|unique:roles,name,' . $role->id
        ]);

        if (self::isProtected($role) && $request->name !== $role->name) {
            return back()->withInput()->withErrors(['name' => "The {$role->name} role is used by the application and cannot be renamed."]);
        }

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
        $role = Role::withCount('users')->findOrFail($id);

        if (self::isProtected($role)) {
            LaravelFlashSessionHelper::setFlashMessage("The {$role->name} role is used by the application and cannot be deleted", 'error');
            return redirect('roles');
        }

        $role->delete();

        $affected = $role->users_count ? " and removed from {$role->users_count} user(s)" : '';
        LaravelFlashSessionHelper::setFlashMessage("Role {$role->name} deleted{$affected}", 'success');

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
        $this->validate($request, [
            'role_id'           => 'required|integer|exists:roles,id',
            'permission_name'   => 'required|array|min:1',
            'permission_name.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findOrFail($request->role_id);

        // One call inside a transaction, so either every selected permission is added or none is.
        DB::transaction(function () use ($role, $request) {
            $role->givePermissionTo($request->permission_name);
        });

        LaravelFlashSessionHelper::setFlashMessage('Permissions added to role ' . $role->name, 'success');

        return redirect('roles/'.$role->id.'/edit');
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

        if (!Permission::where('name', $permission)->exists()) {
            return redirect('roles/'.$role_id.'/edit')->withErrors(['permission' => "There is no permission named {$permission}."]);
        }

        $role->revokePermissionTo($permission);

        LaravelFlashSessionHelper::setFlashMessage("Permission {$permission} removed from role {$role->name}", 'success');

        return redirect('roles/'.$role_id.'/edit');
    }

    public function deleteMsg($id, Request $request){
        $role = Role::withCount('users')->findOrFail($id);

        if (self::isProtected($role)) {
            return Ajaxis::MtWarning(trans('main.Warning').'!', "The {$role->name} role is used by the application and cannot be deleted.");
        }

        $question = trans('main.WouldyouliketoremoveThis').'?';
        if ($role->users_count) {
            $question = "{$role->users_count} user(s) will lose this role. " . $question;
        }

        return Ajaxis::BtDeleting(trans('main.Warning').'!!', $question, '/roles/'. $id . '/delete');
    }
}
