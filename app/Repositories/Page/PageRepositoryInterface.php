<?php

namespace App\Repositories\Page;

use App\Models\Page;

interface PageRepositoryInterface
{
    public function getPageBySlug(string $slug): Page;
}
