<?php

declare(strict_types=1);

namespace Givebutter\Laravel\Enums;

enum BadgeColor: string
{
    case INFO = 'INFO';

    case ERROR = 'ERROR';

    case WARN = 'WARN';
}
