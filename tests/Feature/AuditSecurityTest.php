<?php

namespace Tests\Feature;

use App\User;
use App\Task;
use App\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuditSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function loginWith(array $permissions): User
    {
        $user = User::factory()->create();
        foreach ($permissions as $permission) {
            $user->givePermissionTo(\Spatie\Permission\Models\Permission::findOrCreate($permission, 'web'));
        }
        $this->actingAs($user);
        Gate::before(function ($candidate, $ability) use ($user, $permissions) {
            return $candidate->id === $user->id && in_array($ability, $permissions);
        });
        return $user;
    }

    public function test_anonymous_api_requests_are_rejected(): void
    {
        foreach (['/api/tours', '/api/clients', '/api/tasks', '/api/users', '/api/dashboard', '/api/v1/getTours', '/api/v1/dashboard/tasks'] as $url) {
            $this->getJson($url)->assertUnauthorized();
        }
        $this->postJson('/api/v1/users/1/emails/send', [])->assertUnauthorized();
    }

    public function test_mailbox_identity_cannot_be_overridden(): void
    {
        $user = $this->loginWith(['email.main']);
        $this->postJson('/api/v1/users/' . ($user->id + 1) . '/emails', [])->assertForbidden();
    }

    public function test_read_permissions_do_not_allow_writes_or_bulk_delete(): void
    {
        $this->loginWith(['tour.index', 'task.index', 'clients.index', 'users.index']);
        $this->postJson('/api/tours/bulk-action', ['action' => 'delete', 'tour_ids' => [1]])->assertForbidden();
        $this->postJson('/api/tasks', [])->assertForbidden();
        $this->postJson('/api/clients', [])->assertForbidden();
        $this->postJson('/api/settings/cache/clear', [])->assertForbidden();
    }

    public function test_task_api_persists_actual_schema_fields_and_returns_validation_errors(): void
    {
        $user = $this->loginWith(['task.create', 'task.edit', 'task.index']);
        $status = \App\Status::factory()->create(['type' => 'task']);
        $this->postJson('/api/tasks', [])->assertUnprocessable();
        $response = $this->postJson('/api/tasks', ['content' => 'Audit task', 'status_id' => $status->id,
            'deadline' => '2027-01-01 12:00:00', 'assigned_to' => $user->id]);
        $this->assertSame(201, $response->status(), $response->getContent());
        $id = $response->json('data.id');
        $this->assertDatabaseHas('tasks', ['id' => $id, 'status' => (string) $status->id, 'assign' => (string) $user->id]);
        $this->getJson('/api/tasks')->assertOk()->assertJsonPath('total', 1);
        $this->putJson('/api/tasks/' . $id, ['content' => 'Changed task'])->assertOk();
        $this->getJson('/api/tasks')->assertOk()->assertJsonPath('data.0.content', 'Changed task');
        $this->patchJson('/api/tasks/' . $id . '/status', [])->assertUnprocessable();
    }

    public function test_package_descriptions_sanitize_legacy_and_new_html(): void
    {
        $package = new TourPackage();
        $package->description = '<p>Safe <strong>text</strong><script>alert(1)</script><a href="javascript:alert(1)">link</a></p>';
        $this->assertStringContainsString('<strong>text</strong>', $package->description);
        $this->assertStringNotContainsString('<script', $package->description);
        $this->assertStringNotContainsString('javascript:', $package->description);
        $package->setRawAttributes(['description' => '<img src=x onerror=alert(1)><p>Legacy</p>']);
        $this->assertStringNotContainsString('onerror', $package->description);
        $this->assertStringContainsString('Legacy', $package->description);
    }

    public function test_package_update_rejects_get_and_protected_fields(): void
    {
        $this->loginWith(['tour_package.edit']);
        $this->getJson('/package/api/ajax_update')->assertStatus(405);
        $this->postJson('/package/api/ajax_update', ['package_id' => 1, 'fieldName' => 'id', 'fieldValue' => '2'])->assertUnprocessable();
        $this->postJson('/package/api/ajax_update', ['package_id' => 999, 'fieldName' => 'description', 'fieldValue' => 'Safe'])->assertNotFound();
    }

    public function test_api_write_requires_csrf_outside_test_bypass(): void
    {
        $this->loginWith(['task.create']);
        $this->app->bind(\App\Http\Middleware\VerifyCsrfToken::class, function ($app) {
            return new class($app, $app['encrypter']) extends \App\Http\Middleware\VerifyCsrfToken {
                protected function runningUnitTests() { return false; }
            };
        });
        $this->withSession(['_token' => 'session-token'])->postJson('/api/tasks', [])->assertStatus(419);
        $this->withHeader('X-CSRF-TOKEN', 'session-token')->postJson('/api/tasks', [])->assertUnprocessable();
    }

    public function test_mail_attachments_are_private_and_owner_bound(): void
    {
        Storage::fake('local');
        $user = $this->loginWith(['email.main']);
        $response = $this->postJson('/api/v1/users/' . $user->id . '/attachment', ['attach' => ['name' => '../../payload.php', 'body' => base64_encode('<?php echo 1;')]]);
        $response->assertOk();
        $url = $response->getContent();
        $this->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->actingAs(User::factory()->create());
        $this->get($url)->assertForbidden();
        $this->assertCount(1, Storage::disk('local')->allFiles('mail-attachments'));
    }

    public function test_authorized_client_user_and_dashboard_endpoints_work(): void
    {
        $user = $this->loginWith(['clients.index', 'clients.show', 'clients.edit', 'users.index', 'users.edit', 'task.index', 'tour.index', 'dashboard.index']);
        $client = \App\Client::factory()->create();
        $this->getJson('/api/clients')->assertOk()->assertJsonPath('total', 1);
        $this->getJson('/api/clients/' . $client->id)->assertOk()->assertJsonPath('data.id', $client->id);
        $this->getJson('/api/clients/' . $client->id . '/tours')->assertOk();
        $this->getJson('/api/users/' . $user->id . '/tasks')->assertOk();
        $this->putJson('/api/users/' . $user->id, ['email' => 'invalid'])->assertUnprocessable();
        $this->putJson('/api/users/' . $user->id, ['name' => 'Changed User'])->assertOk();
        $this->getJson('/api/users')->assertOk()->assertJsonPath('data.0.name', 'Changed User');
        $this->getJson('/api/dashboard')->assertOk()->assertJsonPath('data.statistics.total_clients', 1);
        $this->getJson('/api/dashboard/statistics')->assertOk();
        $this->getJson('/api/dashboard/charts')->assertOk();
        $this->getJson('/api/dashboard/upcoming-deadlines')->assertOk();
    }

    public function test_active_user_search_and_permissions_are_enforced(): void
    {
        $this->loginWith(['users.index', 'users.edit']);
        $inactive = User::factory()->create(['name' => 'Matching inactive', 'is_active' => false]);
        $this->getJson('/api/users/search?q=Matching')->assertOk()->assertExactJson([]);
        $this->putJson('/api/users/' . $inactive->id, ['is_active' => true])->assertOk();
        $this->assertTrue($inactive->fresh()->is_active);
        $this->getJson('/api/users/search?q=Matching')->assertOk()->assertJsonCount(1);
    }

    public function test_client_create_and_update_use_bound_requests_and_fresh_lists(): void
    {
        $this->loginWith(['clients.create', 'clients.edit', 'clients.index']);
        \App\Country::create(['name' => 'Egypt', 'alias' => 'EG', 'phone_code' => '20']);
        $city = \App\City::create(['name' => 'Cairo', 'country' => 'EG', 'code' => 'cairo']);
        $data = ['name' => 'Audit Client', 'country' => 'EG', 'city' => $city->id,
            'address' => '123 Test Street', 'work_phone' => '123456789', 'work_email' => 'client@example.test'];
        $response = $this->postJson('/api/clients', $data);
        $this->assertSame(201, $response->status(), $response->getContent());
        $id = $response->json('data.id');
        $this->getJson('/api/clients')->assertOk()->assertJsonPath('data.0.name', 'Audit Client');
        $response = $this->putJson('/api/clients/' . $id, array_merge($data, ['name' => 'Updated Client']));
        $this->assertSame(200, $response->status(), $response->getContent());
        $this->getJson('/api/clients')->assertOk()->assertJsonPath('data.0.name', 'Updated Client');
    }

    public function test_legacy_delete_get_is_rejected_and_post_requires_csrf(): void
    {
        $this->loginWith(['hotel.destroy']);
        $this->getJson('/hotel/1/delete')->assertStatus(405);
        $this->app->bind(\App\Http\Middleware\VerifyCsrfToken::class, function ($app) {
            return new class($app, $app['encrypter']) extends \App\Http\Middleware\VerifyCsrfToken {
                protected function runningUnitTests() { return false; }
            };
        });
        $this->postJson('/hotel/1/delete', [])->assertStatus(419);
    }

    public function test_missing_legacy_permission_fails_closed(): void
    {
        $this->loginWith([]);
        $this->postJson('/package/api/ajax_update', ['package_id' => 1, 'fieldName' => 'description', 'fieldValue' => 'Denied'])->assertForbidden();
    }

    public function test_no_registered_route_points_to_a_missing_action(): void
    {
        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();
            if (strpos($action, '@') === false) continue;
            [$controller, $method] = explode('@', $action, 2);
            $this->assertTrue(method_exists($controller, $method), $route->uri() . ' -> ' . $action);
        }
    }
}
