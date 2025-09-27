<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Tour;
use App\User;
use App\Status;
use App\Client;
use App\Services\TourService;
use App\Exceptions\BusinessLogicException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TourServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $tourService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tourService = new TourService();

        // Create a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create test statuses
        Status::factory()->create(['id' => 1, 'name' => 'Draft', 'type' => 'tour']);
        Status::factory()->create(['id' => 2, 'name' => 'Active', 'type' => 'tour']);
        Status::factory()->create(['id' => 3, 'name' => 'Cancelled', 'type' => 'tour']);
        Status::factory()->create(['id' => 4, 'name' => 'Completed', 'type' => 'tour']);
    }

    /** @test */
    public function it_can_create_a_tour()
    {
        $client = Client::factory()->create();
        $responsibleUser = User::factory()->create();

        $tourData = [
            'name' => 'Amazing Egypt Tour',
            'overview' => 'A wonderful tour of Egypt',
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
            'responsible_users' => [$responsibleUser->id],
        ];

        $tour = $this->tourService->createTour($tourData);

        $this->assertInstanceOf(Tour::class, $tour);
        $this->assertEquals('Amazing Egypt Tour', $tour->name);
        $this->assertEquals(20, $tour->pax);
        $this->assertEquals($client->id, $tour->client_id);
        $this->assertTrue($tour->responsibleUsers->contains($responsibleUser));

        // Check tour days were generated
        $this->assertGreaterThan(0, $tour->tourDays()->count());
    }

    /** @test */
    public function it_throws_exception_when_creating_tour_with_invalid_data()
    {
        $this->expectException(BusinessLogicException::class);

        $tourData = [
            'name' => '', // Invalid empty name
            'departure_date' => now()->addDays(30)->toDateString(),
            'pax' => 20,
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'status' => 1,
        ];

        $this->tourService->createTour($tourData);
    }

    /** @test */
    public function it_can_update_a_tour()
    {
        $tour = Tour::factory()->create([
            'name' => 'Original Tour Name',
            'pax' => 15,
            'status' => 1,
        ]);

        $updateData = [
            'name' => 'Updated Tour Name',
            'pax' => 25,
            'status' => 2,
            'responsible_users' => [$this->user->id],
        ];

        $updatedTour = $this->tourService->updateTour($tour, $updateData);

        $this->assertEquals('Updated Tour Name', $updatedTour->name);
        $this->assertEquals(25, $updatedTour->pax);
        $this->assertEquals(2, $updatedTour->status);
    }

    /** @test */
    public function it_prevents_updating_started_tours_departure_date()
    {
        $tour = Tour::factory()->create([
            'departure_date' => now()->subDays(5)->toDateString(), // Already started
            'status' => 2,
        ]);

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Cannot modify departure_date for tours that have already started');

        $updateData = [
            'departure_date' => now()->addDays(10)->toDateString(),
            'responsible_users' => [$this->user->id],
        ];

        $this->tourService->updateTour($tour, $updateData);
    }

    /** @test */
    public function it_can_delete_a_tour()
    {
        $tour = Tour::factory()->create([
            'departure_date' => now()->addDays(30)->toDateString(),
        ]);

        $result = $this->tourService->deleteTour($tour);

        $this->assertTrue($result);
        $this->assertTrue($tour->fresh()->trashed());
    }

    /** @test */
    public function it_prevents_deleting_started_tours()
    {
        $tour = Tour::factory()->create([
            'departure_date' => now()->subDays(5)->toDateString(),
        ]);

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Cannot delete tours that have already started');

        $this->tourService->deleteTour($tour);
    }

    /** @test */
    public function it_can_clone_a_tour()
    {
        $originalTour = Tour::factory()->create([
            'name' => 'Original Tour',
            'departure_date' => now()->subDays(30)->toDateString(),
        ]);

        $responsibleUser = User::factory()->create();
        $originalTour->responsibleUsers()->attach($responsibleUser);

        $newData = [
            'departure_date' => now()->addDays(30)->toDateString(),
        ];

        $clonedTour = $this->tourService->cloneTour($originalTour, $newData);

        $this->assertNotEquals($originalTour->id, $clonedTour->id);
        $this->assertStringContains('(Copy)', $clonedTour->name);
        $this->assertEquals($newData['departure_date'], $clonedTour->departure_date);
        $this->assertTrue($clonedTour->responsibleUsers->contains($responsibleUser));
    }

    /** @test */
    public function it_generates_unique_clone_names()
    {
        $originalTour = Tour::factory()->create(['name' => 'Test Tour']);

        // Create first clone
        $clone1 = $this->tourService->cloneTour($originalTour, [
            'departure_date' => now()->addDays(30)->toDateString(),
        ]);

        // Create second clone
        $clone2 = $this->tourService->cloneTour($originalTour, [
            'departure_date' => now()->addDays(60)->toDateString(),
        ]);

        $this->assertEquals('Test Tour (Copy)', $clone1->name);
        $this->assertEquals('Test Tour (Copy) 1', $clone2->name);
    }

    /** @test */
    public function it_can_get_tours_with_filters()
    {
        // Create tours with different statuses
        $activeTour = Tour::factory()->create(['status' => 2, 'name' => 'Active Tour']);
        $draftTour = Tour::factory()->create(['status' => 1, 'name' => 'Draft Tour']);

        $filters = ['status' => 2];
        $result = $this->tourService->getTours($filters, 10);

        $this->assertEquals(1, $result->count());
        $this->assertEquals($activeTour->id, $result->first()->id);
    }

    /** @test */
    public function it_can_get_tour_statistics()
    {
        // Create test tours
        Tour::factory()->create(['status' => 2, 'pax' => 10]); // Active
        Tour::factory()->create(['status' => 4, 'pax' => 15]); // Completed
        Tour::factory()->create(['status' => 3, 'pax' => 8]);  // Cancelled

        $statistics = $this->tourService->getTourStatistics();

        $this->assertEquals(3, $statistics['total_tours']);
        $this->assertEquals(1, $statistics['active_tours']);
        $this->assertEquals(1, $statistics['completed_tours']);
        $this->assertEquals(1, $statistics['cancelled_tours']);
        $this->assertEquals(33, $statistics['total_passengers']);
    }

    /** @test */
    public function it_validates_status_transitions()
    {
        $tour = Tour::factory()->create(['status' => 4]); // Completed

        $this->expectException(BusinessLogicException::class);
        $this->expectExceptionMessage('Invalid status transition');

        $updateData = [
            'status' => 2, // Try to change from Completed to Active
            'responsible_users' => [$this->user->id],
        ];

        $this->tourService->updateTour($tour, $updateData);
    }

    /** @test */
    public function it_validates_business_logic_for_passengers_per_room()
    {
        $tourData = [
            'name' => 'Test Tour',
            'departure_date' => now()->addDays(30)->toDateString(),
            'pax' => 50,
            'rooms' => 10, // 5 passengers per room - should be fine
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'status' => 1,
            'responsible_users' => [$this->user->id],
        ];

        $tour = $this->tourService->createTour($tourData);
        $this->assertInstanceOf(Tour::class, $tour);

        // Now try with too many passengers per room
        $this->expectException(BusinessLogicException::class);

        $badTourData = [
            'name' => 'Bad Tour',
            'departure_date' => now()->addDays(30)->toDateString(),
            'pax' => 50,
            'rooms' => 5, // 10 passengers per room - too many
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'status' => 1,
            'responsible_users' => [$this->user->id],
        ];

        $this->tourService->createTour($badTourData);
    }

    /** @test */
    public function it_logs_tour_operations()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Tour created successfully', \Mockery::type('array'));

        $tourData = [
            'name' => 'Logged Tour',
            'departure_date' => now()->addDays(30)->toDateString(),
            'pax' => 20,
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'status' => 1,
            'responsible_users' => [$this->user->id],
        ];

        $this->tourService->createTour($tourData);
    }

    /** @test */
    public function it_handles_concurrent_tour_creation()
    {
        $tourData = [
            'name' => 'Concurrent Tour',
            'departure_date' => now()->addDays(30)->toDateString(),
            'pax' => 20,
            'country_begin' => 'Egypt',
            'city_begin' => 'Cairo',
            'status' => 1,
            'responsible_users' => [$this->user->id],
        ];

        // First tour should succeed
        $tour1 = $this->tourService->createTour($tourData);
        $this->assertInstanceOf(Tour::class, $tour1);

        // Second tour with same name should fail due to unique constraint
        $this->expectException(BusinessLogicException::class);
        $this->tourService->createTour($tourData);
    }
}