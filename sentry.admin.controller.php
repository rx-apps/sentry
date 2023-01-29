<?php

class SentryAdminController extends Sentry
{
	/**
	 * Save Sentry DSN to file.
	 * 
	 * @param string $dsn
	 * @return bool
	 */
	public function setSentryDsn (string $dsn): bool
	{
		$php = '<?php' . "\n" . 'if (!defined(\'RX_BASEDIR\')) exit();' . "\n" . 'return %s;';
		$php = sprintf($php, var_export($dsn, true));
		
		$result = file_put_contents(self::SENTRY_CONFIG_PATH, $php) !== false;
		if ($result && function_exists('opcache_invalidate')) {
			opcache_invalidate(self::SENTRY_CONFIG_PATH);
		}
		
		return $result;
	}

	/**
	 * Action to save module configuration.
	 * 
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function procSentryAdminIndex ()
	{
		$sentryDsn = Context::get('sentry_dsn');
		if (!$this->setSentryDsn($sentryDsn)) {
			$this->setError(-1);
			$this->setMessage('');
		}
		else {
			$this->setMessage('success_updated');
		}
		
		$this->setRedirectUrl(getNotEncodedUrl('', 'module', 'admin', 'act', 'dispSentryAdminIndex'));
	}

	/**
	 * Action to throw a test exception.
	 *
	 * @return void
	 * @throws Exception
	 * @noinspection PhpUnused
	 */
	public function procSentryAdminTest ()
	{
		throw new Exception('This is a test exception from rx-apps/sentry module.');
	}
}
