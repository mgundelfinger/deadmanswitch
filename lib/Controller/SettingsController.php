<?php
// SPDX-FileCopyrightText: Mikael Nazarenko <miknazarenko@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use DateTimeImmutable;
use OCA\DeadManSwitch\Db\AliveStatusMapper;
use OCA\DeadManSwitch\Db\ContactsGroupMapper;
use OCA\DeadManSwitch\Db\UserSettingsMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IUser;
use OCP\IUserSession;

class SettingsController extends BasicController {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $aliveStatusMapper;

	private $contactsGroupMapper;

	private $userSettingsMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		AliveStatusMapper $aliveStatusMapper,
		ContactsGroupMapper $contactsGroupMapper,
		UserSettingsMapper $userSettingsMapper,
	) {
		parent::__construct($appName, $request, $currentUser, $userSettingsMapper);
		$this->currentUser = $currentUser->getUser();
		$this->aliveStatusMapper = $aliveStatusMapper;
		$this->contactsGroupMapper = $contactsGroupMapper;
		$this->userSettingsMapper = $userSettingsMapper;
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
		$userSettings = $this->userSettingsMapper->getSettingsOfUser($userId);
		if ($aliveStatus == null) {
			$aliveStatus = $this->aliveStatusMapper->createAliveStatus($userId);
		}

		$contactGroups = $this->contactsGroupMapper->getList($userId);

		return $this->getTemplate('settings/settings', [
			'page' => 'settings', 'aliveStatus' => $aliveStatus, 'contactGroups' => $contactGroups, 'userSettings' => $userSettings
		]);
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
		$contactGroup = (int) $this->request->getParam('contactGroup');
		$color = (int) $this->request->getParam('color');

		if(!$aliveDays) {
			$errors['aliveDays'] = 'Alive days must be specified and > 0';
		}
		if(!$pendingDays) {
			$errors['pendingDays'] = 'Pending days must be specified and > 0';
		}
		if(!$contactGroup) {
			$errors['contactGroup'] = 'Contact group must be selected';
		}

		if($errors) {
			return $this->getTemplate('settings/settings', ['page' => 'settings', 'errors' => $errors]);
		}

		$aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($userId);
		$aliveStatus->setUserId($userId);
		$aliveStatus->setAliveDays($aliveDays);
		$aliveStatus->setPendingDays($pendingDays);
		$aliveStatus->setStatus($aliveStatus->getStatus());
		$aliveStatus->setLastChangeAsDate(new DateTimeImmutable());
		$aliveStatus->setContactsGroupId($contactGroup);

		if(!$aliveStatus->getId()) {
			$this->aliveStatusMapper->insert($aliveStatus);
		} else {
			$this->aliveStatusMapper->update($aliveStatus);
		}

		$userSettings = $this->userSettingsMapper->getSettingsOfUser($userId);

		$userSettings->setUserId($userId);
		$userSettings->setColor($color);
		$userSettings->setLocale('de');
		if(!$userSettings->getId()) {
			$this->userSettingsMapper->insert($userSettings);
		} else {
			$this->userSettingsMapper->update($userSettings);
		}

		return new RedirectResponse('/index.php/apps/deadmanswitch/settings');
	}


}
