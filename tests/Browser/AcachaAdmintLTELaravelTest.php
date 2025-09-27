<?php

namespace Tests\Browser;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use ReflectionException;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class AcachaAdmintLTELaravelTest.
 *
 * @package Tests\Browser
 */
class AcachaAdmintLTELaravelTest extends DuskTestCase
{
    use DatabaseMigrations;
/*
    public function testLandingPage()
    {
        dump('testLandingPage');
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('AdminTMS')
                ->assertSee('Sign in to start your session');
        });
    }
 */

    public function testLogin()
    {
        dump('testLogin');

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->resize(1920, 1080)
                ->type('email', 'admin@example.com')
                ->type('password', '123456')
                ->press('Sign In')
                ->assertPathIs('/home')
                ->assertSee('Dashboard');
        });
    }
 
    public function testHotelCreate()
    {
        dump('testHotelCreate');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            
            $browser->visit('/hotel')
                ->assertSee('Hotels')
                ->pause(500)
                ->press('New')                    
                ->assertSee('Hotel Create')
                ->type('#name', $faker->name . ' hotel')
                ->type('address_first', $faker->address)
                ->value('#select2-country-container', 'Germany')
                ->value('#country', 'DE')
                ->pause(500)
                ->value('#city_code', 'ChIJAVkDPzdOqEcRcDteW0YgIQQ')
                ->value('#city', 'Berlin, Germany')
                ->pause(500)
                ->press('Save')
                ->pause(1500)
                ->assertSee('Hotels List')
                ->assertPathIs('/hotel');
        });
    }
    
    public function testHotelEdit()
    {
        dump('testHotelEdit');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            
            $browser->visit('/hotel')
                ->assertSee('Hotels')
                ->pause(1000)
                ->press('.btn-primary')                    
                ->pause(1000)
                ->assertSee('Hotel Edit')
                ->type('#name', $faker->name . 'hotel')
                ->type('address_first', $faker->name . 'street')
                ->value('#country', 'DE')
                ->pause(500)
                ->value('#city_code', 'ChIJAVkDPzdOqEcRcDteW0YgIQQ')
                ->value('#city', 'Berlin, Germany')
                ->pause(500)
                ->press('Save')
                ->pause(1500)
                ->assertSee('Hotels List')
                ->assertPathIs('/hotel');
        });
    }
    
    public function testTourShowEdit()
    {
        dump('testTourShowEdit');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            $browser->visit('/tour')                 
                ->assertSee('Tours List')
                
                ->waitFor('.show-button')
                ->press('.show-button')
                ->assertSee('Tour')
                ->press('Edit')
                ->assertSee('Edit Tour')
                ->type('#name', $faker->name . ' tour')
                ->type('pax', rand(3,30))
                ->type('pax_free', rand(1,5))

                ->press('Save')
                ->assertSee('Tour');
        });
    }
    
    public function testTourCreate()
    {
        dump('testTourCreate');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            $browser->visit('/tour')
                ->assertSee('Tours List')
                ->waitFor('.btn-success')
                ->press('form > .btn-success')
                ->assertSee('Create Tour')
                ->type('name', $faker->name . ' tour')
                ->type('overview', $faker->name . ' owerview')
                ->type('remark', $faker->name . ' remark')
                ->type('departure_date', \Carbon\Carbon::now()->format('Y-m-d'))
                ->type('retirement_date', \Carbon\Carbon::now()->addDays(rand(3, 25))->format('Y-m-d'))
                ->value('#select2-country_from-container', 'Germany')
                ->value('#country_from', 'DE')
                
                ->value('#city_from', 'Berlin, Germany')                    
                ->value('#city_code_from', 'ChIJAVkDPzdOqEcRcDteW0YgIQQ')
                    
                ->value('#select2-country_to-container', 'France')
                ->value('#country_to', 'FR')
                
                ->value('#city_to', 'Paris, France')                    
                ->value('#city_code_to', 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ')
                    
                ->press('.select2-search__field')
                ->waitFor('.select2-results__option')
                ->press('.select2-results__option')

                ->type('pax', rand(3,30))
                ->type('pax_free', rand(1,5))                    
                    
                    
                ->press('.btn_for_select_room_type')
                ->waitFor('.select_room_type')
                
                ->press('.select_room_type')
                    
                ->waitFor('.block-qty-room > input')
                ->type('.block-qty-room > input', rand(1,5))
                    
                ->type('total_amount', rand(1,8))
                ->type('price_for_one', rand(18,60))
                    
                ->type('invoice', \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'))
                ->type('ga', \Carbon\Carbon::now()->addDays(rand(3, 25))->format('Y-m-d'))                    

                ->press('Save')
                ->assertSee('Tour');
        });
    }
    

    public function testTourAddTransfer()
    {
        dump('testTourAddTransfer');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            $browser->visit('/tour')
                ->assertSee('Tours List')
                ->waitFor('.show-button')
                ->press('.show-button')
                ->waitFor('.add-service-quick')
                ->press('.add-service-quick')
                 
                ->pause(1000)
                ->waitFor('.add-service-button')
                ->press('.add-service-button')
                    
                    
                ->pause(500)    
                ->waitFor('.addTransferPackageWithDate')
                ->press('.addTransferPackageWithDate')
                ->pause(500)        
                ->waitFor('.btn-send-transfer_add')
                ->press('.btn-send-transfer_add')
                        
                ->assertSee('Add Service');
        });
    }
       
    

    public function testTourAddServices()
    {
        dump('testTourAddServices');

        $this->browse(function (Browser $browser) {
            $faker = \Faker\Factory::create();
            $browser->visit('/tour')                
                ->assertSee('Tours List')
                ->waitFor('.show-button')
                ->press('.show-button')
                ->waitFor('.box-header > .add-service-quick')
                ->press('.box-header > .add-service-quick')
                    
                ->waitFor('#select2-service-select-container')
                ->press('#select2-service-select-container')
                    
                ->waitFor('.select2-results__option')
                ->press('.select2-results__option')
                    
                ->pause(3500);
//                 
//                ->pause(1000)
//                ->waitFor('.add-service-button')
//                ->press('.add-service-button')
//                    
//                    
//                ->pause(500)    
//                ->waitFor('.addTransferPackageWithDate')
//                ->press('.addTransferPackageWithDate')
//                ->pause(500)        
//                ->waitFor('.btn-send-transfer_add')
//                ->press('.btn-send-transfer_add')
//                        
//                ->assertSee('Add Service');
                        
                    
/*                    
                ->type('name', $faker->name . ' tour')
                ->type('overview', $faker->name . ' owerview')
                ->type('remark', $faker->name . ' remark')
                ->type('departure_date', \Carbon\Carbon::now()->format('Y-m-d'))
                ->type('retirement_date', \Carbon\Carbon::now()->addDays(rand(3, 25))->format('Y-m-d'))
                ->value('#select2-country_from-container', 'Germany')
                ->value('#country_from', 'DE')
                
                ->value('#city_from', 'Berlin, Germany')                    
                ->value('#city_code_from', 'ChIJAVkDPzdOqEcRcDteW0YgIQQ')
                    
                ->value('#select2-country_to-container', 'France')
                ->value('#country_to', 'FR')
                
                ->value('#city_to', 'Paris, France')                    
                ->value('#city_code_to', 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ')
                    
                ->press('.select2-search__field')
                ->waitFor('.select2-results__option')
                ->press('.select2-results__option')

                ->type('pax', rand(3,30))
                ->type('pax_free', rand(1,5))                    
                    
                    
                ->press('.btn_for_select_room_type')
                ->waitFor('.select_room_type')
                
                ->press('.select_room_type')
                    
                ->waitFor('.block-qty-room > input')
                ->type('.block-qty-room > input', rand(1,5))
                    
                ->type('total_amount', rand(1,8))
                ->type('price_for_one', rand(18,60))
                    
                ->type('invoice', \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'))
                ->type('ga', \Carbon\Carbon::now()->addDays(rand(3, 25))->format('Y-m-d'))                    

                ->press('Save')
                ->assertSee('Tour');
 * 
 */
        });
    }
        
    
    public function testLogout()
    {
        dump('logout');
        $this->browse(function (Browser $browser) {
            $browser->visit('/profile')
                ->pause(500)
                ->click('#logout');
        });
    }

    /**
     * Test Login required fields.
     *
     * @return void
     */
