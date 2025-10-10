<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\User;

class SnappyMailController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('preventBackHistory');
    }

    /**
     * SSO Login to SnappyMail
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sso(Request $request)
    {
        $user = Auth::user();

        // Check if user has email credentials configured
        if (!$user || !$user->email_login) {
            return redirect()->back()->with('error', 'Email credentials not configured. Please contact administrator.');
        }

        try {
            // Generate SSO hash for SnappyMail
            $ssoHash = $this->generateSSOHash($user);

            // Log SSO attempt
            Log::info('SnappyMail SSO attempt', [
                'user_id' => $user->id,
                'email' => $user->email_login
            ]);

            // Redirect to SnappyMail with SSO token
            $snappymailUrl = config('snappymail.url', '/mail');
            return redirect($snappymailUrl . '/?sso&hash=' . $ssoHash);

        } catch (\Exception $e) {
            Log::error('SnappyMail SSO error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to connect to webmail. Please try again.');
        }
    }

    /**
     * Generate SSO hash for SnappyMail authentication
     *
     * @param User $user
     * @return string
     */
    private function generateSSOHash(User $user)
    {
        $ssoKey = config('snappymail.sso_key');

        if (empty($ssoKey)) {
            throw new \Exception('SnappyMail SSO key not configured');
        }

        // Prepare SSO data
        $ssoData = [
            'Email' => $user->email_login,
            'Password' => $this->getEmailPassword($user),
            'Time' => time(),
            'Name' => $user->name ?? $user->email_login,
        ];

        // Create SSO hash using HMAC SHA-256
        $dataString = json_encode($ssoData);
        $hash = hash_hmac('sha256', $dataString, $ssoKey);

        // Return base64 encoded data with hash
        return base64_encode(json_encode([
            'data' => $ssoData,
            'hash' => $hash
        ]));
    }

    /**
     * Get email password for user (decrypt if encrypted)
     *
     * @param User $user
     * @return string
     */
    private function getEmailPassword(User $user)
    {
        // If password is encrypted in database
        if ($user->email_password_encrypted ?? false) {
            try {
                return Crypt::decryptString($user->email_password);
            } catch (\Exception $e) {
                Log::error('Failed to decrypt email password', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Email password decryption failed');
            }
        }

        // Return plain password (if stored in env or config)
        return $user->email_password ?? env('IMAP_PASSWORD', '');
    }

    /**
     * Direct link to SnappyMail (without SSO)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function direct()
    {
        $snappymailUrl = config('snappymail.url', '/mail');
        return redirect($snappymailUrl);
    }

    /**
     * Admin panel access (requires admin role)
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function admin()
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Admin access required');
        }

        $adminUrl = config('snappymail.admin_url', '/mail/?admin');
        return redirect($adminUrl);
    }

    /**
     * Configure email settings for current user
     *
     * @return \Illuminate\View\View
     */
    public function configure()
    {
        $user = Auth::user();

        return view('snappymail.configure', [
            'user' => $user,
            'imapHost' => env('IMAP_HOST', ''),
            'imapPort' => env('IMAP_PORT', 993),
            'imapEncryption' => env('IMAP_ENCRYPTION', 'ssl'),
        ]);
    }

    /**
     * Save email configuration for user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'email_login' => 'required|email',
            'email_password' => 'required|min:6',
        ]);

        $user = Auth::user();

        // Encrypt password before storing
        $user->email_login = $request->email_login;
        $user->email_password = Crypt::encryptString($request->email_password);
        $user->email_password_encrypted = true;
        $user->save();

        return redirect()->route('snappymail.sso')
            ->with('success', 'Email configuration saved successfully');
    }

    /**
     * Check SnappyMail status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        $snappymailPath = public_path('snappymail/index.php');
        $isInstalled = file_exists($snappymailPath);

        return response()->json([
            'installed' => $isInstalled,
            'url' => config('snappymail.url', '/mail'),
            'sso_configured' => !empty(config('snappymail.sso_key')),
        ]);
    }
}
