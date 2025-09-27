<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Tour;
use App\User;
use App\Client;
use App\Status;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure we have statuses
        $this->ensureStatuses();

        // Get users and clients
        $users = User::limit(10)->get();
        $clients = Client::limit(5)->get();

        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        if ($clients->isEmpty()) {
            $clients = Client::factory()->count(3)->create();
        }

        // Create various types of tours
        $this->createUpcomingTours($users, $clients);
        $this->createActiveTours($users, $clients);
        $this->createCompletedTours($users, $clients);
        $this->createCancelledTours($users, $clients);
        $this->createDraftTours($users, $clients);

        $this->command->info('Tours seeded successfully!');
    }

    /**
     * Ensure tour statuses exist
     */
    protected function ensureStatuses()
    {
        $statuses = [
            ['id' => 1, 'name' => 'Draft', 'type' => 'tour', 'color' => '#6c757d'],
            ['id' => 2, 'name' => 'Active', 'type' => 'tour', 'color' => '#28a745'],
            ['id' => 3, 'name' => 'Cancelled', 'type' => 'tour', 'color' => '#dc3545'],
            ['id' => 4, 'name' => 'Completed', 'type' => 'tour', 'color' => '#007bff'],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['id' => $status['id']],
                $status
            );
        }
    }

    /**
     * Create upcoming tours
     */
    protected function createUpcomingTours($users, $clients)
    {
        $tours = Tour::factory()
            ->count(15)
            ->upcoming()
            ->active()
            ->create();

        foreach ($tours as $tour) {
            // Assign random client (50% chance)
            if (rand(0, 1) && $clients->isNotEmpty()) {
                $tour->update(['client_id' => $clients->random()->id]);
            }

            // Assign responsible users
            $responsibleUsers = $users->random(rand(1, 3));
            $tour->responsibleUsers()->attach($responsibleUsers);

            // Assign assigned users
            $assignedUsers = $users->random(rand(1, 2));
            $tour->assignedUsers()->attach($assignedUsers);
        }

        $this->command->info('Created ' . $tours->count() . ' upcoming tours');
    }

    /**
     * Create active tours (currently running)
     */
    protected function createActiveTours($users, $clients)
    {
        $tours = Tour::factory()
            ->count(8)
            ->started()
            ->active()
            ->create();

        foreach ($tours as $tour) {
            // Assign random client (70% chance for active tours)
            if (rand(0, 9) < 7 && $clients->isNotEmpty()) {
                $tour->update(['client_id' => $clients->random()->id]);
            }

            // Assign responsible users
            $responsibleUsers = $users->random(rand(1, 2));
            $tour->responsibleUsers()->attach($responsibleUsers);

            // Assign assigned users
            $assignedUsers = $users->random(rand(2, 4));
            $tour->assignedUsers()->attach($assignedUsers);
        }

        $this->command->info('Created ' . $tours->count() . ' active tours');
    }

    /**
     * Create completed tours
     */
    protected function createCompletedTours($users, $clients)
    {
        $tours = Tour::factory()
            ->count(20)
            ->completed()
            ->create();

        foreach ($tours as $tour) {
            // Most completed tours have clients
            if (rand(0, 9) < 8 && $clients->isNotEmpty()) {
                $tour->update(['client_id' => $clients->random()->id]);
            }

            // Assign responsible users
            $responsibleUsers = $users->random(rand(1, 2));
            $tour->responsibleUsers()->attach($responsibleUsers);

            // Assign assigned users
            $assignedUsers = $users->random(rand(1, 3));
            $tour->assignedUsers()->attach($assignedUsers);
        }

        $this->command->info('Created ' . $tours->count() . ' completed tours');
    }

    /**
     * Create cancelled tours
     */
    protected function createCancelledTours($users, $clients)
    {
        $tours = Tour::factory()
            ->count(5)
            ->cancelled()
            ->create();

        foreach ($tours as $tour) {
            // Some cancelled tours have clients
            if (rand(0, 1) && $clients->isNotEmpty()) {
                $tour->update(['client_id' => $clients->random()->id]);
            }

            // Assign responsible users
            $responsibleUsers = $users->random(rand(1, 2));
            $tour->responsibleUsers()->attach($responsibleUsers);
        }

        $this->command->info('Created ' . $tours->count() . ' cancelled tours');
    }

    /**
     * Create draft tours
     */
    protected function createDraftTours($users, $clients)
    {
        $tours = Tour::factory()
            ->count(10)
            ->draft()
            ->create();

        foreach ($tours as $tour) {
            // Draft tours rarely have clients assigned yet
            if (rand(0, 9) < 2 && $clients->isNotEmpty()) {
                $tour->update(['client_id' => $clients->random()->id]);
            }

            // Assign responsible users
            $responsibleUsers = $users->random(rand(1, 2));
            $tour->responsibleUsers()->attach($responsibleUsers);
        }

        $this->command->info('Created ' . $tours->count() . ' draft tours');
    }
}