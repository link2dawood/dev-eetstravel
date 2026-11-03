<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MailboxCredentials;
use App\Services\MailboxVerifier;

class SnappyMailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\EmailAccess::class, 'preventBackHistory']);
    }

    private function feature($name)
    {
        abort_unless(config('snappymail.features.' . $name, true), 403);
        abort_if(config('snappymail.security.require_https') && !request()->secure(), 403);
    }

    private function webmailUrl(): string
    {
        $url = rtrim((string)config('snappymail.url', '/snappymail'), '/');
        // Repair the historical /mail default on this bundled installation.
        if ($url === '/mail' && !is_dir(public_path('mail'))) $url = '/snappymail';
        return $url;
    }

    private function loadApi()
    {
        if (!defined('APP_DATA_FOLDER_PATH')) define('APP_DATA_FOLDER_PATH', rtrim(config('snappymail.data_path'), '/\\') . DIRECTORY_SEPARATOR);
        $_ENV['SNAPPYMAIL_INCLUDE_AS_API'] = true;
        $root = dirname(config('snappymail.path')) . DIRECTORY_SEPARATOR;
        if (!defined('APP_VERSION')) define('APP_VERSION', config('snappymail.version'));
        if (!defined('APP_INDEX_ROOT_PATH')) define('APP_INDEX_ROOT_PATH', $root);
        try { require_once $root . 'snappymail/v/' . APP_VERSION . '/include.php'; }
        finally { unset($_ENV['SNAPPYMAIL_INCLUDE_AS_API']); }
    }

    public function sso(Request $request)
    {
        $this->feature('sso_enabled');
        $user = Auth::user();
        if (!$user->email_login || !$user->email_password) return redirect()->route('snappymail.configure')->with('error', 'Configure your mailbox to open webmail.');
        try {
            $password = app(MailboxCredentials::class)->password($user);
            $this->loadApi();
            $hash = \RainLoop\Api::CreateUserSsoHash((string)$user->email_login, $password);
            if (!$hash) throw new \RuntimeException('Sign-in token unavailable');
            if (config('snappymail.logging.enabled')) Log::info('Webmail sign-in token created', ['user_id' => $user->id]);
            return redirect()->away(url($this->webmailUrl()) . '/?Sso&hash=' . rawurlencode($hash));
        } catch (\Throwable $e) {
            if (config('snappymail.logging.enabled')) Log::warning('Webmail sign-in unavailable', ['user_id' => $user->id]);
            return redirect()->route('snappymail.configure')->with('error', 'Webmail sign-in failed. Check your mailbox configuration.');
        }
    }

    public function direct()
    {
        $this->feature('direct_login');
        return redirect()->away(url($this->webmailUrl()) . '/');
    }

    public function admin()
    {
        $this->feature('admin_access');
        abort_unless(Auth::user()->hasRole('admin'), 403);
        return redirect()->away(url($this->webmailUrl()) . '/?admin');
    }

    public function configure()
    {
        $this->feature('user_config');
        return view('snappymail.configure', ['user' => Auth::user()]);
    }

    public function saveConfiguration(Request $request)
    {
        $this->feature('user_config');
        $data = $request->validate(['email_login' => 'required|string|email|max:191', 'email_password' => 'required|string|min:6|max:1024']);
        try { $verified = app(MailboxVerifier::class)->verify($data['email_login'], $data['email_password']); }
        catch (\Throwable $e) { $verified = false; }
        if (!$verified) throw \Illuminate\Validation\ValidationException::withMessages(['email_login' => 'Mailbox sign-in could not be verified. Check the address and password or try again later.']);
        $user = Auth::user();
        $user->email_login = $data['email_login'];
        $user->email_password = $data['email_password'];
        $user->save();
        return redirect()->route('snappymail.configure')->with('success', 'Mailbox verified and configuration saved. Open webmail when ready.');
    }

    public function status()
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);
        return response()->json(['installed' => is_file(config('snappymail.path')), 'url' => url($this->webmailUrl()) . '/', 'sso_enabled' => (bool)config('snappymail.features.sso_enabled')]);
    }

    public function gateway(Request $request, $path = '')
    {
        abort_unless(config('snappymail.features.sso_enabled') || config('snappymail.features.direct_login'), 403);
        abort_if(config('snappymail.security.require_https') && !$request->secure(), 403);
        abort_if(preg_match('~(?:^|/)(?:data|_data_)(?:/|$)~i', $path), 404);
        abort_unless(in_array(trim($path, '/'), ['', 'index.php'], true), 404);
        if (preg_match('~(?:^|[&/])admin(?:[&/=]|$)~i', urldecode($request->server('QUERY_STRING', '')))) {
            abort_unless(Auth::user()->hasRole('admin') && config('snappymail.features.admin_access'), 403);
        }
        $this->loadApi();
        // Enforce this inside the engine too, including custom admin keys/hosts and AJAX.
        \RainLoop\Api::Config()->Set('security', 'allow_admin_panel', Auth::user()->hasRole('admin') && config('snappymail.features.admin_access'));
        ob_start();
        \RainLoop\Service::Handle();
        return response(ob_get_clean());
    }
}