//    public function testLoginRequiredFields()
//    {
//        dump('testLoginRequiredFields');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/login')
//                ->type('email', '')
//                ->type('password', '')
//                ->press('Sign In')
//                ->pause(1000)
//                ->assertSee('The email field is required')
//                ->assertSee('The password field is required');
//        });
//    }

    /**
     * Test Login required fields errors disappears on key down.
     *
     * @return void
     */
//    public function testLoginRequiredFieldsDisappearsOnKeyDown()
//    {
//        dump('testLoginRequiredFieldsDissappearsOnKeyDown');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/login')
//                ->type('email', '')
//                ->type('password', '')
//                ->press('Sign In')
//                ->pause(1000)
//                ->type('email', 's')
//                ->waitUntilMissing('#validation_error_email')
//                ->assertDontSee('The email field is required')
//                ->type('password', 'p')
//                ->waitUntilMissing('#validation_error_password')
//                ->assertDontSee('The password field is required');
//        });
//    }

    /**
     * Test Login credentials not match.
     *
     * @return void
     */
//    public function testLoginCredentialsNotMatch()
//    {
//        dump('testLoginCredentialsNotMatch');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/login')
//                ->type('email', 'emailquesegurquenoexisteix@sadsadsa.com')
//                ->type('password', '12345678')
//                ->press('Sign In')
//                ->pause(1000)
//                ->assertSee('These credentials do not match our records');
//        });
//    }

    /**
     * Test Login credentials not match disappears on key down.
     *
     * @return void
     */
