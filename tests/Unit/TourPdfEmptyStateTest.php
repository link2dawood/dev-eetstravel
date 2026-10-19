<?php

namespace Tests\Unit;

use Tests\TestCase;

class TourPdfEmptyStateTest extends TestCase
{
    private function data(): array
    {
        return [
            'mode' => 'pdf', 'office' => null, 'hotels' => [], 'vouchers' => [], 'days' => [],
            'summary' => array_fill_keys(['code', 'duration', 'route', 'group', 'rooms', 'tour_leader', 'tour_leader_phone', 'in_charge', 'in_charge_email'], '') + [
                'name' => 'Empty <tour>', 'start' => '19 Oct 2026', 'end' => '20 Oct 2026',
            ],
        ];
    }

    public function test_empty_vouchers_include_tour_context_and_generate_a_pdf_download(): void
    {
        $data = $this->data();
        $html = view('export.voucher', $data)->render();
        $this->assertStringContainsString('Service Vouchers', $html);
        $this->assertStringContainsString('Empty &lt;tour&gt;', $html);
        $this->assertStringContainsString('No services are marked for vouchers', $html);
        $response = app('dompdf.wrapper')->loadHTML($html)->download('voucher.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_itinerary_explains_missing_days_and_empty_scheduled_days(): void
    {
        $data = $this->data();
        $this->assertStringContainsString('No tour days are available', view('export.itinerary', $data)->render());
        $data['days'] = [['number' => 1, 'date' => '19 Oct 2026', 'items' => [], 'notes' => [], 'overnight' => null]];
        $html = view('export.itinerary', $data)->render();
        $this->assertStringContainsString('No services are scheduled for this day', $html);
        $this->assertStringContainsString('Empty &lt;tour&gt;', $html);
        $response = app('dompdf.wrapper')->loadHTML($html)->download('itinerary.pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }
}
