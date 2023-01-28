<?php

use Rhymix\Framework\Debug;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\SentrySdk;
use Sentry\Serializer\RepresentationSerializer;
use Sentry\StacktraceBuilder;
use function Sentry\captureEvent;
use function Sentry\captureException;
use function Sentry\init as initSentry;

class SentryController extends Sentry
{
	/**
	 * Trigger to run before module initialization.
	 * 
	 * @return bool
	 * @noinspection PhpUnused
	 */
	public function triggerBeforeModuleInit (): bool
	{
		$this->registerErrorHandlers(error_reporting());
		return true;
	}

	/**
	 * Register error handlers.
	 * 
	 * @param int $errorLevels
	 * @return void
	 * @noinspection PhpParamsInspection
	 */
	private function registerErrorHandlers (int $errorLevels)
	{
		if (!$this->initSentry()) {
			return;
		}
		
		set_error_handler(static function (int $errno, string $errStr, string $errFile, int $errLine) {
			Debug::addError($errno, $errStr, $errFile, $errLine);
		}, $errorLevels);
		
		set_exception_handler(static function (Throwable $e) {
			captureException($e);
			Debug::exceptionHandler($e);
		});
		
		register_shutdown_function(static function () {
			$errInfo = error_get_last();
			if ($errInfo === null || ($errInfo['type'] !== 1 && $errInfo['type'] !== 4)) {
				return;
			}
			$errInfo['file'] = Debug::translateFilename($errInfo['file']);
			
			$client = SentrySdk::getCurrentHub()->getClient();
			$options = $client->getOptions();
			$stacktraceBuilder = new StacktraceBuilder($options, new RepresentationSerializer($options));
			
			$event = Event::createEvent();
			$event->setStacktrace($stacktraceBuilder->buildFromBacktrace(debug_backtrace(2), $errInfo['file'], $errInfo['line'] - 3));
			
			$hint = new EventHint();
			$hint->exception = new ErrorException($errInfo['message'], 0, $errInfo['type'], $errInfo['file'], $errInfo['line']);
			captureEvent($event, $hint);
		});
	}


	/**
	 * Initialize sentry SDK instance.
	 *
	 * @return bool
	 */
	private function initSentry (): bool
	{
		if (!file_exists(self::SENTRY_CONFIG_PATH)) {
			return false;
		}

		require_once __DIR__ . '/vendor/autoload.php';
		initSentry([ 'dsn' => $this->getAdminModel()->getSentryDsn() ]);

		return true;
	}
}
