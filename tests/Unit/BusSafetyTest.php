<?php

namespace Tests\Unit;

use App\Bus;
use App\BusDay;
use App\Helper\BusDayHelper;
use App\Http\Controllers\BusController;
use App\File;
use App\Http\Controllers\FileController;
use App\Library\Services\DeleteModel;
use App\Repository\Contracts\BusRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('bus_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bus_id');
            $table->integer('trip_id')->nullable();
            $table->integer('tour_package_id')->nullable();
            $table->date('date')->nullable();
            $table->integer('tour_id')->nullable();
            $table->integer('transfer_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->timestamps();
        });
        Schema::create('trip_to_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bus_day_id');
            $table->integer('driver_id');
        });
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attach_file_name');
            $table->string('attach_content_type');
            $table->timestamps();
        });
    }

    public function test_checking_a_scheduled_bus_never_deletes_it()
    {
        DB::table('bus_days')->insert(['bus_id' => 42]);
        $bus = $this->getMockBuilder(Bus::class)->onlyMethods(['delete'])->getMock();
        $bus->id = 42;
        $bus->name = 'Scheduled coach';
        $bus->expects($this->never())->method('delete');

        $this->assertStringContainsString('cannot be deleted', (new DeleteModel())->check($bus, 'Bus'));
    }

    public function test_unused_bus_can_be_deleted()
    {
        $bus = new Bus(['id' => 42, 'name' => 'Unused coach']);
        $this->assertNull((new DeleteModel())->check($bus, 'Bus'));
    }

    public function test_full_trip_conflicts_include_the_last_day_and_exclude_own_trip()
    {
        DB::table('bus_days')->insert([
            ['id' => 1, 'bus_id' => 42, 'trip_id' => 10, 'date' => '2026-10-20'],
            ['id' => 2, 'bus_id' => 42, 'trip_id' => 10, 'date' => '2026-10-21'],
        ]);
        $helper = new BusDayHelper();
        $this->assertTrue($helper->validateBuses(42, '2026-10-20', '2026-10-21', null, 1, 10)['check']);

        DB::table('bus_days')->insert(['id' => 3, 'bus_id' => 43, 'trip_id' => 11, 'date' => '2026-10-21']);
        $this->assertFalse($helper->validateBuses(43, '2026-10-20', '2026-10-21', null, 1, 10)['check']);

        DB::table('trip_to_drivers')->insert(['bus_day_id' => 3, 'driver_id' => 7]);
        $this->assertFalse($helper->validateDrivers([7], '2026-10-20', '2026-10-21', null, 1, 10));
    }

    public function test_busy_bus_without_a_parent_trip_returns_a_conflict()
    {
        DB::table('bus_days')->insert(['bus_id' => 42, 'date' => '2026-10-20']);
        $result = (new BusDayHelper())->validateBuses(42, '2026-10-20', '2026-10-20');
        $this->assertFalse($result['check']);
        $this->assertSame('2026-10-20', $result['start']);
    }

    public function test_editing_one_tour_day_does_not_change_other_days()
    {
        DB::table('bus_days')->insert([
            ['id' => 1, 'bus_id' => 42, 'tour_package_id' => 7, 'status_id' => 1, 'date' => '2026-10-20'],
            ['id' => 2, 'bus_id' => 42, 'tour_package_id' => 7, 'status_id' => 1, 'date' => '2026-10-21'],
        ]);
        $helper = $this->createMock(BusDayHelper::class);
        $helper->expects($this->once())->method('validateBuses')
            ->with(43, '2026-10-20', '2026-10-20', null, 1)->willReturn(['check' => true]);
        $helper->method('validateDrivers')->willReturn(true);
        $helper->expects($this->never())->method('changeStatusTransferPackage');
        $controller = new BusController($this->createMock(BusRepository::class), $helper);

        BusDay::withoutEvents(function () use ($controller) {
            $controller->updateBusDayTour(Request::create('/', 'POST', [
                'bus_id' => 43, 'status' => 2, 'id_bus_day' => 1, 'tour_mode' => 1, 'tour_package_id' => 7,
            ]));
        });

        $this->assertSame(43, DB::table('bus_days')->where('id', 1)->value('bus_id'));
        $this->assertSame(42, DB::table('bus_days')->where('id', 2)->value('bus_id'));
        $this->assertSame(1, DB::table('bus_days')->where('id', 2)->value('status_id'));
    }

    public function test_local_upload_link_and_download_do_not_need_a_storage_symlink()
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/test.txt', 'Bus attachment');
        $file = File::create(['attach_file_name' => 'uploads/test.txt', 'attach_content_type' => 'text/plain']);

        $this->assertSame(route('file.download', ['id' => $file->id]), $file->url);
        $response = (new FileController())->download($file->id);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        ob_start();
        $response->sendContent();
        $this->assertSame('Bus attachment', ob_get_clean());
    }
}
