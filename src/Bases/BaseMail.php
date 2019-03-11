<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BaseMail.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseMail extends Mailable
{
    use Queueable, SerializesModels;


    abstract public static function mock();
}
