<?php

namespace App\Http\Controllers\ScaffoldInterface;

use Amranidev\Ajaxis\Ajaxis;
use App\Helper\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use URL;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Roles are shown in the delete confirmation so the impact is visible
        $permissions = Permission::with('roles:id,name')->get();
        return view('scaffold-interface.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('scaffold-interface.permissions.create');
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
            'name' => 'required',
            'alias' => 'required'
        ]);

        Permission::create(['name' => $request->name, 'alias' => $request->alias]);

        return redirect('permissions');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('scaffold-interface.permissions.edit', compact('permission'));
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
        'name' => 'required|unique:permissions,name,' . $request->permission_id,
        'alias' => 'required'
    ]);

    $permission = Permission::findOrFail($request->permission_id);
    $permission->name = $request->name;
    $permission->alias = $request->alias;
    $permission->save();

    return redirect('permissions')->with('success', 'Permission updated successfully');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
  public function destroy($id, Request $request)
    {
        try {
            $permission = Permission::findOrFail($id);

            // A permission still in use is only removed when the user confirmed
            // detaching it (force=1 comes from the confirmation on the index page)
            $roleNames = $permission->roles()->pluck('name');
            $userCount = $permission->users()->count();

            if (($roleNames->isNotEmpty() || $userCount > 0) && !$request->boolean('force')) {
                $usedBy = $roleNames->isNotEmpty() ? 'roles: ' . $roleNames->implode(', ') : $userCount . ' user(s)';
                return redirect('permissions')->with('error', "Cannot delete permission \"{$permission->name}\". It is assigned to {$usedBy}.");
            }

            DB::transaction(function () use ($permission) {
                $permission->roles()->detach();
                $permission->users()->detach();
                $permission->delete();
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $message = 'Permission deleted successfully';
            if ($roleNames->isNotEmpty()) {
                $message .= ' and removed from roles: ' . $roleNames->implode(', ');
            }

            return redirect('permissions')->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect('permissions')->with('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    /**
     * Show delete confirmation modal
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteMsg($id, Request $request)
    {
        try {
            $permission = Permission::findOrFail($id);
            
            $msg = Ajaxis::BtDeleting(
                trans('main.Warning') . '!!',
                trans('main.WouldyouliketoremoveThis') . '?',
                '/permissions/' . $id . '/delete'
            );
            
            if ($request->ajax()) {
                return $msg;
            }
            
            return redirect('permissions');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Permission not found'], 404);
            }
            
            return redirect('permissions')->with('error', 'Permission not found');
        }
    }
}
