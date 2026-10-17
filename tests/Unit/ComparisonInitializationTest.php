<?php

namespace Tests\Unit;

use App\Comparison;
use App\ComparisonRow;
use App\Quotation;
use App\Http\Controllers\ComparisonController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ComparisonInitializationTest extends TestCase
{
    private function fixture(): Quotation
    {
        foreach (['tours', 'quotations', 'tour_days', 'comparisons', 'comparison_rows', 'tour_room_type_hotels'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->increments('id');
                $table->timestamps();
                if (in_array($name, ['tours', 'quotations', 'tour_days'])) $table->softDeletes();
                if ($name === 'quotations') $table->integer('tour_id');
                if ($name === 'tour_days') { $table->integer('tour'); $table->string('date'); }
                if ($name === 'tour_room_type_hotels') $table->integer('tour_id');
                if ($name === 'comparisons') $table->string('comments')->nullable();
                if ($name === 'comparison_rows') {
                    $table->integer('comparison_id'); $table->string('date');
                    $table->boolean('rooming_list_reserved')->default(false);
                    $table->boolean('visa_confirmation')->default(false);
                    $table->decimal('city_tax')->default(0);
                }
            });
        }
        DB::table('tours')->insert(['id' => 1]);
        DB::table('quotations')->insert(['id' => 1, 'tour_id' => 1]);
        DB::table('tour_days')->insert([
            ['id' => 1, 'tour' => 1, 'date' => '2026-10-01'],
            ['id' => 2, 'tour' => 1, 'date' => '2026-10-02'],
        ]);
        return Quotation::findOrFail(1);
    }

    public function test_display_builds_placeholders_without_writing(): void
    {
        $quotation = $this->fixture();
        for ($i = 0; $i < 2; $i++) {
            $comparison = Comparison::forDisplay($quotation);
            $this->assertTrue($comparison->needsInitialization());
            $this->assertCount(2, $comparison->comparison_rows);
            $this->assertNull($comparison->comparisonRowByDate('2026-10-01')->id);
            $view = (new ComparisonController())->show(1);
            $this->assertSame('comparison.initialize', $view->name());
        }
        $this->assertSame(0, Comparison::count());
        $this->assertSame(0, ComparisonRow::count());
    }

    public function test_explicit_initialization_is_idempotent_and_preserves_saved_values(): void
    {
        $quotation = $this->fixture();
        Comparison::create(['id' => 1, 'comments' => 'Keep this']);
        $row = ComparisonRow::create(['comparison_id' => 1, 'date' => '2026-10-01', 'city_tax' => 12.5, 'rooming_list_reserved' => true]);
        $before = $row->fresh()->getAttributes();
        $controller = new ComparisonController();
        for ($i = 0; $i < 2; $i++) {
            $response = $controller->update(Request::create('/comparison/1', 'PUT', ['initialize_front_sheet' => '1']), 1);
            $this->assertSame(302, $response->getStatusCode());
        }
        $this->assertSame(1, Comparison::count());
        $this->assertSame(2, ComparisonRow::count());
        $this->assertSame($before, $row->fresh()->getAttributes());
        $this->assertSame('Keep this', Comparison::find(1)->comments);
        $this->assertFalse(Comparison::forDisplay($quotation)->needsInitialization());
        $this->assertSame('comparison.show', $controller->show(1)->name());
    }

    public function test_explicit_initialization_creates_a_missing_comparison(): void
    {
        $quotation = $this->fixture();
        (new ComparisonController())->update(Request::create('/comparison/1', 'PUT', ['initialize_front_sheet' => '1']), 1);
        $this->assertSame(1, Comparison::count());
        $this->assertSame(2, ComparisonRow::count());
        $this->assertFalse(Comparison::forDisplay($quotation)->needsInitialization());
    }

    public function test_initialization_rolls_back_if_a_row_cannot_be_created(): void
    {
        $this->fixture();
        $controller = new class extends ComparisonController {
            protected function syncComparisonRows($comparison)
            {
                parent::syncComparisonRows($comparison);
                throw new \RuntimeException('Simulated initialization failure');
            }
        };
        try {
            $controller->update(Request::create('/comparison/1', 'PUT', ['initialize_front_sheet' => '1']), 1);
            $this->fail('Expected failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated initialization failure', $exception->getMessage());
        }
        $this->assertSame(0, Comparison::count());
        $this->assertSame(0, ComparisonRow::count());
    }
}
