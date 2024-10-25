<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\AppInfo\Application;
use OCA\DeadManSwitch\Db\UserSettingsMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;

class BasicController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $userSettingsMapper;

	private $userSettings;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		UserSettingsMapper $userSettingsMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->userSettingsMapper = $userSettingsMapper;
		$userId = $this->currentUser->getUID();
		$this->userSettings = $this->userSettingsMapper->getSettingsOfUser($userId);
	}

	protected function getTemplate($templateName, $params) {
		$params['userSettings'] = $this->userSettings;
		return new TemplateResponse(
			Application::APP_ID,
			$templateName,
			$params
		);
	}

}
