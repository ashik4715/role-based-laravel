<?php

namespace App\Services\Application\Form\Button;

class Button
{
    public function __construct(public readonly string $title, public readonly ButtonAction $action, public readonly ?string $link = null)
    {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'action' => $this->action,
            'link' => $this->link,
        ];
    }
}
