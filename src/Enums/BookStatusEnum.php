<?php

namespace App\Enums;

enum BookStatusEnum: string
{
    case AVAILABLE = 'available';
    case LOANED = 'loaned';
    case REPAIRED = 'repaired';
    case LOST = 'lost';
}
