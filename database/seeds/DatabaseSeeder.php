<?php

use Illuminate\Database\Seeder;
// use Seed\CountriesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         $this->call(PermissionsSeeder::class);
         $this->call(RolesTableSeeder::class);
         $this->call(AdminUserSeeder::class);
         $this->call(CriteriaTableSeeder::class);
         $this->call(CriteriaTypeSeeder::class);
         $this->call(StatusTableSeeder::class);
         $this->call(StatusTypeSeeder::class);
         $this->call(RateTableSeeder::class);
         $this->call(RateTypeSeeder::class);
         $this->call(RoomTypesTableSeeder::class);
         $this->call(CurrencyRatesTableSeeder::class);
         $this->call(CurrenciesTableSeeder::class);
         $this->call(CountriesSeeder::class);
         $this->call(MainChatTableSeeder::class);
        $this->call(DriverPermissionsSeeder::class);
        $this->call(PermissionsBusAndClientSeeder::class);
        $this->call(PermissionsChatAndSuplierSearchSeeder::class);
        $this->call(PermissionsDashboard::class);
        $this->call(PermissionsCalculation::class);
        $this->call(PermissionsActivities::class);
        $this->call(PermissionsAnnouncements::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(PermissionsQuotation::class);
			$this->call(AddSettingsEmailFrequency::class);
			$this->call(AdditionalPermissions::class);
			$this->call(PermissionDelete::class);
			$this->call(PermissionHolidaySeeder::class);
			$this->call(PermissionNotificationSeeder::class);

        $event = factory(App\Event::class, 500)->create();
        $guide = factory(\App\Guide::class, 500)->create();
        $hotel = factory(\App\Hotel::class, 500)->create();
        $transfer = factory(\App\Transfer::class, 500)->create();
        $restaurant = factory(\App\Restaurant::class, 500)->create();
        $flights = factory(\App\Flight::class, 500)->create();
        $cruises = factory(\App\Cruises::class, 500)->create();



    }
}
