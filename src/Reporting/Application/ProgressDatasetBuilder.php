<?php

declare(strict_types=1);

namespace App\Reporting\Application;

final class ProgressDatasetBuilder
{
    /**
     * @param list<array{performed_on:string, weight:float}> $points
     * @return array{labels:list<string>, values:list<float>}
     */
    public function build(array $points): array
    {
        $labels = [];
        $values = [];

        foreach ($points as $point) {
            $labels[] = $point['performed_on'];
            $values[] = (float) $point['weight'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
