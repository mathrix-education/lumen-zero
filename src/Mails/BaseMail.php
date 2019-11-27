<?php

declare(strict_types=1);

namespace Mathrix\Lumen\Zero\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class BaseMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    abstract public static function mock();
}
