<?php
namespace App\Http\Controllers;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;
class gCalendarController extends Controller {
    public function __construct() {
        $this->middleware(['auth','can:dashboard.index']);
    }
    private function client(Request $request) {
        $client = new Google_Client();
        $client->setApplicationName(config('app.name'));
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $credentials = config('services.google_calendar.credentials');
        if ($credentials && is_file($credentials)) {
            $client->setAuthConfig($credentials);
        } elseif (config('services.google_calendar.client_id') && config('services.google_calendar.client_secret')) {
            $client->setClientId(config('services.google_calendar.client_id'));
            $client->setClientSecret(config('services.google_calendar.client_secret'));
            $client->setRedirectUri(route('cal.oauth'));
            $token = $request->session()->get('google_calendar.token');
            if ($token) $client->setAccessToken($token);
        } else abort(503, 'Google Calendar is not configured.');
        return $client;
    }
    public function oauth(Request $request) {
        $client = $this->client($request);
        if ($request->has('code')) {
            $state = $request->session()->pull('google_calendar.state');
            abort_unless(is_string($state) && hash_equals($state, (string)$request->input('state')), 403);
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            abort_if(isset($token['error']), 422, 'Google Calendar authorization failed.');
            $request->session()->put('google_calendar.token', $token);
            return redirect('/home');
        }
        $state = bin2hex(random_bytes(24));
        $request->session()->put('google_calendar.state',$state);
        $client->setState($state);
        return redirect()->away($client->createAuthUrl());
    }
    public function getCalendars(Request $request) {
        try {
            return (new Google_Service_Calendar($this->client($request)))->calendarList->listCalendarList()->getItems();
        } catch (\Google\Service\Exception $e) {
            report($e);
            return response()->json(['message'=>'Google Calendar is unavailable. Reconnect the calendar and try again.'],503);
        }
    }
    public function getCalendarListJson(Request $request) { return $this->getCalendars($request); }
}
