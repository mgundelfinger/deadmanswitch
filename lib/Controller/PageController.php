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
use OCP\AppFramework\Services\IAppConfig;
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
	 * @var IAppConfig
	 */
	private $config;

	public function __construct(
		string $appName,
		IRequest $request,
		IInitialState $initialStateService,
		IAppConfig $config,
		?string $userId,
		MailService $mailService,
		CheckInController $checkInController,
		IUserSession $currentUser,
		JobMapper $jobMapper,
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function mainPage(): TemplateResponse {
		$appVersion = $this->config->getAppValueString('installed_version');
		return new TemplateResponse(
			Application::APP_ID,
			'myMainTemplate',
			[
				'app_version' => $appVersion,
			]
		);
	}
}
