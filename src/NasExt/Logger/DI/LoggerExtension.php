<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Logger\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
	class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
}

if (isset(\Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
	unset(\Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']);
	class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

/**
 * LoggerExtension
 *
 * @author Dusan Hudak
 */
class LoggerExtension extends CompilerExtension
{

	/** @var array */
	public $defaults = array(
		'loggerRepository' => FALSE,
		'loggerDirectory' => NULL,
		'defaultLogLevel' => NULL,
	);


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('logger'))
			->setClass('NasExt\Logger\Logger')
			->setArguments(array($config['loggerRepository'], $config['loggerDirectory'], $config['defaultLogLevel']));
	}


	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $config, Compiler $compiler) {
			$compiler->addExtension('logger', new LoggerExtension());
		};
	}
}
