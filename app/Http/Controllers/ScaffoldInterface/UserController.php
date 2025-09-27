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
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = \App\User::all();

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
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
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
        $email_login = $request->get('email_login', null);
        $email_password = $request->get('email_password', null);
        $email_server = $request->get('email_server', null);

        $this->validate($request, [
            'avatar'    => 'image',
            'name' => 'required',
            'email' => 'required|email'
        ]);


        if((int) $email_server !=  0){
            if($email_login != null &&  $email_password != null){
                if(!$this->adminHelper->validateEmailCorrect($email_login, $email_password, $email_server)){
                    return back()->with('incorrect_data', 'Incorrect email login or password login');
                }
            }
        }



        $user = \App\User::findOrfail($request->user_id);

        $user->email = $request->email;
        $user->name = $request->name;
        $user->location = $request->location;
        $user->education = $request->education;
        $user->note = $request->note;
        $user->email_server = 1;
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
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
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
            'avatar'   => 'image',
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $user = \App\User::findOrfail($id);
            $user->delete();
            LaravelFlashSessionHelper::setFlashMessage("User {$user->name} deleted", 'success');


        return redirect('users');
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
    public function deleteMsg($id, Request $request){
//        $msg = Ajaxis::BtDeleting('Warning!!','Would you like to remove This?','/users/'. $id . '/delete');
        $msg = Ajaxis::BtDeleting( trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?','/users/'. $id . '/delete');
        if($request->ajax())
        {
            return $msg;
        }
    }
}