//    public function testLoginCredentialsNotMatchDissappearsOnKeyDown()
//    {
//        dump('testLoginCredentialsNotMatchDissappearsOnKeyDown');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/login')
//                ->type('email', 'emailquesegurquenoexisteix@sadsadsa.com')
//                ->type('password', '12345678')
//                ->press('Sign In')
//                ->pause(1000)
//                ->type('password', '1')
//                ->pause(1000)
//                ->assertDontSee('These credentials do not match our records');
//        });
//    }

    /**
     * Test register page.
     *
     * @return void
     */
//    public function testRegisterPage()
//    {
//        dump('testRegisterPage');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/register')
//                ->assertSee('Register a new membership');
//        });
//    }

    /**
     * Test Password reset Page.
     *
     * @return void
     */
//    public function testPasswordResetPage()
//    {
//        dump('testPasswordResetPage');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/password/reset')
//                ->assertSee('Reset Password');
//        });
//    }

    /**
     * Test home page is only for authorized Users.
     *
     * @return void
     */
//    public function testHomePageForUnauthenticatedUsers()
//    {
//        dump('testHomePageForUnauthenticatedUsers');
//        $this->browse(function (Browser $browser) {
//            $user = factory(\App\User::class)->create();
//            view()->share('user', $user);
//            $browser->visit('/home')
//                ->pause(2000)
//                ->assertPathIs('/login');
//        });
//    }

    /**
     * Test home page works with Authenticated Users.
     *
     * @return void
     */
//    public function testHomePageForAuthenticatedUsers()
//    {
//        dump('testHomePageForAuthenticatedUsers');
//
//        $this->browse(function (Browser $browser) {
//            $user = factory(\App\User::class)->create();
//            view()->share('user', $user);
//            $browser->loginAs($user)
//                ->visit('/home')
//                ->assertSee($user->name);
//        });
//
//        $this->logout();
//    }

    /**
     * Test log out.
     * @group shit
     * @return void
     */
//    public function testLogout()
//    {
//        dump('testLogout');
//        $this->browse(function (Browser $browser) {
//            $user = factory(\App\User::class)->create();
//            view()->share('user', $user);
//            $browser->loginAs($user)
//                ->visit('/home')
//                ->click('#user_menu')
//                ->pause(500)
//                ->click('#logout')
//                ->pause(500);
//        });
//    }

    /**
     * Test 404 Error page.
     *
     * @return void
     */
//    public function test404Page()
//    {
//        dump('test404Page');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/asdasdjlapmnnkadsdsa')
//                ->assertSee('404');
//        });
//    }

    /**
     * Test user registration.
     *
     * @return void
     */
