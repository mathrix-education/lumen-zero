<?php

namespace Mathrix\Lumen\Bases;

use Illuminate\Queue\SerializesModels;

/**
 * Class BaseEvent.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
abstract class BaseEvent
{
    use SerializesModels;
}
