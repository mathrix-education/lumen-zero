<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Events;

use Illuminate\Queue\SerializesModels;

abstract class BaseEvent
{
    use SerializesModels;
}
