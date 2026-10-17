<?php

namespace Tests\Unit;

use App\Helper\BusDayHelper;
use App\Http\Controllers\TourPackageController;
use App\Repository\Contracts\TourPackageRepository;
use App\Repository\Contracts\TourRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class OrphanPackageEditTest extends TestCase
{
    public function test_update_of_orphan_is_rejected_before_any_write(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->increments('id'); $table->string('name'); $table->integer('tour_id')->nullable(); $table->softDeletes();
        });
        Schema::create('tour_days', function (Blueprint $table) {
            $table->increments('id'); $table->integer('tour'); $table->softDeletes();
        });
        Schema::create('packages_tour_days', function (Blueprint $table) {
            $table->integer('tour_package_id'); $table->integer('tour_day_id');
        });
        Schema::create('tours', function (Blueprint $table) {
            $table->increments('id'); $table->softDeletes();
        });
        DB::table('tour_packages')->insert(['id' => 1, 'name' => 'Original package']);
        $controller = new TourPackageController(
            \Mockery::mock(TourRepository::class),
            \Mockery::mock(BusDayHelper::class),
            \Mockery::mock(TourPackageRepository::class)
        );
        try {
            $controller->update(1, Request::create('/tour_package/1/update', 'POST', ['name' => 'Changed']));
            $this->fail('An orphan package must not be updated');
        } catch (HttpException $exception) {
            $this->assertSame(409, $exception->getStatusCode());
        }
        $this->assertSame('Original package', DB::table('tour_packages')->value('name'));
        $this->assertSame(0, DB::table('packages_tour_days')->count());
    }
}
