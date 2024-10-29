<?php

namespace App\Services\Application\Form\Button;

enum ButtonAction: string
{
    case REDIRECT = 'redirect';
    case BACK = 'back';
}