//    public function testNewUserRegistration()
//    {
//        dump('testNewUserRegistration');
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/register')
//                ->type('name', 'Sergi Tur Badenas')
//                ->type('email', 'sergiturbadenas@gmail.com')
//                ->click('.icheckbox_square-blue')
//                ->type('password', 'passw0RD')
//                ->type('password_confirmation', 'passw0RD')
//                ->press('Register')
//                ->waitFor('#result')
//                ->pause(5000)
//                ->assertPathIs('/home')
//                ->assertSee('Sergi Tur Badenas');
//        });
//
//        $this->logout();
//    }

    /**
     * Test new user registration required fields.
     *
     * @return void
     */
//    public function testNewUserRegistrationRequiredFields()
//    {
//        dump('testNewUserRegistrationRequiredFields');
//
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/register')
//                ->type('name', '')
//                ->type('email', '')
//                ->type('password', '')
//                ->press('Register')
//                ->pause(1000)
//                ->assertSee('The name field is required')
//                ->assertSee('The email field is required')
//                ->assertSee('The password field is required')
//                ->assertSee('The terms field is required');
//        });
//    }

    /**
     * Test new user registration required fields disappears on key down.
     *
     * @return void
     */
//    public function testNewUserRegistrationRequiredFieldsDissappearsOnKeyDown()
//    {
//        dump('testNewUserRegistrationRequiredFieldsDissappearsOnKeyDown');
//
//        $this->browse(function (Browser $browser) {
//            $browser->visit('/register')
//                ->type('name', '')
//                ->type('email', '')
//                ->type('password', '')
//                ->press('Register')
//                ->pause(2000)
//                ->type('name', 'S')
//                ->pause(2000)
//                ->assertDontSee('The name field is required')
//                ->type('email', 's')
//                ->pause(2000)
//                ->assertDontSee('The email field is required')
//                ->type('password', 'p')
//                ->pause(2000)
//                ->assertDontSee('The password field is required')
//                ->click('.icheckbox_square-blue')
//                ->pause(2000)
//                ->assertDontSee('The terms field is required');
//        });
//    }

    /**
     * Test send password reset.
     *
     * @return void
     */
//    public function testSendPasswordReset()
//    {
//        dump('testSendPasswordReset');
//
//        $this->browse(function (Browser $browser) {
//            $user = factory(\App\User::class)->create();
//            $browser->visit('password/reset')
//                ->type('email', $user->email)
//                ->press('Send Password Reset Link')
//                ->waitFor('#result')
//                ->pause(1000)
//                ->assertSee('We have e-mailed your password reset link!');
//        });
//    }

    /**
     * Test send password reset user not exists.
     *
     * @return void
     */
//    public function testSendPasswordResetUserNotExists()
//    {
//        dump('testSendPasswordResetUserNotExists');
//
//        $this->browse(function (Browser $browser) {
//            $browser->visit('password/reset')
//                ->type('email', 'notexistingemail@gmail.com')
//                ->press('Send Password Reset Link')
//                ->pause(1000)
//                ->assertSee('We can\'t find a user with that e-mail address.');
//        });
//    }

    /**
     * Test send password reset required fields.
     *
     * @return void
     */
//    public function testSendPasswordResetRequiredFields()
//    {
//        dump('testSendPasswordResetRequiredFields');
//
//        $this->browse(function (Browser $browser) {
//            $browser->visit('password/reset')
//                ->press('Send Password Reset Link')
//                ->pause(1000)
//                ->assertSee('The email field is required.');
//        });
//    }

    /**
     * Test send password reset required fields dissapears on key down.
     *
     * @return void
     */
//    public function testSendPasswordResetRequiredFieldsDisappearsOnKeyDown()
//    {
//        dump('testSendPasswordResetRequiredFieldsDisappearsOnKeyDown');
//
//        $this->browse(function (Browser $browser) {
//            $browser->visit('password/reset')
//                ->type('email', '')
//                ->press('Send Password Reset Link')
//                ->pause(1000)
//                ->type('email', 's')
//                ->pause(2000)
//                ->assertDontSee('The email field is required.');
//        });
//    }
}
