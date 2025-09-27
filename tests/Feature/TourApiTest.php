<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Tour;
use App\User;
use App\Status;
use App\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class TourApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create test statuses
        Status::factory()->create(['id' => 1, 'name' => 'Draft', 'type' => 'tour']);
        Status::factory()->create(['id' => 2, 'name' => 'Active', 'type' => 'tour']);
        Status::factory()->create(['id' => 3, 'name' => 'Cancelled', 'type' => 'tour']);
        Status::factory()->create(['id' => 4, 'name' => 'Completed', 'type' => 'tour']);
    }

    /** @test */
    public function it_requires_authentication_for_api_access()
    {
        $response = $this->getJson('/api/tours');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_list_tours()
    {
        Sanctum::actingAs($this->user);

        Tour::factory()->count(3)->create();

        $response = $this->getJson('/api/tours');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'tours' => [
                            '*' => [
                                'id',
                                'name',
                                'departure_date',
                                'pax',
                                'status',
                                'urls'
                            ]
                        ]
                    ],
                    'meta',
                    'links'
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function it_can_create_a_tour_via_api()
    {
        Sanctum::actingAs($this->user);

        $client = Client::factory()->create();

        $tourData = [
            'name' => 'API Test Tour',
            'overview' => 'A tour created via API',
            'departure_date' => now()->addDays(30)->toDateString(),
            'retirement_date' => now()->addDays(37)->toDateString(),
            'pax' => 20,
            'rooms' => 10,
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'country_end' => 'Egypt',
            'city_end' => 'Luxor',
            'status' => 1,
            'client_id' => $client->id,
            'responsible_users' => [$this->user->id],
        ];

        $response = $this->postJson('/api/tours', $tourData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'pax',
                        'status',
                        'client',
                        'responsible_users'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'API Test Tour',
                        'pax' => 20
                    ]
                ]);

        $this->assertDatabaseHas('tours', [
            'name' => 'API Test Tour',
            'pax' => 20
        ]);
    }

    /** @test */
    public function it_validates_tour_creation_data()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'name' => '', // Required field missing
            'departure_date' => 'invalid-date', // Invalid date format
            'pax' => -5, // Invalid passenger count
        ];

        $response = $this->postJson('/api/tours', $invalidData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ])
                ->assertJson([
                    'success' => false
                ]);
    }

    /** @test */
    public function it_can_show_a_tour()
    {
        Sanctum::actingAs($this->user);

        $tour = Tour::factory()->create();

        $response = $this->getJson("/api/tours/{$tour->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'name',
                        'departure_date',
                        'pax',
                        'status',
                        'statistics',
                        'permissions',
                        'urls'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $tour->id,
                        'name' => $tour->name
                    ]
                ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_tour()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/tours/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_a_tour()
    {
        Sanctum::actingAs($this->user);

        $tour = Tour::factory()->create([
            'name' => 'Original Name',
            'pax' => 15
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'pax' => 25,
            'responsible_users' => [$this->user->id],
        ];

        $response = $this->putJson("/api/tours/{$tour->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Updated Name',
                        'pax' => 25
                    ]
                ]);

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'name' => 'Updated Name',
            'pax' => 25
        ]);
    }

    /** @test */
    public function it_can_delete_a_tour()
    {
        Sanctum::actingAs($this->user);

        $tour = Tour::factory()->create([
            'departure_date' => now()->addDays(30)->toDateString()
        ]);

        $response = $this->deleteJson("/api/tours/{$tour->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertSoftDeleted('tours', [
            'id' => $tour->id
        ]);
    }

    /** @test */
    public function it_can_clone_a_tour()
    {
        Sanctum::actingAs($this->user);

        $originalTour = Tour::factory()->create([
            'name' => 'Original Tour'
        ]);

        $cloneData = [
            'departure_date' => now()->addDays(30)->toDateString(),
            'name' => 'Cloned Tour'
        ];

        $response = $this->postJson("/api/tours/{$originalTour->id}/clone", $cloneData);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => 'Cloned Tour'
                    ]
                ]);

        $this->assertDatabaseHas('tours', [
            'name' => 'Cloned Tour'
        ]);
    }

    /** @test */
    public function it_can_search_tours()
    {
        Sanctum::actingAs($this->user);

        Tour::factory()->create(['name' => 'Egypt Adventure Tour']);
        Tour::factory()->create(['name' => 'Turkey Cultural Tour']);
        Tour::factory()->create(['name' => 'Egypt Luxury Tour']);

        $response = $this->getJson('/api/tours/search?q=Egypt');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'meta' => [
                        'query' => 'Egypt',
                        'total_results' => 2
                    ]
                ]);

        $tours = $response->json('data');
        $this->assertCount(2, $tours);
    }

    /** @test */
    public function it_can_get_tour_statistics()
    {
        Sanctum::actingAs($this->user);

        // Create test data
        Tour::factory()->create(['status' => 2, 'pax' => 10]);
        Tour::factory()->create(['status' => 4, 'pax' => 15]);

        $response = $this->getJson('/api/tours/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_tours',
                        'active_tours',
                        'completed_tours',
                        'cancelled_tours',
                        'total_passengers'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function it_can_get_calendar_data()
    {
        Sanctum::actingAs($this->user);

        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        Tour::factory()->create([
            'name' => 'Calendar Tour',
            'departure_date' => now()->addDays(10)->toDateString(),
            'retirement_date' => now()->addDays(15)->toDateString()
        ]);

        $response = $this->getJson("/api/tours/calendar?start={$startDate}&end={$endDate}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'start',
                            'end',
                            'backgroundColor',
                            'extendedProps'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function it_can_perform_bulk_actions()
    {
        Sanctum::actingAs($this->user);

        $tours = Tour::factory()->count(3)->create([
            'departure_date' => now()->addDays(30)->toDateString()
        ]);

        $tourIds = $tours->pluck('id')->toArray();

        $bulkData = [
            'action' => 'update_status',
            'tour_ids' => $tourIds,
            'status' => 2
        ];

        $response = $this->postJson('/api/tours/bulk-action', $bulkData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'success' => 3,
                        'failed' => 0
                    ]
                ]);

        foreach ($tours as $tour) {
            $this->assertDatabaseHas('tours', [
                'id' => $tour->id,
                'status' => 2
            ]);
        }
    }

    /** @test */
    public function it_filters_tours_by_status()
    {
        Sanctum::actingAs($this->user);

        Tour::factory()->create(['status' => 1]); // Draft
        Tour::factory()->create(['status' => 2]); // Active
        Tour::factory()->create(['status' => 2]); // Active

        $response = $this->getJson('/api/tours?status=2');

        $response->assertStatus(200);

        $tours = $response->json('data.tours');
        $this->assertCount(2, $tours);

        foreach ($tours as $tour) {
            $this->assertEquals(2, $tour['status']['id']);
        }
    }

    /** @test */
    public function it_filters_tours_by_date_range()
    {
        Sanctum::actingAs($this->user);

        $futureDate = now()->addDays(30)->toDateString();
        $pastDate = now()->subDays(30)->toDateString();

        Tour::factory()->create(['departure_date' => $futureDate]);
        Tour::factory()->create(['departure_date' => $pastDate]);

        $response = $this->getJson("/api/tours?departure_from={$futureDate}");

        $response->assertStatus(200);

        $tours = $response->json('data.tours');
        $this->assertCount(1, $tours);
        $this->assertEquals($futureDate, $tours[0]['departure_date']);
    }

    /** @test */
    public function it_includes_pagination_metadata()
    {
        Sanctum::actingAs($this->user);

        Tour::factory()->count(25)->create();

        $response = $this->getJson('/api/tours?per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'meta' => [
                        'total',
                        'per_page',
                        'current_page',
                        'last_page'
                    ],
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next'
                    ]
                ]);

        $meta = $response->json('meta');
        $this->assertEquals(25, $meta['total']);
        $this->assertEquals(10, $meta['per_page']);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Sanctum::actingAs($this->user);

        // Try to update a tour with invalid status transition
        $tour = Tour::factory()->create(['status' => 4]); // Completed

        $updateData = [
            'status' => 2, // Try to change to Active
            'responsible_users' => [$this->user->id],
        ];

        $response = $this->putJson("/api/tours/{$tour->id}", $updateData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false
                ]);
    }
}