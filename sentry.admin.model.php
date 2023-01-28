<?php

class SentryAdminModel extends Sentry
{
	/**
	 * Get Sentry DSN from file.
	 * 
	 * @return string|null
	 */
	public function getSentryDsn (): ?string
	{
		if (!file_exists(self::SENTRY_CONFIG_PATH))
		{
			return null;
		}
		
		return include self::SENTRY_CONFIG_PATH;
	}
}
