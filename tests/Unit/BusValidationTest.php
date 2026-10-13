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
    /** @dataProvider dateRanges */
    public function test_trip_dates_are_validated_before_scheduling($start, $end, $valid)
    {
        $controller = new BusController($this->createMock(BusRepository::class), new BusDayHelper());
        $request = Request::create('/', 'POST', [
            'name_trip' => 'Test trip', 'country_begin' => '1', 'city_begin' => '1',
            'start_date' => $start, 'end_date' => $end,
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
        ];
    }

    /** @dataProvider busParameters */
    public function test_update_uniqueness_excludes_current_bus_for_each_route($parameter)
    {
        $request = new UpdateBusRequest();
        $route = new Route('POST', 'bus/{'.$parameter.'}', function () {});
        $route->bind(Request::create('/bus/42', 'POST'));
        $request->setRouteResolver(function () use ($route) { return $route; });

        $this->assertStringContainsString('unique:buses,bus_number,42', $request->rules()['bus_number']);
    }

    public static function busParameters(): array
    {
        return [['bu'], ['bus'], ['id']];
    }
}
