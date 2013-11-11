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

use Nette\Diagnostics\Debugger;
use Nette\Diagnostics\Dumper;
use Nette\FatalErrorException;
use Nette\Object;

/**
 * Logger
 *
 * @author Dusan Hudak
 */
class Logger extends Object
{
	const INFO = 1;
	const ERROR = 2;
	const WARNING = 3;

	/** @var int */
	private $defaultLogLevel = self::INFO;

	/** @var string */
	private $loggerDirectory;

	/** @var ILoggerRepository */
	private $loggerRepository;


	/**
	 * @param ILoggerRepository $loggerRepository
	 * @param string $loggerDirectory
	 * @param int $defaultLogLevel
	 * @throws InvalidArgumentException
	 */
	public function __construct(ILoggerRepository $loggerRepository, $loggerDirectory, $defaultLogLevel)
	{
		$this->loggerRepository = $loggerRepository;

		if ($loggerDirectory == NULL) {
			throw new InvalidArgumentException('Logger Directory must be defined.');
		}
		$this->loggerDirectory = $loggerDirectory;

		if ($defaultLogLevel != NULL) {
			if ($defaultLogLevel < self::INFO || $defaultLogLevel > self::WARNING) {
				throw new InvalidArgumentException('Default Log Level must be one of the NasExt\Logger\'s priority constants.');
			}
			$this->defaultLogLevel = $defaultLogLevel;
		}
	}


	/**
	 * generateExceptionFile
	 * @param Exception $exception
	 * @return string
	 */
	private function generateExceptionFile($exception)
	{
		$hash = md5($exception);
		$exceptionFilename = "exception-" . @date('Y-m-d-H-i-s') . "-$hash.html";
		foreach (new \DirectoryIterator($this->loggerDirectory) as $entry) {
			if (strpos($entry, $hash)) {
				$exceptionFilename = $entry;
				$saved = TRUE;
				break;
			}
		}

		$exceptionFilename = $this->loggerDirectory . '/' . $exceptionFilename;
		if (empty($saved) && $logHandle = @fopen($exceptionFilename, 'w')) {
			ob_start(); // double buffer prevents sending HTTP headers in some PHP
			ob_start(function ($buffer) use ($logHandle) {
				fwrite($logHandle, $buffer);
			}, 4096);
			Debugger::getBlueScreen()->render($exception);
			ob_end_flush();
			ob_end_clean();
			fclose($logHandle);
		}
		return $exceptionFilename;
	}


	/**
	 * Log a message
	 * @param Exception|string|array $message
	 * @param null|string $identifier
	 * @param null|int $priority
	 * @param null|array $args
	 * @throws InvalidArgumentException
	 */
	public function message($message, $identifier = NULL, $priority = NULL, $args = NULL)
	{
		if (!$message) {
			throw new InvalidArgumentException('The message has to be specified.');
		}

		if ($priority == NULL) {
			$priority = $this->defaultLogLevel;
		}

		if ($priority < self::INFO || $priority > self::WARNING) {
			throw new InvalidArgumentException('Default Log Level must be one of the NasExt\Logger\'s priority constants.');
		}

		$exception = NULL;
		$exceptionFilename = NULL;
		if ($message instanceof \Exception) {
			$exception = ($message instanceof FatalErrorException ? 'Fatal error: ' . $message->getMessage() : 'HTTP code' . $message->getCode() . ':: ' . get_class($message) . ": " . $message->getMessage())
				. " in " . $message->getFile() . ":" . $message->getLine();
			$exceptionFilename = $this->generateExceptionFile($message);
			$message = $message->getMessage();
		}

		if (!is_string($args)) {
			$args = Dumper::toText($args);
		}

		if (!is_string($message)) {
			$message = Dumper::toText($message);
		}

		$this->loggerRepository->save($message, $exception, $exceptionFilename, $identifier, $priority, $args);
	}
}
