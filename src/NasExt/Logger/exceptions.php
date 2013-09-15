<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 20013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Logger;

/**
 * Common interface for caching mandrill exceptions
 *
 * @author Dusan Hudak
 */
interface Exception
{

}

/**
 * Class InvalidArgumentException
 */
class InvalidArgumentException extends \Exception implements Exception
{

}
