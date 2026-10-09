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
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(\Illuminate\Http\Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), 
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * The user has been authenticated.
     * Root fix: Override to check permissions before redirecting to prevent redirect loops
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Check if user has permission to access dashboard
        try {
            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('dashboard.index')) {
                return redirect()->intended($this->redirectPath());
            }
        } catch (\Exception $e) {
            // If permission check fails, log and redirect anyway to prevent loops
            \Log::warning('Permission check failed during login redirect', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
        }

        // Root fix: Redirect to home even if permission check fails to prevent redirect loops
        // The middleware will handle showing appropriate error if needed
        return redirect()->intended($this->redirectPath());
    }
}
