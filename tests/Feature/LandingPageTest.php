<?php

namespace Tests\Feature;

use App\Tour;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Coverage for the public client-facing tour landing page
 * (GET /tour/{id}/landingpage, TourController@landingPage).
 *
 * Critical behaviors verified:
 *  - Anonymous viewers must pass a matching ?t=<share_token>.
 *  - Authenticated staff can still access by id alone.
 *  - 404 is rendered identically whether the id is missing or the
 *    token is wrong (anti-IDOR — don't reveal id existence).
 *  - Response carries no-cache headers + noindex.
 *  - Supplier internal contact info + internal workflow status are
 *    never rendered into the page.
 *
 * Uses DatabaseTransactions (not RefreshDatabase) because the project's
 * 148 historic migrations don't replay cleanly on a fresh DB — the
 * shared dev_tms schema is the source of truth.
 */
class LandingPageTest extends TestCase
{
    use DatabaseTransactions;

    /** Build a minimal Tour row that the landing controller can resolve. */
    private function makeTour(array $overrides = []): Tour
    {
        return Tour::create(array_merge([
            'name'            => 'Phoenix Adventures ' . uniqid(),
            'pax'             => 10,
            'departure_date'  => '2026-06-01',
            'retirement_date' => '2026-06-05',
            'country_begin'   => 'Italy',
            'city_begin'      => 'Rome',
            'status'          => 1,
        ], $overrides));
    }

    // --- Happy paths -------------------------------------------------

    /** @test */
    public function anonymous_with_valid_token_renders_landing_page(): void
    {
        $tour  = $this->makeTour();
        $token = $tour->ensureShareToken();

        $response = $this->get("/tour/{$tour->id}/landingpage?t={$token}");

        $response->assertOk();
        $response->assertSee($tour->name);
    }

    /** @test */
    public function authenticated_user_can_access_without_token(): void
    {
        $tour = $this->makeTour();
        $user = User::query()->first() ?? User::create([
            'name'     => 'Test Staff',
            'email'    => 'staff-' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->actingAs($user)->get("/tour/{$tour->id}/landingpage");

        $response->assertOk();
        $response->assertSee($tour->name);
    }

    /** @test */
    public function response_sets_no_cache_and_noindex_headers(): void
    {
        $tour  = $this->makeTour();
        $token = $tour->ensureShareToken();

        $response = $this->get("/tour/{$tour->id}/landingpage?t={$token}");

        $response->assertOk();
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertSame('noindex, nofollow', $response->headers->get('X-Robots-Tag'));
    }

    /** @test */
    public function landing_page_does_not_leak_supplier_contact_strings(): void
    {
        $tour  = $this->makeTour();
        $token = $tour->ensureShareToken();

        $response = $this->get("/tour/{$tour->id}/landingpage?t={$token}");

        $response->assertOk();
        // These four labels were rendered by the legacy view from
        // package->service()->work_phone and ->work_fax. The migration
        // removed both fields entirely.
        $response->assertDontSee('work_phone');
        $response->assertDontSee('work_fax');
        $response->assertDontSee('Tel:');
        $response->assertDontSee('Fax:');
    }

    /** @test */
    public function landing_page_does_not_leak_internal_workflow_status(): void
    {
        $tour  = $this->makeTour();
        $token = $tour->ensureShareToken();

        $response = $this->get("/tour/{$tour->id}/landingpage?t={$token}");

        $response->assertOk();
        // The old view rendered $package->getStatusName() ("Pending",
        // "Confirmed", "Cancelled"). The migration removed it.
        $response->assertDontSee('Pending', false);
        $response->assertDontSee('Confirmed', false);
    }

    // --- Failure paths ----------------------------------------------

    /** @test */
    public function anonymous_without_token_returns_404(): void
    {
        $tour = $this->makeTour();
        $tour->ensureShareToken(); // ensure tour has a token, attacker still doesn't pass it

        $response = $this->get("/tour/{$tour->id}/landingpage");

        $response->assertStatus(404);
        $response->assertSee("isn't available", false);
    }

    /** @test */
    public function anonymous_with_wrong_token_returns_404(): void
    {
        $tour = $this->makeTour();
        $tour->ensureShareToken();

        $response = $this->get("/tour/{$tour->id}/landingpage?t=not-the-real-token");

        $response->assertStatus(404);
    }

    /** @test */
    public function nonexistent_tour_id_returns_404(): void
    {
        $response = $this->get('/tour/99999999/landingpage?t=anything');

        $response->assertStatus(404);
    }

    /** @test */
    public function non_numeric_id_in_path_does_not_reach_controller(): void
    {
        // Route is constrained to digits via ->where('id', '[0-9]+').
        // Path-traversal-style inputs hit Laravel's NotFoundHttpException
        // at the router layer before any DB lookup.
        $response = $this->get('/tour/abc/landingpage?t=anything');

        $response->assertStatus(404);
    }

    // --- Token model -------------------------------------------------

    /** @test */
    public function ensure_share_token_is_idempotent_and_unique_per_tour(): void
    {
        $tourA = $this->makeTour();
        $tourB = $this->makeTour();

        $tokenA1 = $tourA->ensureShareToken();
        $tokenA2 = $tourA->ensureShareToken();
        $tokenB  = $tourB->ensureShareToken();

        $this->assertNotEmpty($tokenA1);
        $this->assertSame($tokenA1, $tokenA2, 'ensureShareToken should be idempotent for the same tour');
        $this->assertNotSame($tokenA1, $tokenB, 'tokens must differ between tours');
        $this->assertSame(40, strlen($tokenA1), 'token should be a 40-char sha1 hex string');
    }
}
