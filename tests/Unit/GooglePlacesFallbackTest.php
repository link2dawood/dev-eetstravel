<?php

namespace Tests\Unit;

use App\GooglePlaces;
use App\Helper\GooglePlacesHelper;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SKAgarwal\GoogleApi\Exceptions\RequestDeniedException;
use SKAgarwal\GoogleApi\PlacesApi;
use Tests\TestCase;

class GooglePlacesFallbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('google_places', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->integer('service_id');
            $table->string('place_id');
        });
    }

    public function test_missing_location_does_not_call_google(): void
    {
        $this->app->bind(PlacesApi::class, function () { $this->fail('Unexpected Google request'); });
        $this->assertNull(GooglePlacesHelper::getPlace(1, GooglePlaces::$services['flight']));
    }

    public function test_successful_lookup_is_preserved(): void
    {
        $this->location();
        $result = collect(['status' => 'OK', 'result' => ['place_id' => 'saved-place']]);
        $client = \Mockery::mock(PlacesApi::class);
        $client->shouldReceive('placeDetails')->once()->with('saved-place')->andReturn($result);
        $this->app->bind(PlacesApi::class, fn () => $client);
        $this->assertSame($result, GooglePlacesHelper::getPlace(1, GooglePlaces::$services['flight']));
    }

    public function test_denied_and_network_failures_do_not_remove_saved_location(): void
    {
        $this->location();
        foreach ([new RequestDeniedException('REQUEST_DENIED'), new ConnectException('Offline', new Request('GET', 'https://example.test'))] as $exception) {
            $client = \Mockery::mock(PlacesApi::class);
            $client->shouldReceive('placeDetails')->once()->with('saved-place')->andThrow($exception);
            $this->app->bind(PlacesApi::class, fn () => $client);
            $this->assertNull(GooglePlacesHelper::getPlace(1, GooglePlaces::$services['flight']));
            $this->assertSame('saved-place', DB::table('google_places')->value('place_id'));
        }
    }

    private function location(): void
    {
        DB::table('google_places')->insert(['type' => GooglePlaces::$services['flight'], 'service_id' => 1, 'place_id' => 'saved-place']);
    }
}
