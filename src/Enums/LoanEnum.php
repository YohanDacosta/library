<?php

namespace App\Enums;

enum LoanEnum: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case LOST = 'lost';
}
