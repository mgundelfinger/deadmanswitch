<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\CheckupIntervalMapper;
use OCA\DeadManSwitch\Db\ConfirmatorMapper;
use OCA\DeadManSwitch\Db\ConfirmatorsGroupMapper;
use OCA\DeadManSwitch\Db\Confirmator;
use OCA\DeadManSwitch\Db\ContactMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\AppInfo\Application;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IUser;
use OCP\IUserSession;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConfirmatorController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $confirmatorMapper;

	/**
	 * @var ConfirmatorsGroupMapper
	 */
	private $confirmatorsGroupMapper;

	private $contactMapper;

	private $checkupIntervalMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		ConfirmatorMapper $confirmatorMapper,
		ConfirmatorsGroupMapper $confirmatorsGroupMapper,
		ContactMapper $contactMapper,
		CheckupIntervalMapper $checkupIntervalMapper
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->confirmatorMapper = $confirmatorMapper;
		$this->confirmatorsGroupMapper = $confirmatorsGroupMapper;
		$this->contactMapper = $contactMapper;
		$this->checkupIntervalMapper = $checkupIntervalMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmators')]
	public function confirmators(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'confirmators/confirmators',
			['page' => 'confirmators']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-confirmators')]
	public function getConfirmators(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$confirmators = $this->confirmatorMapper->getConfirmatorsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($confirmators as $confirmator) {
			$data[] = [
				'contact' => $confirmator->getContactId(),
				'interval' => $confirmator->getIntervalId(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/confirmators/delete?id='.$confirmator->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/confirmators/edit?id='.$confirmator->getId().'">Edit</a>'
			];
		}

		$confirmatorsCount = $this->confirmatorMapper->getConfirmatorsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $confirmatorsCount,
			'recordsFiltered' => $confirmatorsCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/confirmators/create')]
	public function create(): TemplateResponse {
		$confirmator = new Confirmator();
		$userId = $this->currentUser->getUID();
		$groupsList = $this->confirmatorsGroupMapper->getList($userId);

		$contactList = $this->contactMapper->getList($userId);
		$checkupIntervalList = $this->checkupIntervalMapper->getList($userId);

		return new TemplateResponse(
			Application::APP_ID,
			'confirmators/create',
			[
				'page' => 'confirmators', 'confirmator' => $confirmator, 'groupsList' => $groupsList,
				'contactList' => $contactList, 'checkupIntervalList' => $checkupIntervalList
			]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/confirmators/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$confirmator = new Confirmator();
		$confirmator->loadData($this->request->getParams());
		$confirmator->setUserId($userId);
		$errors = $confirmator->validate();

		if($errors) {
			$groupsList = $this->confirmatorsGroupMapper->getList($userId);
			$currentGroups = $this->confirmatorMapper->getGroups($confirmator);
			return new TemplateResponse(
				Application::APP_ID,
				'confirmators/create',
				['page' => 'confirmators', 'confirmator' => $confirmator, 'errors' => $errors, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups]
			);
		}

		$this->confirmatorMapper->insert($confirmator);
		$groupsIds = (array) $this->request->getParam('confirmatorGroups');
		$groups = $this->confirmatorsGroupMapper->getGroups($userId, $groupsIds);
		$this->confirmatorsGroupMapper->updateGroups($confirmator, $groups);
		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmators');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/confirmators/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$confirmator = $this->confirmatorMapper->getConfirmatorOfUser($id, $userId);

		$confirmator->loadData($this->request->getParams());
		$confirmator->setUserId($userId);
		$errors = $confirmator->validate();

		if($errors) {
			$groupsList = $this->confirmatorsGroupMapper->getList($userId);
			$currentGroups = $this->confirmatorMapper->getGroups($confirmator);
			return new TemplateResponse(
				Application::APP_ID,
				'confirmators/edit',
				['page' => 'confirmators', 'confirmator' => $confirmator, 'errors' => $errors, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups]
			);
		}

		if($confirmator->isModified()) {
			$this->confirmatorMapper->update($confirmator);
		}
		$groupsIds = (array) $this->request->getParam('contactGroups');
		$groups = $this->confirmatorsGroupMapper->getGroups($userId, $groupsIds);
		$this->confirmatorsGroupMapper->updateGroups($confirmator, $groups);
		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmators');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmators/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$confirmator = $this->confirmatorMapper->getConfirmatorOfUser($id, $userId);
		$groupsList = $this->confirmatorsGroupMapper->getList($userId);
		$currentGroups = $this->confirmatorMapper->getGroups($confirmator);

		$contactList = $this->contactMapper->getList($userId);
		$checkupIntervalList = $this->checkupIntervalMapper->getList($userId);
		$currentContact = $this->contactMapper->getContactOfUser($confirmator->getContactId(), $userId);
		$currentInterval = $this->checkupIntervalMapper->getCheckupIntervalOfUser($confirmator->getIntervalId(), $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'confirmators/edit',
			[
				'page' => 'confirmators', 'confirmator' => $confirmator, 'groupsList' => $groupsList,
				'currentGroups' => $currentGroups, 'contactList' => $contactList, 'checkupIntervalList' => $checkupIntervalList,
				'currentContact' => $currentContact, 'currentInterval' => $currentInterval
			]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmators/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->confirmatorMapper->deleteConfirmator($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmators');
	}


}
