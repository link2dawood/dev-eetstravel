<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.simple-login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $this->validateLogin($request);

        // Check if user exists and test password
        $user = \App\User::where('email', $request->input('email'))->first();
        
        // TEMPORARY DEBUG - Remove this after testing
        if (!$user) {
            return back()->withErrors([
                'email' => 'DEBUG: User not found in database with email: ' . $request->input('email')
            ])->withInput($request->only('email', 'remember'));
        }
        
        $receivedPassword = $request->input('password');
        $passwordCheck = \Illuminate\Support\Facades\Hash::check($receivedPassword, $user->password);
        
        if (!$passwordCheck) {
            // Test with the expected password
            $test123456 = \Illuminate\Support\Facades\Hash::check('123456', $user->password);
            
            return back()->withErrors([
                'email' => 'DEBUG: Password mismatch! ' .
                          'Received: "' . $receivedPassword . '" (length: ' . strlen($receivedPassword) . ')' .
                          ' | Expected "123456" works: ' . ($test123456 ? 'YES' : 'NO') .
                          ' | User: ' . $user->name . ' (ID: ' . $user->id . ')'
            ])->withInput($request->only('email', 'remember'));
        }

        // Try authentication
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If we reach here, Auth::attempt failed even though password matched
        return back()->withErrors([
            'email' => 'DEBUG: Password matches but Auth::attempt() failed. This might be due to user status or other constraints.'
        ])->withInput($request->only('email', 'remember'));
    }
}
