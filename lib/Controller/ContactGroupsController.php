<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\ContactsGroup;
use OCA\DeadManSwitch\Db\ContactsGroupMapper;
use OCA\DeadManSwitch\Db\UserSettingsMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IL10N;
use OCP\IUser;
use OCP\IUserSession;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactGroupsController extends BasicController {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $contactsGroupMapper;

	private IL10N $l;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		ContactsGroupMapper $contactsGroupMapper,
		UserSettingsMapper $userSettingsMapper,
		IL10N $l,
	) {
		parent::__construct($appName, $request, $currentUser, $userSettingsMapper);
		$this->currentUser = $currentUser->getUser();
		$this->contactsGroupMapper = $contactsGroupMapper;
		$this->l = $l;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contact-groups')]
	public function contactGroups(): TemplateResponse {

		return $this->getTemplate('contact-groups/contact-groups', ['page' => 'contact-groups']);

	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-contact-groups')]
	public function getContactGroups(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$contactsGroups = $this->contactsGroupMapper->getContactGroupsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($contactsGroups as $contactsGroup) {
			$data[] = [
				'name' => $contactsGroup->getName(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/contact-groups/delete?id='.$contactsGroup->getId().'">' . $this->l->t("Delete") . '</a>
					<a href="/index.php/apps/deadmanswitch/contact-groups/edit?id='.$contactsGroup->getId().'">' . $this->l->t("Edit") . '</a>'
			];
		}

		$contactsGroupsCount = $this->contactsGroupMapper->getContactsGroupsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $contactsGroupsCount,
			'recordsFiltered' => $contactsGroupsCount,
			'data' => $data
		]);

		header('Content-Type: application/json; charset=utf-8');
		echo $data;
		die;

	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contact-groups/create')]
	public function create(): TemplateResponse {
		$contactsGroup = new ContactsGroup();

		return $this->getTemplate('contact-groups/create', ['page' => 'contact-groups', 'contactsGroup' => $contactsGroup]);

	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/contact-groups/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$contactsGroup = new ContactsGroup();
		$contactsGroup->loadData($this->request->getParams());
		$contactsGroup->setUserId($userId);
		$errors = $contactsGroup->validate();

		if($errors) {
			return $this->getTemplate('contact-groups/create', ['page' => 'contact-groups', 'contactsGroup' => $contactsGroup, 'errors' => $errors]);
		}

		$this->contactsGroupMapper->insert($contactsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/contact-groups');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/contact-groups/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$contactsGroup = $this->contactsGroupMapper->getContactGroupOfUser($id, $userId);

		$contactsGroup->loadData($this->request->getParams());
		$contactsGroup->setUserId($userId);
		$errors = $contactsGroup->validate();

		if($errors) {
			return $this->getTemplate('contact-groups/edit', ['page' => 'contact-groups', 'contactsGroup' => $contactsGroup, 'errors' => $errors]);
		}

		$this->contactsGroupMapper->update($contactsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/contact-groups');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contact-groups/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$contactsGroup = $this->contactsGroupMapper->getContactGroupOfUser($id, $userId);

		return $this->getTemplate('contact-groups/edit', ['page' => 'contact-groups', 'contactsGroup' => $contactsGroup]);

	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contact-groups/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->contactsGroupMapper->deleteContactGroup($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/contact-groups');
	}


}
