<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use DateTime;
use OCA\DeadManSwitch\Db\JobMapper;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\PreConditionNotMetException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\Service\MailService;
use OCA\DeadManSwitch\AppInfo\Application;
use OCA\DeadManSwitch\Cron\CheckInTask;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IUser;
use OCP\IUserSession;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends Controller {

	public const ACTIVE_CONFIG_KEY = 'active';
	public const CHECK_IN_INTERVAL_CONFIG_KEY = 'check_in_interval';
	public const LAST_CHECK_IN_CONFIG_KEY = 'last_check_in';
	public const SWITCH_ARMED_CONFIG_KEY = 'switch_armed';
	public const SWITCH_COMPLETE_CONFIG_KEY = 'switch_complete';
	public const TRANSFER_EMAIL_CONFIG_KEY = 'transfer_email';

	public const CONFIG_KEYS = [
		self::ACTIVE_CONFIG_KEY,
		self::CHECK_IN_INTERVAL_CONFIG_KEY,
		self::LAST_CHECK_IN_CONFIG_KEY,
		self::SWITCH_ARMED_CONFIG_KEY,
		self::SWITCH_COMPLETE_CONFIG_KEY,
		self::TRANSFER_EMAIL_CONFIG_KEY,
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

	private $jobMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IInitialState $initialStateService,
		IConfig $config,
		?string $userId,
		MailService $mailService,
		CheckInController $checkInController,
		IUserSession $currentUser,
		JobMapper $jobMapper,
	) {
		parent::__construct($appName, $request);
		$this->initialStateService = $initialStateService;
		$this->config = $config;
		$this->userId = $userId;
		$this->mailService = $mailService;
		$this->checkInController = $checkInController;
		$this->currentUser = $currentUser->getUser();
		$this->jobMapper = $jobMapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function mainPage(): TemplateResponse {
		$userEmail = $this->currentUser->getEMailAddress();
		$uid = $this->currentUser->getUID();
		$active = $this->checkInController->hasJob($userEmail, $uid);
		$interval = $this->config->getUserValue($this->userId, Application::APP_ID, self::CHECK_IN_INTERVAL_CONFIG_KEY, CheckInTask::INTERVAL_DAILY);
		$switchArmed = $this->config->getUserValue($this->userId, Application::APP_ID, self::SWITCH_ARMED_CONFIG_KEY, '0');
		$switchComplete = $this->config->getUserValue($this->userId, Application::APP_ID, self::SWITCH_COMPLETE_CONFIG_KEY, '0');
		$initialState = [
			self::ACTIVE_CONFIG_KEY => ($active ? '1' : '0'),
			self::CHECK_IN_INTERVAL_CONFIG_KEY => $interval,
			self::SWITCH_ARMED_CONFIG_KEY => $switchArmed,
			self::SWITCH_COMPLETE_CONFIG_KEY => $switchComplete,
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
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs')]
	public function jobs(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'jobs',
			['page' => 'jobs']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-jobs')]
	public function getJobs(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$jobs = $this->jobMapper->getJobsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($jobs as $job) {
			$data[] = [
				'name' => $job->getName(),
				'emailSubject' => $job->getEmailSubject(),
			];
		}

		$jobsCount = $this->jobMapper->getJobsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $jobsCount,
			'recordsFiltered' => $jobsCount,
			'data' => $data
		]);

		header('Content-Type: application/json; charset=utf-8');
		echo $data;
		die;


		return new JsonResponse(array('headers' => 'kjhjkh'));
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
	// #[FrontpageRoute(verb: 'PUT', url: '/config/{interval}/{active}')]
	public function saveConfig(string $interval, string $active): DataResponse {
		$userEmail = $this->currentUser->getEMailAddress();
		$uid = $this->currentUser->getUID();

		$this->config->setUserValue($uid, Application::APP_ID, self::CHECK_IN_INTERVAL_CONFIG_KEY, $interval);
		$this->config->setUserValue($uid, Application::APP_ID, self::ACTIVE_CONFIG_KEY, $active);
		$this->config->setUserValue($uid, Application::APP_ID, self::LAST_CHECK_IN_CONFIG_KEY, date_format(new DateTime(), 'Y-m-d'));
		$this->config->setUserValue($uid, Application::APP_ID, self::SWITCH_ARMED_CONFIG_KEY, '0');
		$this->config->setUserValue($uid, Application::APP_ID, self::SWITCH_COMPLETE_CONFIG_KEY, '0');
		$this->config->setUserValue($uid, Application::APP_ID, self::TRANSFER_EMAIL_CONFIG_KEY, $userEmail);

		$active = filter_var($active, FILTER_VALIDATE_BOOLEAN);

		if ($active) {
			$this->checkInController->addJob($userEmail, $uid);
		} else {
			$this->checkInController->removeJob($userEmail, $uid);
		}

		return new DataResponse([
			'message' => 'Dead Man Switch ' . ($active ? 'enabled' : 'disabled') .'!',
		]);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function checkInPage(): TemplateResponse {
		$this->config->setUserValue($this->userId, Application::APP_ID, self::LAST_CHECK_IN_CONFIG_KEY, date_format(new DateTime(), 'Y-m-d'));
		$this->config->setUserValue($this->userId, Application::APP_ID, self::SWITCH_ARMED_CONFIG_KEY, '0');

		$appVersion = $this->config->getAppValue(Application::APP_ID, 'installed_version');
		return new TemplateResponse(
			Application::APP_ID,
			'checkInTemplate',
			[
				'app_version' => $appVersion,
			]
		);
	}
}
