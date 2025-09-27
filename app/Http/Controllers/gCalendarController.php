<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Http\Request;

class gCalendarController extends Controller
{
    private $client;


    /**
     * gCalendarController constructor.
     */
    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setDeveloperKey('AIzaSyAf_19rJpARrFZH5i7Oj6rzW2vdEb4G5Vc');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=Calendar-12aea1d9b225.json');
        $client->useApplicationDefaultCredentials();

        $this->client = $client;
    }


    public function getCalendars(){
        $service =  new Google_Service_Calendar($this->client);
        $calendars = $service->calendarList->listCalendarList()->getItems();

        return $calendars;
    }
}
