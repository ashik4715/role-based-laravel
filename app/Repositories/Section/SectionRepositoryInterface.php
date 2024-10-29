<?php

namespace App\Repositories\Section;

use App\Models\Section;
use App\Repositories\EloquentRepositoryInterface;

interface SectionRepositoryInterface extends EloquentRepositoryInterface
{
    public function getSectionBySlug(string $slug): ?Section;

    public function getSectionByOrder(int $order): Section;

    public function getSectionCount(): int;
}
