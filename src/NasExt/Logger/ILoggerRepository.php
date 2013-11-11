<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Logger;

/**
 * ILoggerRepository
 *
 * @author Dusan Hudak
 */
interface ILoggerRepository
{

	/**
	 * save
	 * @param string $message
	 * @param string $exception
	 * @param string $exceptionFilename
	 * @param string $identifier
	 * @param int $priority
	 * @param string $args
	 */
	public function save($message, $exception = NULL, $exceptionFilename = NULL, $identifier = NULL, $priority = NULL, $args = NULL);
}
