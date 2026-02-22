<?php

namespace App\Enums;

enum LoanStatusEnum: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
}
