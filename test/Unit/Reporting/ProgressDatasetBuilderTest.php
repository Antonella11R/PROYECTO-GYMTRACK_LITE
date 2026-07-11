<?php

declare(strict_types=1);

namespace Tests\Unit\Reporting;

use App\Reporting\Application\ProgressDatasetBuilder;
use PHPUnit\Framework\TestCase;

final class ProgressDatasetBuilderTest extends TestCase
{
    public function test_it_builds_labels_and_values_for_the_chart(): void
    {
        $builder = new ProgressDatasetBuilder();

        $dataset = $builder->build([
            ['performed_on' => '2026-05-01', 'weight' => 45.5],
            ['performed_on' => '2026-05-10', 'weight' => 50.0],
        ]);

        self::assertSame(['2026-05-01', '2026-05-10'], $dataset['labels']);
        self::assertSame([45.5, 50.0], $dataset['values']);
    }
}
