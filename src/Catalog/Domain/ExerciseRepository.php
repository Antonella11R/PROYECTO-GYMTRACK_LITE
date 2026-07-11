<?php

declare(strict_types=1);

namespace App\Catalog\Domain;

interface ExerciseRepository
{
    public function findById(int $id): ?Exercise;

    /** @return list<Exercise> */
    public function all(): array;

    /** @return list<Exercise> */
    public function active(): array;

    public function save(Exercise $exercise): Exercise;

    public function update(Exercise $exercise): void;
}
