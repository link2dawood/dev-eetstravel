<?php
namespace Tests\Unit;

use App\Helper\ActivityDescription;
use PHPUnit\Framework\TestCase;

class ActivityDescriptionTest extends TestCase
{
    public function test_rich_text_becomes_readable_plain_text(): void
    {
        $cases = [
            '<p>Comment <strong>created</strong></p><p>Second line<br>Third line</p>' => 'Comment created Second line Third line',
            'Alice &amp; Bob&nbsp; – café' => 'Alice & Bob – café',
            '<script>alert(1)</script><p>Safe</p><style>body{display:none}</style>' => 'Safe',
            '<p><img src=x onerror=alert(1)>Message</p>' => 'Message',
            'Plain description' => 'Plain description',
        ];
        foreach ($cases as $input => $expected) $this->assertSame($expected, ActivityDescription::plainText($input));
        $this->assertSame('', ActivityDescription::plainText(null));
    }
}
