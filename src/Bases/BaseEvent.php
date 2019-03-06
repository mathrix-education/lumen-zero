<?php
namespace Mathrix\Lumen\Bases;

use Illuminate\Queue\SerializesModels;

/**
 * Class BaseEvent.
 * Base class for all events.
 *
 * @author    Mathieu Bour <mathieu.tin.bour@gmail.com>
 * @since     1.0.0
 * @copyright Mathrix Education SA
 * @package   App\Events
 */
abstract class BaseEvent
{
    use SerializesModels;
}
