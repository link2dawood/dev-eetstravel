<?php

namespace App\Http\Controllers\ScaffoldInterface;

use Amranidev\Ajaxis\Ajaxis;
use App\Helper\AdminHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Http\Controllers\Controller;
use App\User;
use App\Task;
use Hash;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth; // ADD THIS LINE

class UserController extends Controller
{
    /**
     * @var AdminHelper
     */
    private $adminHelper;

    /**
     * UserController constructor.
     * @param AdminHelper $adminHelper
     */
    public function __construct(AdminHelper $adminHelper)
    {
        $this->adminHelper = $adminHelper;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        // update() also saves a user's own profile, so it checks users.edit itself.
        $this->middleware('permissions.required')->except('update');
    }

    const AVATAR_RULE = 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120';

    /**
     * Store an uploaded avatar under a generated name and return its public path.
     */
    private function storeAvatar($file)
    {
        $filename = $file->hashName();
        $file->move(public_path('uploads/avatars'), $filename);

        return 'uploads/avatars/' . $filename;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('scaffold-interface.resource-details', [
            'heading' => 'User Details', 'listRoute' => 'users.index',
            'fields' => ['ID' => $user->id, 'Name' => $user->name, 'Email' => $user->email,
                'Roles' => $user->roles->pluck('name')->implode(', ')],
        ]);
    }

    public function index()
    {
        $users = \App\User::with(['roles', 'permissions'])->orderBy('name')->paginate(25);

        return view('scaffold-interface.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('scaffold-interface.users.create');
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
        $this->validateUser($request);
        $user = new \App\User();

        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);

        if($request->hasFile('avatar')){
            $user->avatar = $this->storeAvatar($request->file('avatar'));
        }
        $user->save();

        LaravelFlashSessionHelper::setFlashMessage("User {$user->name} created", 'success');

        return redirect('users');
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
        $user = \App\User::findOrfail($id);
        $roles = Role::all()->pluck('name', 'id');
        $permissions = Permission::all()->pluck('alias', 'name');
        $userRoles = $user->roles->pluck('name', 'id');
        $userPermissions = $user->permissions->pluck('alias', 'name');
        $permissions = $permissions->diff($userPermissions);
        $roles = $roles->diff($userRoles);

        return view(
            'scaffold-interface.users.edit',
            compact('user', 'roles', 'permissions', 'userRoles', 'userPermissions')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Anyone may save their own profile; editing another account needs users.edit.
        abort_unless((int) $id === (int) Auth::id() || Auth::user()->can('users.edit'), 403);
        $user = \App\User::findOrFail($id);

        $email_login = $request->get('email_login', null);
        $email_password = $request->get('email_password', null);
        $email_server = $request->get('email_server', null);

        $this->validate($request, [
            'avatar'   => self::AVATAR_RULE,
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|max:191|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);


        if((int) $email_server !=  0){
            if($email_login != null &&  $email_password != null){
                if(!$this->adminHelper->validateEmailCorrect($email_login, $email_password, $email_server)){
                    return back()->with('incorrect_data', 'Incorrect email login or password login');
                }
            }
        }



        $user->email = $request->email;
        $user->name = $request->name;
        // Only the profile forms send these; the admin edit form must not wipe them.
        foreach (['location', 'education', 'note'] as $field) {
            if ($request->has($field)) {
                $user->$field = $request->get($field);
            }
        }
        if ($request->has('email_server')) {
            $user->email_server = (int) $email_server;
        } elseif ($request->filled('email_login') && !$user->email_server) {
            // Entering mailbox credentials on the profile connects the default (EETS) server.
            $user->email_server = \App\Email::TYPE_EETS;
        }
        if ($request->email_login) {
	        $user->email_login = $request->email_login;
        }
	    if ($request->email_password) {
		    $user->email_password = Crypt::encryptString($request->email_password);
	    }
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        if($request->hasFile('avatar')){
            $user->avatar = $this->storeAvatar($request->file('avatar'));
        }
        $user->save();

        LaravelFlashSessionHelper::setFlashMessage("User {$user->name} edited", 'success');

        if($request->get('edit_profile') == 1){
            return redirect('profile');
        }else{
            return redirect('users');
        }
    }

    public function validateUser(Request $request)
    {
        $this->validate($request, [
            'avatar'   => self::AVATAR_RULE,
            'name'     => 'required|string|max:191',
            'email'    => 'required|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
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
        // The users list deletes over fetch(); a redirect would be followed as DELETE /users (405).
        $respond = function ($success, $message) use ($request) {
            if ($request->expectsJson()) {
                return response()->json(['success' => $success, 'message' => $message], $success ? 200 : 422);
            }
            LaravelFlashSessionHelper::setFlashMessage($message, $success ? 'success' : 'error');
            return redirect('users');
        };

        if (Auth::id() == $id) {
            return $respond(false, 'You cannot delete yourself');
        }

        try {
            $user = \App\User::findOrFail($id);
            $userName = $user->name;
            $user->delete();

            return $respond(true, "User {$userName} deleted successfully");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $respond(false, 'User not found');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return $respond(false, 'Error deleting user');
        }
    }

    /**
     * Assign Role to user.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function addRole(Request $request)
    {
      
        $user = \App\User::findOrfail($request->user_id);
        if ($request->role_name) {
            $user->assignRole($request->role_name);
        }


        return redirect('users/'.$request->user_id.'/edit');
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
        $user = \App\User::findorfail($request->user_id);
        if ($request->permission_name) {
            foreach ($request->permission_name as $item){
                $user->givePermissionTo($item);
            }
        }

        return redirect('users/'.$request->user_id.'/edit');
    }

    /**
     * revoke Permission to a user.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function revokePermission($user_id, $permission)
    {
        $user = \App\User::findorfail($user_id);

        $user->revokePermissionTo($permission);

        return redirect('users/'.$user_id.'/edit');
    }

    /**
     * revoke Role to a a user.
     *
     * @param \Illuminate\Http\Request
     *
     * @return \Illuminate\Http\Response
     */
    public function revokeRole(Request $request)
    {
        $user_id = $request->get('user_id');
        $role = $request->get('role');
        $user = \App\User::findorfail($user_id);

        $user->removeRole($role);

        return redirect('users/'.$user_id.'/edit');
    }

    /**
     * show modal window and display message before delete user
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function deleteMsg($id, Request $request)
    {
        try {
            $user = \App\User::find($id);
            
            // Check if user exists
            if (!$user) {
                \Log::error('User not found with ID: ' . $id);
                $msg = Ajaxis::MtWarning(
                    trans('main.Warning') . '!',
                    'User not found!'
                );
                return $msg;
            }
            
            // Check if user can be deleted
            if (Auth::id() == $id) {
                $msg = Ajaxis::MtWarning(
                    trans('main.Warning') . '!',
                    'You cannot delete yourself!'
                );
            } else {
                // Return the Ajaxis delete confirmation modal
                // Try using route() helper instead of url()
                $msg = Ajaxis::BtDeleting(
                    trans('main.Warning') . '!',
                    'Would you like to remove ' . $user->name . '?',
                    route('users.legacy.destroy', $id)  // Using named route
                );
            }
            
            if($request->ajax()) {
                return $msg;
            }
            
            return $msg;
            
        } catch (\Exception $e) {
            \Log::error('Error in deleteMsg for user ID ' . $id . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $msg = Ajaxis::MtWarning(
                trans('main.Warning') . '!',
                'Error: ' . $e->getMessage()
            );
            
            if($request->ajax()) {
                return $msg;
            }
            
            return $msg;
        }
    }
}
