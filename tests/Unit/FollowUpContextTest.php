<?php

namespace Tests\Unit;

use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\SupplierSearchController;
use App\Http\Controllers\TourPackageController;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FollowUpContextTest extends TestCase
{
    public function test_direct_quotation_create_uses_the_existing_new_flow(): void
    {
        $response = (new QuotationController())->create();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('tour.create', ['is_quotation' => 1]), $response->getTargetUrl());
    }

    public function test_supplier_search_rejects_missing_or_invalid_context_before_queries(): void
    {
        $controller = new SupplierSearchController(\Mockery::mock(TourPackageController::class));
        foreach ([null, '', 'Unknown', ['Hotel'], 'User'] as $service) {
            try {
                $controller->show(Request::create('/supplier_show', 'GET', ['service' => $service]));
                $this->fail('Invalid supplier context was accepted');
            } catch (HttpException $exception) {
                $this->assertSame(422, $exception->getStatusCode());
            }
        }
    }

    public function test_reporting_still_rejects_missing_service_context(): void
    {
        try {
            (new ReportingController())->show(Request::create('/reporting_supplier_show'));
            $this->fail('Missing reporting service was accepted');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
    }
}
