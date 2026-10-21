<?php
namespace Tests\Feature;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
/** Current authentication coverage replacing obsolete AdminLTE generator tests. */
class AcachaAdminLTELaravelTest extends TestCase {
    use RefreshDatabase;
    public function test_guests_are_redirected_to_login(): void {
        $this->get('/')->assertRedirect('/login');
        $this->get('/home')->assertRedirect('/login');
    }
    public function test_login_and_registration_forms_render(): void {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/password/reset')->assertOk();
    }
    public function test_registration_creates_an_authenticated_user(): void {
        $this->post('/register',['name'=>'Test User','email'=>'new@example.test','password'=>'safe-password','password_confirmation'=>'safe-password'])->assertRedirect('/home');
        $this->assertDatabaseHas('users',['email'=>'new@example.test']);
        $this->assertAuthenticated();
    }
    public function test_registration_rejects_missing_fields(): void {
        $this->postJson('/register',[])->assertUnprocessable()->assertJsonValidationErrors(['name','email','password']);
    }
    public function test_login_accepts_valid_credentials(): void {
        $user=User::factory()->create(['password'=>Hash::make('safe-password')]);
        $this->post('/login',['email'=>$user->email,'password'=>'safe-password'])->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
    public function test_login_rejects_invalid_credentials(): void {
        $user=User::factory()->create();
        $this->postJson('/login',['email'=>$user->email,'password'=>'wrong-password'])->assertUnprocessable();
        $this->assertGuest();
    }
    public function test_unknown_pages_return_404(): void { $this->get('/missing-audit-page')->assertNotFound(); }
}
