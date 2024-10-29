<?php

namespace App\Services\Application;

interface DraftChecker
{
    public function shouldStartDrafting(string $section_slug): bool;
}
