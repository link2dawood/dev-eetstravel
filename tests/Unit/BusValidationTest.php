<?php

namespace Tests\Unit;

use App\Helper\BusDayHelper;
use App\Http\Controllers\BusController;
use App\Http\Requests\UpdateBusRequest;
use App\Repository\Contracts\BusRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BusValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Schema::create('buses', function ($table) {
            $table->increments('id');
            $table->string('bus_number')->nullable();
            $table->softDeletes();
        });
        \Illuminate\Support\Facades\Schema::create('transfers', function ($table) {
            $table->increments('id');
            $table->softDeletes();
        });
        \Illuminate\Support\Facades\Schema::create('status', function ($table) {
            $table->increments('id');
            $table->string('type');
        });
        \Illuminate\Support\Facades\DB::table('buses')->insert(['id' => 1]);
        \Illuminate\Support\Facades\DB::table('status')->insert(['id' => 1, 'type' => 'bus']);
    }
    /** @dataProvider dateRanges */
    public function test_trip_dates_are_validated_before_scheduling($start, $end, $valid)
    {
        $controller = new BusController($this->createMock(BusRepository::class), new BusDayHelper());
        $request = Request::create('/', 'POST', [
            'name_trip' => 'Test trip', 'country_begin' => '1', 'city_begin' => '1',
            'start_date' => $start, 'end_date' => $end,
            'bus_trip' => 1, 'status' => 1,
        ]);

        try {
            $controller->validateDayTrip($request);
            $this->assertTrue($valid, 'Invalid dates were accepted.');
        } catch (ValidationException $exception) {
            $this->assertFalse($valid, 'Valid dates were rejected.');
        }
    }

    public static function dateRanges(): array
    {
        return [
            ['2026-10-12', '2026-10-12', true],
            ['2026-10-12', '2026-10-14', true],
            ['2026-10-14', '2026-10-12', false],
            ['2026-02-30', '2026-03-01', false],
            ['', '', false],
            ['2026-10-01', '2026-11-29', true],
            ['2026-10-01', '2026-11-30', false],
        ];
    }

    /** @dataProvider busParameters */
    public function test_update_uniqueness_excludes_current_bus_for_each_route($parameter)
    {
        $request = new UpdateBusRequest();
        $route = new Route('POST', 'bus/{'.$parameter.'}', function () {});
        $route->bind(Request::create('/bus/42', 'POST'));
        $request->setRouteResolver(function () use ($route) { return $route; });

        $this->assertStringContainsString('unique:buses,bus_number,"42"', (string) $request->rules()['bus_number'][4]);
    }

    public static function busParameters(): array
    {
        return [['bu'], ['bus'], ['id']];
    }

    public function test_deleted_company_is_rejected_and_deleted_bus_number_can_be_reused()
    {
        $db = \Illuminate\Support\Facades\DB::class;
        $db::table('transfers')->insert(['id' => 7, 'deleted_at' => '2026-10-01']);
        $db::table('buses')->insert(['id' => 2, 'bus_number' => 'W-123', 'deleted_at' => '2026-10-01']);
        $rules = (new \App\Http\Requests\StoreBusRequest())->rules();
        $data = ['name' => "Müller's Bus", 'bus_number' => 'W-123'];
        $this->assertTrue(\Illuminate\Support\Facades\Validator::make($data, $rules)->passes());
        $this->assertTrue(\Illuminate\Support\Facades\Validator::make($data + ['transfer_id' => 7], $rules)->fails());
        $db::table('buses')->where('id', 2)->update(['deleted_at' => null]);
        $this->assertTrue(\Illuminate\Support\Facades\Validator::make($data, $rules)->fails());
        $data['bus_number'] = '<img src=x onerror=alert(1)>';
        $this->assertTrue(\Illuminate\Support\Facades\Validator::make($data, $rules)->fails());
    }

    public function test_bus_capacity_and_comments_are_persisted_by_create_and_update()
    {
        \Illuminate\Support\Facades\Schema::table('buses', function ($table) {
            $table->string('name')->nullable();
            $table->timestamps();
        });
        require_once database_path('migrations/2026_10_30_120000_add_capacity_and_comments_to_buses.php');
        (new \AddCapacityAndCommentsToBuses())->up();
        \App\Bus::withoutEvents(function () {
            $repository = new \App\Repository\BusRepository\EloquentBusRepository();
            $bus = $repository->create(['name' => 'Coach', 'bus_number' => 'W-456', 'capacity' => 50, 'comments' => 'Accessible']);
            $this->assertSame(50, $bus->fresh()->capacity);
            $repository->updateById($bus->id, ['capacity' => 40, 'comments' => 'Updated']);
            $this->assertSame(40, $bus->fresh()->capacity);
            $this->assertSame('Updated', $bus->fresh()->comments);
        });
    }

    public function test_legacy_bus_update_requires_edit_permission()
    {
        $route = app('router')->getRoutes()->match(Request::create('/bus/42/update', 'POST'));
        $this->assertSame('bus.edit', $route->getAction('permissions'));
    }

    public function test_calendar_write_routes_require_explicit_permissions()
    {
        foreach (['/api/add_day' => 'bus.create', '/bus_day/update' => 'bus.edit', '/bus_day/update/tour' => 'bus.edit', '/bus_day/delete' => 'bus.destroy'] as $url => $permission) {
            $route = app('router')->getRoutes()->match(Request::create($url, 'POST'));
            $this->assertSame($permission, $route->getAction('permissions'));
        }
    }

    public function test_calendar_options_are_paginated_and_exclude_deleted_buses()
    {
        \Illuminate\Support\Facades\Schema::table('buses', function ($table) { $table->string('name')->nullable(); });
        for ($id = 2; $id <= 30; $id++) {
            \Illuminate\Support\Facades\DB::table('buses')->insert(['id' => $id, 'name' => 'Coach ' . $id, 'bus_number' => 'W-' . $id]);
        }
        \Illuminate\Support\Facades\DB::table('buses')->where('id', 30)->update(['deleted_at' => '2026-10-01']);
        $controller = new BusController($this->createMock(BusRepository::class), new BusDayHelper());
        $data = $controller->calendarOptions(Request::create('/', 'GET', ['kind' => 'bus', 'q' => 'Coach']))->getData(true);
        $this->assertCount(25, $data['results']);
        $this->assertTrue($data['pagination']['more']);
        $filtered = $controller->calendarOptions(Request::create('/', 'GET', ['kind' => 'bus', 'q' => 'W-30']))->getData(true);
        $this->assertSame([], $filtered['results']);
    }

    public function test_bus_saves_preserve_fields_and_return_the_expected_response()
    {
        $data = ['name' => 'Coach', 'bus_number' => 'W-123', 'capacity' => 50, 'comments' => 'Accessible'];
        foreach ([false, true] as $json) {
            foreach (['store', 'update'] as $action) {
                $repository = $this->createMock(BusRepository::class);
                $bus = new \App\Bus($data);
                $bus->id = 42;
                if ($action === 'store') {
                    $repository->expects($this->once())->method('create')->with($data)->willReturn($bus);
                    $request = \App\Http\Requests\StoreBusRequest::create('/bus', 'POST', $data + ['id' => 999, 'created_at' => '2000-01-01']);
                } else {
                    $repository->expects($this->once())->method('updateById')->with(42, $data);
                    $repository->method('byId')->with(42)->willReturn($bus);
                    $request = UpdateBusRequest::create('/bus/42/update', 'POST', $data + ['id' => 999]);
                }
                if ($json) $request->headers->set('Accept', 'application/json');
                $controller = new BusController($repository, new BusDayHelper());
                $response = $action === 'store' ? $controller->store($request) : $controller->update($request, 42);
                $this->assertSame($json ? 200 : 302, $response->getStatusCode());
                $this->assertSame(route('bus.index'), $json ? $response->getData(true)['route'] : $response->getTargetUrl());
            }
        }
    }

    public function test_bus_upload_validation_cannot_be_bypassed_by_alternate_field()
    {
        foreach ([new \App\Http\Requests\StoreBusRequest(), new UpdateBusRequest()] as $request) {
            $rules = $request->rules();
            $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
            $this->assertTrue(\Illuminate\Support\Facades\Validator::make(['attach' => [$file]], array_intersect_key($rules, array_flip(['attach', 'attach.*'])))->fails());
            $this->assertTrue(\Illuminate\Support\Facades\Validator::make(['attach_bus' => [$file]], ['attach_bus' => $rules['attach_bus']])->fails());
        }
    }
}
