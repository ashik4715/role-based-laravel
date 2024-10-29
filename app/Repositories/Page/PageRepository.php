<?php

namespace App\Repositories\Page;

use App\Models\Page;

class PageRepository implements PageRepositoryInterface
{
    public function __construct(private readonly Page $model)
    {
    }

    public function getPageBySlug(string $slug): Page
    {
        return $this->model->slug($slug)->first();
    }
}
