<?php

class SentryAdminView extends Sentry
{
	/**
	 * @var array[] Administrator menu items.
	 */
	protected static $_menus = [
		// [ string $menu_lang_key, [ string $act, string ...$sub_act ], (bool) $visibility = true ]
		[ 'sentry_admin_menu_index', [ 'dispSentryAdminIndex' ] ],
		[ 'sentry_admin_menu_test', [ 'procSentryAdminTest' ] ],
	];

	/**
	 * Initialize.
	 * 
	 * @return void
	 */
	public function init ()
	{
		Context::set('menus', self::$_menus);
		$this->setTemplatePath(__DIR__ . '/tpl');
	}
	
	/**
	 * Action to modify module configuration.
	 * 
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function dispSentryAdminIndex ()
	{
		$oAdminModel = $this->getAdminModel();

		$current_version = $oAdminModel->getCurrentVersion();
		Context::set('current_version', $current_version);

		$github_version = $oAdminModel->getGithubVersion();
		Context::set('github_version', $github_version);

		$is_github_updated = version_compare($current_version, $github_version, '>=');
		Context::set('is_github_updated', $is_github_updated);

		$github_url = $oAdminModel->getGithubUrl();
		Context::set('github_url', $github_url);
		
		$oModel = $this->getAdminModel();
		Context::set('enabled', $oModel->isSentryEnabled());
		Context::set('sentry_dsn', $oModel->getSentryDsn());

		$this->setTemplateFile('index');
	}
}
