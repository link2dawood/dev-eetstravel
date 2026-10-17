<?php
namespace Tests\Unit;

require_once __DIR__.'/../../scripts/lib/LegacyEncodingRepair.php';

class LegacyEncodingRepairTest extends \PHPUnit\Framework\TestCase
{
    public function test_repairs_single_double_and_mixed_encoding_without_changing_good_text(): void
    {
        foreach (['€', '¥', 'Kč', 'лв', '分', 'Dürnstein', 'Müller', 'São Paulo', '東京', 'S£', '<b>café</b>'] as $value) {
            $this->assertSame($value, \LegacyEncodingRepair::repair($value));
            $encoded = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
            $this->assertSame($value, \LegacyEncodingRepair::repair($encoded));
            $double = mb_convert_encoding($encoded, 'UTF-8', 'Windows-1252');
            $this->assertSame($value, \LegacyEncodingRepair::repair($double));
        }
        $this->assertSame('Good € / Dürnstein', \LegacyEncodingRepair::repair('Good € / DÃ¼rnstein'));
    }
}
