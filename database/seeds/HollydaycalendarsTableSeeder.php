<?php

use Illuminate\Database\Seeder;
use App\Hollydaycalendar;

class HollydaycalendarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendars = [
            [ 
                'name'              => 'Austrian Holidays',
                'googleCalendarId'  => 'en.austrian#holiday@group.v.calendar.google.com',
                'color'             => 'purple'
            ],
            
            [ 
                'name'              => 'Hungarian Holidays',
                'googleCalendarId'  => 'en.hungarian#holiday@group.v.calendar.google.com',
                'color'             => 'orange'
            ],
            
            [ 
                'name'              => 'Germanian Holidays',
                'googleCalendarId'  => 'en.german#holiday@group.v.calendar.google.com',
                'color'             => 'blue'
            ],
            
            [ 
                'name'              => 'China Holidays',
                'googleCalendarId'  => 'en.china#holiday@group.v.calendar.google.com',
                'color'             => 'green'
            ],
            
            [
                'name'              => 'Australian Holidays',
                'googleCalendarId'  => 'en.australian#holiday@group.v.calendar.google.com',
                'color'             => '#B0171F',
            ],
            
            [
                'name'              => 'Brazilian Holidays',
                'googleCalendarId'  => 'en.brazilian#holiday@group.v.calendar.google.com',
                'color'             => '#CD8C95',
            ],
            
            [
                'name'              => 'Canadian Holidays',
                'googleCalendarId'  => 'en.canadian#holiday@group.v.calendar.google.com',
                'color'             => '#DA70D6',
            ],
            
            [
                'name'              => 'Danish Holidays',
                'googleCalendarId'  => 'en.danish#holiday@group.v.calendar.google.com',
                'color'             => '#8B7B8B',
            ],
            
            [
                'name'              => 'Finnish Holidays',
                'googleCalendarId'  => 'en.finnish#holiday@group.v.calendar.google.com',
                'color'             => '#BF3EFF',
            ],
            
            [
                'name'              => 'French Holidays',
                'googleCalendarId'  => 'en.french#holiday@group.v.calendar.google.com',
                'color'             => '#8470FF',
            ],
            
            [
                'name'              => 'Greek Holidays',
                'googleCalendarId'  => 'en.greek#holiday@group.v.calendar.google.com',
                'color'             => '#6495ED',
            ],
                        
         
            [
                'name'              => 'Indian Holidays',
                'googleCalendarId'  => 'en.indian#holiday@group.v.calendar.google.com',
                'color'             => '#1E90FF',
            ],


            [
                'name'              => 'Irish Holidays',
                'googleCalendarId'  => 'en.irish#holiday@group.v.calendar.google.com',
                'color'             => '#87CEEB',
            ],
            
            [
                'name'              => 'Italian Holidays',
                'googleCalendarId'  => 'en.italian#holiday@group.v.calendar.google.com',
                'color'             => '#00E5EE',
            ],
            
            [
                'name'              => 'Japanese Holidays',
                'googleCalendarId'  => 'en.japanese#holiday@group.v.calendar.google.com',
                'color'             => '#B0E0E6',
            ],
                    
            [
                'name'              => 'Jewish Holidays',
                'googleCalendarId'  => 'en.jewish#holiday@group.v.calendar.google.com',
                'color'             => '#00CD66',
            ],
            
            [
                'name'              => 'Malaysian Holidays',
                'googleCalendarId'  => 'en.malaysia#holiday@group.v.calendar.google.com',
                'color'             => '#76EE00',
            ],
                    
            
            [
                'name'              => 'Mexican Holidays',
                'googleCalendarId'  => 'en.mexican#holiday@group.v.calendar.google.com',
                'color'             => '#A2CD5A',
            ],
            
            [
                'name'              => 'New Zealand Holidays',
                'googleCalendarId'  => 'en.new_zealand#holiday@group.v.calendar.google.com',
                'color'             => '#CDC673',
            ],
            
            [
                'name'              => 'Norwegian Holidays',
                'googleCalendarId'  => 'en.norwegian#holiday@group.v.calendar.google.com',
                'color'             => '#CDBE70',
            ],
            
            [
                'name'              => 'Philippines Holidays',
                'googleCalendarId'  => 'en.philippines#holiday@group.v.calendar.google.com',
                'color'             => '#CDAD00',
            ],
            
            [
                'name'              => 'Portuguese Holidays',
                'googleCalendarId'  => 'en.portuguese#holiday@group.v.calendar.google.com',
                'color'             => '	#B8860B',
            ],
        
            [
                'name'              => 'Russian Holidays',
                'googleCalendarId'  => 'en.russian#holiday@group.v.calendar.google.com',
                'color'             => '#CDAF95',
            ],
            
            [
                'name'              => 'Singapore Holidays',
                'googleCalendarId'  => 'en.singapore#holiday@group.v.calendar.google.com',
                'color'             => '#BC8F8F',
            ],
            
            [
                'name'              => 'South Africa Holidays',
                'googleCalendarId'  => 'en.sa#holiday@group.v.calendar.google.com',
                'color'             => '#EE0000',
            ],
            
            [
                'name'              => 'South Korean Holidays',
                'googleCalendarId'  => 'en.south_korea#holiday@group.v.calendar.google.com',
                'color'             => '#C67171',
            ],
            
            [
                'name'              => 'Spain Holidays',
                'googleCalendarId'  => 'en.spain#holiday@group.v.calendar.google.com',
                'color'             => '#B8B8B8	',
            ],
                    
            [
                'name'              => 'Swedish Holidays',
                'googleCalendarId'  => 'en.swedish#holiday@group.v.calendar.google.com',
                'color'             => '#FF7D40',
            ],
        
            
            [
                'name'              => 'Taiwan Holidays',
                'googleCalendarId'  => 'en.taiwan#holiday@group.v.calendar.google.com',
                'color'             => '#CDAA7D',
            ],

            
            [
                'name'              => 'UK Holidays',
                'googleCalendarId'  => 'en.uk#holiday@group.v.calendar.google.com',
                'color'             => '#EEC900',
            ],
            
            [
                'name'              => 'US Holidays',
                'googleCalendarId'  => 'en.usa#holiday@group.v.calendar.google.com',
                'color'             => '#FFD700',
            ],
                    
            [
                'name'              => 'Vietnamese Holidays',
                'googleCalendarId'  => 'en.vietnamese#holiday@group.v.calendar.google.com',
                'color'             => '#B3EE3A',
            ]
        ];
            
        foreach($calendars as $calendar){
            Hollydaycalendar::create([
                'name'             => $calendar['name'],
                'googlecalendarid'  => $calendar['googleCalendarId'],
                'color'             => $calendar['color'],
            ]);            
        }
    }
}
