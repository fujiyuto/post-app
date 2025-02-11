<?php

namespace App\Enums;

enum Status: string
{
    case PENDING   = '保留中';
    case CONFIRMED = '予約確定';
    case CANCELLED = 'キャンセル済み';
    case COMPLETED = '来店済み';
}
