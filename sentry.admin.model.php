<?php

class SentryAdminModel extends Sentry
{
	/**
	 * Get GitHub repository URL.
	 *
	 * @return string
	 */
	public function getGithubUrl(): string
	{
		$module_name = str_replace('_', '-', $this->module);
		return 'https://github.com/rx-apps/' . $module_name;
	}

	/**
	 * Get the latest release version from GitHub.
	 *
	 * @return string
	 */
	public function getGithubVersion(): string
	{
		$module_name = str_replace('_', '-', $this->module);
		$github_url = 'https://api.github.com/repos/rx-apps/' . $module_name . '/releases/latest';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $github_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_USERAGENT, 'rx-apps/1.0');
		curl_setopt($ch, CURLOPT_REFERER, 'https://github.com/rx-apps/');

		($output = json_decode(curl_exec($ch))) && curl_close($ch);
		if(!isset($output->tag_name))
		{
			return '0.0.0';
		}

		return $output->tag_name;
	}

	/**
	 * Get the current module version.
	 * 
	 * @return string
	 */
	public function getCurrentVersion(): string
	{
		$oModuleModel = getModel('module');
		$module_info = $oModuleModel->getModuleInfoXml($this->module);
		return $module_info->version;
	}
	
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
