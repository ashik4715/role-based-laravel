<?php

namespace App\Repositories\Section;

use App\Models\Section;
use App\Repositories\BaseRepository;

class SectionRepository extends BaseRepository implements SectionRepositoryInterface
{
    public function __construct(Section $model)
    {
        parent::__construct($model);
    }

    public function getSectionBySlug(string $slug): ?Section
    {
        return $this->model->whereSlug($slug)->first();
    }

    public function getSectionByOrder(int $order): Section
    {
        return $this->model->whereOrder($order)->first();
    }

    public function getSectionCount(): int
    {
        return $this->model->count();
    }
}
