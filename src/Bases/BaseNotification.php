<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Notifications\Notification;

/**
 * Class BaseNotification.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseNotification extends Notification
{
    abstract public function via(): array;
}
