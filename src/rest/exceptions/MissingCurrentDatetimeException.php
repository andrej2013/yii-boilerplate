<?php

namespace andrej2013\yiiboilerplate\rest\exceptions;

use Exception;

/**
 * Class MissingCurrentDatetimeException
 * @package andrej2013\yiiboilerplate\rest\exceptions
 *
 * Used for notifying the client that they forgot the current datetime header
 */
class MissingCurrentDatetimeException extends Exception
{

}
