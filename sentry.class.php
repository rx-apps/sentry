<?php

class Sentry extends ModuleObject
{
	protected const SENTRY_CONFIG_PATH = RX_BASEDIR . 'files/config/sentry.php';
	
	/**
	 * @var array 등록할 트리거 목록
	 */
	protected static $_insert_triggers = [
		// [ string $triggerName, string $triggerPosition, string $controllerMethodName ]
		// [ 'moduleHandler.init', 'before', 'triggerBeforeModuleInit' ]
	];

	/**
	 * @var array 삭제할 트리거 목록
	 */
	protected static $_delete_triggers = [
		// [ string $triggerName, string $triggerPosition, string $controllerMethodName ]
	];

	/**
	 * 컨트롤러 인스턴스를 가져옵니다.
	 *
	 * @return SentryController
	 */
	protected function getController(): self
	{
		return SentryController::getInstance();
	}

	/**
	 * 관리자 모델 인스턴스를 가져옵니다.
	 *
	 * @return SentryAdminModel
	 */
	protected function getAdminModel(): self
	{
		return SentryAdminModel::getInstance();
	}

	/**
	 * 관리자 컨트롤러 인스턴스를 가져옵니다.
	 *
	 * @return SentryAdminController
	 */
	protected function getAdminController(): self
	{
		return SentryAdminController::getInstance();
	}

	/**
	 * @var object? 모듈 설정값 캐시
	 */
	protected static $_config_cache = null;

	/**
	 * 모듈 설정값을 가져옵니다.
	 *
	 * @return object
	 */
	public function getConfig(): object
	{
		if(self::$_config_cache === null)
		{
			$oModuleModel = getModel('module');
			self::$_config_cache = $oModuleModel->getModuleConfig($this->module) ?: new stdClass();
		}

		return self::$_config_cache;
	}

	/**
	 * 모듈 설정값을 DB에 저장합니다.
	 *
	 * @param object $config
	 * @return object
	 */
	public function setConfig(object $config): object
	{
		$oModuleController = getController('module');
		$output = $oModuleController->insertModuleConfig($this->module, $config);

		if(!$output->toBool())
		{
			self::$_config_cache = $config;
		}
		return $output;
	}

	/**
	 * XE 오브젝트를 생성합니다.
	 *
	 * @param int $status
	 * @param string $message
	 * @return BaseObject
	 * @noinspection PhpIncompatibleReturnTypeInspection
	 * @noinspection PhpUndefinedClassInspection
	 */
	protected function createObject(int $status = 0, string $message = 'success')
	{
		$args = func_get_args();
		if(count($args) > 2)
		{
			global $lang;
			$message = sprintf($lang->$message, ...array_slice($args, 2));
		}

		return class_exists('BaseObject') ? new BaseObject($status, $message) : new Object($status, $message);
	}

	/**
	 * 트리거 업데이트 여부를 확인합니다.
	 *
	 * @return bool
	 */
	private function isTriggersUpdated(): bool
	{
		foreach(self::$_insert_triggers as $trigger)
		{
			if(!$this->isTriggerExists($trigger))
			{
				return false;
			}
		}

		foreach(self::$_delete_triggers as $trigger)
		{
			if($this->isTriggerExists($trigger))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * 트리거의 존재 여부를 확인합니다.
	 *
	 * @param array $trigger
	 * @return bool
	 */
	protected function isTriggerExists(array $trigger): bool
	{
		$oModuleModel = getModel('module');
		return (bool) $oModuleModel->getTrigger($trigger[0], $this->module, 'controller', $trigger[2], $trigger[1]);
	}

	/**
	 * 트리거를 등록합니다.
	 *
	 * @return BaseObject
	 */
	private function registerTriggers()
	{
		$oModuleController = getController('module');

		foreach(self::$_insert_triggers as $trigger)
		{
			if(!$this->isTriggerExists($trigger))
			{
				$oModuleController->insertTrigger($trigger[0], $this->module, 'controller', $trigger[2], $trigger[1]);
			}
		}

		foreach(self::$_delete_triggers as $trigger)
		{
			if($this->isTriggerExists($trigger))
			{
				$oModuleController->deleteTrigger($trigger[0], $this->module, 'controller', $trigger[2], $trigger[1]);
			}
		}

		return $this->createObject(0, 'success_updated');
	}

	/**
	 * 관리자 페이지에서 모듈 설치 시 호출됩니다.
	 *
	 * @return BaseObject
	 */
	public function moduleInstall()
	{
		return $this->registerTriggers();
	}

	/**
	 * 관리자 페이지에서 업데이트가 필요한지 확인하기 위해 호출됩니다.
	 *
	 * @return bool
	 */
	public function checkUpdate(): bool
	{
		return !($this->isTriggersUpdated());
	}

	/**
	 * 관리자 페이지에서 모듈 업데이트 시 호출됩니다.
	 *
	 * @return BaseObject
	 */
	public function moduleUpdate()
	{
		return $this->registerTriggers();
	}

	/**
	 * 여러가지 사유로 캐시 재생성이 필요할 때 호출됩니다.
	 *
	 * @return void
	 */
	public function recompileCache()
	{
	}
}
