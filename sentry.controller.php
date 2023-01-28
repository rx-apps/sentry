<?php

use Rhymix\Framework\Debug;
use function Sentry\init as initSentry;
use function Sentry\captureException;
use function Sentry\captureLastError;

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
		
		set_exception_handler(static function (Exception $e) {
			captureException($e);
			Debug::exceptionHandler($e);
		});
		
		register_shutdown_function(static function () {
			captureLastError();
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
		initSentry([ 'dsn' => (include self::SENTRY_CONFIG_PATH) ]);

		return true;
	}
}
