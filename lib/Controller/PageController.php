<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use DateTime;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\PreConditionNotMetException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\Controller\CheckInController;
use OCA\DeadManSwitch\Service\MailService;
use OCA\DeadManSwitch\AppInfo\Application;
use OCP\IUser;
use OCP\IUserSession;

class PageController extends Controller {

	public const ACTIVE_CONFIG_KEY = 'active';
	public const CHECK_IN_INTERVAL_CONFIG_KEY = 'check_in_interval';
	public const LAST_CHECK_IN_CONFIG_KEY = 'last_check_in';

	public const CONFIG_KEYS = [
		self::ACTIVE_CONFIG_KEY,
		self::CHECK_IN_INTERVAL_CONFIG_KEY,
		self::LAST_CHECK_IN_CONFIG_KEY
	];

	/**
	 * @var IInitialState
	 */
	private $initialStateService;
	/**
	 * @var IConfig
	 */
	private $config;
	/**
	 * @var string|null
	 */
	private $userId;

	/**
	 * @var MailService
	 */
	private $mailService;

	/**
	 * @var CheckInController
	 */
	private $checkInController;

	/**
	 * @var IUser
	 */
	private $currentUser;
	

	public function __construct(string $appName,
								IRequest $request,
								IInitialState $initialStateService,
								IConfig $config,
								?string $userId,
								MailService $mailService,
								CheckInController $checkInController,
								IUserSession $currentUser) {
		parent::__construct($appName, $request);
		$this->initialStateService = $initialStateService;
		$this->config = $config;
		$this->userId = $userId;
		$this->mailService = $mailService;
		$this->checkInController = $checkInController;
		$this->currentUser = $currentUser->getUser();
	}

	/**
	 * This returns the template of the main app's page
	 * It adds some initialState data (file list and fixed_gif_size config value)
	 * and also provide some data to the template (app version)
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function mainPage(): TemplateResponse {
		$active = $this->config->getUserValue($this->userId, Application::APP_ID, self::ACTIVE_CONFIG_KEY, false);
		$interval = $this->config->getUserValue($this->userId, Application::APP_ID, self::CHECK_IN_INTERVAL_CONFIG_KEY, 1);
		$initialState = [
			self::ACTIVE_CONFIG_KEY => $active,
			self::CHECK_IN_INTERVAL_CONFIG_KEY => $interval
		];
		$this->initialStateService->provideInitialState('initial_state', $initialState);

		$appVersion = $this->config->getAppValue(Application::APP_ID, 'installed_version');
		return new TemplateResponse(
			Application::APP_ID,
			'myMainTemplate',
			[
				'app_version' => $appVersion,
			]
		);
	}

	/**
	 * This is an API endpoint to set a user config value
	 * It returns a simple DataResponse: a message to be displayed
	 *
	 * @NoAdminRequired
	 *
	 * @param string $interval
	 * @param bool $active
	 * @return DataResponse
	 * @throws PreConditionNotMetException
	 */
	public function saveConfig(string $interval, bool $active): DataResponse {
		$userEmail = $this->currentUser->getEMailAddress();
		$uid = $this->currentUser->getUID();
		$this->config->setUserValue($uid, Application::APP_ID, self::CHECK_IN_INTERVAL_CONFIG_KEY, $interval);
		$this->config->setUserValue($uid, Application::APP_ID, self::ACTIVE_CONFIG_KEY, (string) $active);
		$this->config->setUserValue($uid, Application::APP_ID, self::LAST_CHECK_IN_CONFIG_KEY, date_format(new DateTime(), 'Ymd'));

		$this->mailService->notify($userEmail, $active ? 'enabled' : 'disabled');
		if ($active) {
			$this->checkInController->addJob($userEmail, $uid);
		} else {
			$this->checkInController->removeJob($userEmail, $uid);
		}

		return new DataResponse([
			'message' => 'your email is ' . $this->currentUser->getEMailAddress(),
		]);
	}
}