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
		$php = '<?php' . "\n" . 'if (!defined(\'RX_BASEDIR\')) exit();' . "\n" . 'return \'%s\';';
		$php = sprintf($php, escape($dsn));
		
		return file_put_contents(self::SENTRY_CONFIG_PATH, $php) !== false;
	}
}
