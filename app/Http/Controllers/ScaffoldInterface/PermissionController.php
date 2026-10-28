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
     * Permission names are checked in code and routes as "module.action" (bus_calendar and
     * guestList.add are also in use): letters, digits, _ and - separated by dots, no spaces.
     */
    const NAME_FORMAT = 'regex:/^[A-Za-z0-9_-]+(\.[A-Za-z0-9_-]+)*$/';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissions.required');
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return view('scaffold-interface.resource-details', [
            'heading' => 'Permission Details', 'listRoute' => 'permissions.index',
            'fields' => ['ID' => $permission->id, 'Name' => $permission->name,
                'Alias' => $permission->alias, 'Roles' => $permission->roles->pluck('name')->implode(', ')],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        // Roles are shown in the delete confirmation so the impact is visible
        $permissions = Permission::with('roles:id,name')->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)->orWhere('alias', 'like', $like);
                });
            })
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        return view('scaffold-interface.permissions.index', compact('permissions', 'search'));
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
            'name' => ['required', 'string', 'max:191', self::NAME_FORMAT, 'unique:permissions,name'],
            'alias' => 'required|string|max:191'
        ], ['name.regex' => 'Use letters, numbers, _ and - separated by dots, e.g. tour.index.']);

        Permission::create(['name' => $request->name, 'alias' => $request->alias]);

        return redirect('permissions')->with('success', "Permission {$request->name} created successfully");
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
        $permission = Permission::withCount('users')->with('roles:id,name')->findOrFail($id);
        $inUse = $permission->roles->isNotEmpty() || $permission->users_count > 0;

        return view('scaffold-interface.permissions.edit', compact('permission', 'inUse'));
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
    // The edit form posts permission_id to /permissions/update; the resource PUT route carries the id in the URL.
    $permission = Permission::findOrFail($id ?? $request->permission_id);
    $renaming = $request->name !== $permission->name;

    $nameRules = ['required', 'string', 'max:191', 'unique:permissions,name,' . $permission->id];
    if ($renaming) {
        $nameRules[] = self::NAME_FORMAT;
    }
    $this->validate($request, [
        'name' => $nameRules,
        'alias' => 'required|string|max:191'
    ], ['name.regex' => 'Use letters, numbers, _ and - separated by dots, e.g. tour.index.']);

    // Code and routes check permissions by name, so renaming one that is assigned would silently revoke that access.
    if ($renaming) {
        $roleNames = $permission->roles()->pluck('name');
        $userCount = $permission->users()->count();
        if ($roleNames->isNotEmpty() || $userCount > 0) {
            $usedBy = $roleNames->isNotEmpty() ? 'roles: ' . $roleNames->implode(', ') : $userCount . ' user(s)';
            return back()->withInput()->withErrors(['name' => "This permission is assigned to {$usedBy}. Its name cannot be changed; edit the alias instead."]);
        }
    }

    $permission->name = $request->name;
    $permission->alias = $request->alias;
    $permission->save();

    app(PermissionRegistrar::class)->forgetCachedPermissions();

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
            // detaching it (the index page adds force=1 only after that confirmation)
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
