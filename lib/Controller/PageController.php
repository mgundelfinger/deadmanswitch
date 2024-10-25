<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\UserSettingsMapper;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IUserSession;

class PageController extends BasicController {

	/**
	 * @var IAppConfig
	 */
	private $config;

	public function __construct(
		string $appName,
		IRequest $request,
		IAppConfig $config,
		IUserSession $currentUser,
		UserSettingsMapper $userSettingsMapper,
	) {
		parent::__construct($appName, $request, $currentUser, $userSettingsMapper);
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

		return $this->getTemplate('myMainTemplate', 			[
			'app_version' => $appVersion,
		]);
	}
}
