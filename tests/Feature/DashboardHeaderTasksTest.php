<?php

namespace Tests\Feature;

use App\Helper\DashboardHelper;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardHeaderTasksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('assign')->nullable();
            $table->string('content')->nullable();
            $table->integer('tour')->nullable();
            $table->dateTime('dead_line')->nullable();
            $table->integer('status')->nullable();
            $table->integer('priority')->nullable();
        });

        Schema::create('task_user', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('task_id');
        });

        $user = new User();
        $user->setAttribute('id', 42);
        Auth::login($user);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('tasks');

        parent::tearDown();
    }

    public function test_header_fetches_only_active_assigned_and_related_tasks_in_two_queries(): void
    {
        $future = now()->addDay()->toDateTimeString();
        $past = now()->subDay()->toDateTimeString();
        $expiredTasks = [];

        for ($id = 1; $id <= 50; $id++) {
            $expiredTasks[] = [
                'id' => $id,
                'assign' => 42,
                'dead_line' => $past,
                'status' => 1,
            ];
        }

        DB::table('tasks')->insert($expiredTasks);
        DB::table('tasks')->insert([
            ['id' => 51, 'assign' => 42, 'dead_line' => $future, 'status' => null],
            ['id' => 52, 'assign' => 42, 'dead_line' => $future, 'status' => 7],
            ['id' => 53, 'assign' => 42, 'dead_line' => $future, 'status' => 8],
            ['id' => 54, 'assign' => 7, 'dead_line' => $future, 'status' => 1],
        ]);
        DB::table('task_user')->insert(['user_id' => 42, 'task_id' => 54]);

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = strtolower($query->sql);
        });

        $tasks = DashboardHelper::getTasks();

        $this->assertSame([54, 51], array_column($tasks, 'id'));
        $this->assertCount(2, $queries);
        foreach ($queries as $query) {
            $this->assertStringContainsString('dead_line', $query);
            $this->assertStringContainsString('not in', $query);
        }
    }
}
