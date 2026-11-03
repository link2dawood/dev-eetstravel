<?php

namespace Tests\Feature;

use App\Task;
use App\Tour;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TaskTourNameTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('tours', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->softDeletes();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tour')->nullable();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('tours');

        parent::tearDown();
    }

    public function test_tour_name_uses_the_eager_loaded_tour_without_an_extra_query(): void
    {
        DB::table('tours')->insert([
            ['id' => 7, 'name' => 'Regression tour'],
            ['id' => 8, 'name' => 'Second regression tour'],
        ]);
        DB::table('tasks')->insert([
            ['id' => 1, 'tour' => 7],
            ['id' => 2, 'tour' => 8],
        ]);

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        $tasks = Task::with('tour')->orderBy('id')->get();

        $this->assertSame(
            ['Regression tour', 'Second regression tour'],
            $tasks->map(function (Task $task) {
                return $task->tourName();
            })->all()
        );
        $this->assertCount(2, $queries);
    }
}
