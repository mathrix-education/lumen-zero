<?php

namespace Mathrix\Lumen\Exceptions\Models\Models;

use Mathrix\Lumen\Exceptions\Http\Http404NotFoundException;

/**
 * Class ModelNotFoundException.
 *
 * @author Mathieu Bour <mathieu@mathrix.fr>
 * @copyright Mathrix Education SA.
 * @since 1.0.0
 */
class ModelNotFoundException extends Http404NotFoundException
{
    protected $message = "Unable to find a model matching those criteria.";
}
