<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\AliveStatus;
use OCA\DeadManSwitch\Db\AliveStatusMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\AppInfo\Application;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IUser;
use OCP\IUserSession;

class SettingsController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $aliveStatusMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		AliveStatusMapper $aliveStatusMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->aliveStatusMapper = $aliveStatusMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/settings')]
	public function settings(): TemplateResponse {
		$userId = $this->currentUser->getUID();
		$aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($userId);

		return new TemplateResponse(
			Application::APP_ID,
			'settings/settings',
			['page' => 'settings', 'aliveStatus' => $aliveStatus]
		);
	}






	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/settings/update')]
	public function update(): Response {
		$userId = $this->currentUser->getUID();

		$errors = [];
		$aliveDays = (int) $this->request->getParam('aliveDays');
		$pendingDays = (int) $this->request->getParam('pendingDays');
		if(!$aliveDays) {
			$errors['aliveDays'] = 'Alive days must be specified and > 0';
		}
		if(!$pendingDays) {
			$errors['pendingDays'] = 'Pendinf days must be specified and > 0';
		}
		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'settings/settings',
				['page' => 'settings', 'errors' => $errors]
			);
		}

		$aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($userId);
		$aliveStatus->setUserId($userId);
		$aliveStatus->setAliveDays($aliveDays);
		$aliveStatus->setPendingDays($pendingDays);
		$aliveStatus->setStatus((int) $aliveStatus->getStatus());
		$aliveStatus->setLastChange(date('Y-m-d H:i:s'));
		$aliveStatus->setContactsGroupId(2);

		if(!$aliveStatus->getId()) {
			$this->aliveStatusMapper->insert($aliveStatus);
		} else {
			$this->aliveStatusMapper->update($aliveStatus);
		}


		return new RedirectResponse('/index.php/apps/deadmanswitch/settings');
	}


}
