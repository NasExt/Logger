NasExt/Logger
===========================

Requirements
------------

NasExt/Logger requires PHP 5.3.2 or higher.

- [Nette Framework 2.0.x](https://github.com/nette/nette)


Installation
-----------

The best way to install NasExt/Logger is using  [Composer](http://getcomposer.org/):

```sh
$ composer require nasext/logger:@dev
```

Enable the extension using your neon config.

```yml
services:
    loggerRepository: LoggerRepository

nasext.logger:
    loggerRepository: @loggerRepository
    loggerDirectory: log/logger

extensions:
	nasext.logger: NasExt\Logger\DI\LoggerExtension
```

## Use
###Presenter - inject service
```php
/** @var \NasExt\Logger\Logger */
protected $logger;

/**
 * INJECT Logger
 * @param \NasExt\Logger\Logger $logger
 */
public function injectLoger(\NasExt\Logger\Logger $logger) {
    $this->logger = $logger;
}
```
###Presenter - log
```php
$this->logger->message('Message ......');
$this->logger->message('Message ......', 'SOME-IDENTIFER', \NasExt\Logger\Logger::WARNING);
$this->logger->message('Message ......', 'SOME-IDENTIFER', \NasExt\Logger\Logger::WARNING);
$this->logger->message('Message ......', 'SOME-IDENTIFER', \NasExt\Logger\Logger::WARNING, array( some values ));
```

You can also log exceptions:
```php
try {
    ...
} catch (\Exception $e) {
    $this->logger->message($e);
}
```

###Logger Repository
```php
class LoggerRepository extends Repository implements \NasExt\Logger\ILoggerRepository {

    /**
     * save
     * @param string $message
     * @param string $exception
     * @param string $exceptionFilename
     * @param string $identifier
     * @param int $priority
     * @param string $args
     */
    public function save($message, $exception = NULL, $exceptionFilename = NULL, $identifier = NULL, $priority = NULL, $args = NULL) {
       ...
    }

}
```
For example
```php
class LoggerRepository extends Repository implements \NasExt\Logger\ILoggerRepository {

	/** @var  Nette\Security\User */
	private $user;

	/** @var  Nette\Http\IRequest */
	private $httpRequest;
    /**
     * save
     * @param string $message
     * @param string $exception
     * @param string $exceptionFilename
     * @param string $identifier
     * @param int $priority
     * @param string $args
     */
    public function save($message, $exception = NULL, $exceptionFilename = NULL, $identifier = NULL, $priority = NULL, $args = NULL) {
        $logEntityData = array(
            'user_id' => $this->user->getId(),
            'ip' => $this->httpRequest->getRemoteAddress(),
            'datetime' => new \Nette\DateTime(),
            'priority' => $priority,
            'exception' => $exception,
            'exceptionFilename' => $exceptionFilename,
            'message' => $message,
            'identifier' => $identifier,
            'args' => $args,
            'url' => $this->httpRequest->getUrl()->absoluteUrl,
        );
        $logEntity = $this->getTable()->insert($logEntityData);
        return $logEntity;
    }

}
```

-----

Repository [http://github.com/nasext/logger](http://github.com/nasext/logger).