<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\ConfirmatorsGroup;
use OCA\DeadManSwitch\Db\ConfirmatorsGroupMapper;
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

class ConfirmatorGroupsController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $confirmatorsGroupMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		ConfirmatorsGroupMapper $confirmatorsGroupMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->confirmatorsGroupMapper = $confirmatorsGroupMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmator-groups')]
	public function confirmatorGroups(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'confirmator-groups/confirmator-groups',
			['page' => 'confirmator-groups']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-confirmator-groups')]
	public function getConfirmatorGroups(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$confirmatorsGroups = $this->confirmatorsGroupMapper->getConfirmatorGroupsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($confirmatorsGroups as $confirmatorsGroup) {
			$data[] = [
				'name' => $confirmatorsGroup->getName(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/confirmator-groups/delete?id='.$confirmatorsGroup->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/confirmator-groups/edit?id='.$confirmatorsGroup->getId().'">Edit</a>'
			];
		}

		$confirmatorsGroupsCount = $this->confirmatorsGroupMapper->getConfirmatorsGroupsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $confirmatorsGroupsCount,
			'recordsFiltered' => $confirmatorsGroupsCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/confirmator-groups/create')]
	public function create(): TemplateResponse {
		$confirmatorsGroup = new ConfirmatorsGroup();
		return new TemplateResponse(
			Application::APP_ID,
			'confirmator-groups/create',
			['page' => 'confirmator-groups', 'confirmatorsGroup' => $confirmatorsGroup]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/confirmator-groups/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$confirmatorsGroup = new ConfirmatorsGroup();
		$confirmatorsGroup->loadData($this->request->getParams());
		$confirmatorsGroup->setUserId($userId);
		$errors = $confirmatorsGroup->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'confirmator-groups/create',
				['page' => 'confirmator-groups', 'confirmatorsGroup' => $confirmatorsGroup, 'errors' => $errors]
			);
		}

		$this->confirmatorsGroupMapper->insert($confirmatorsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmator-groups');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/confirmator-groups/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$confirmatorsGroup = $this->confirmatorsGroupMapper->getConfirmatorGroupOfUser($id, $userId);

		$confirmatorsGroup->loadData($this->request->getParams());
		$confirmatorsGroup->setUserId($userId);
		$errors = $confirmatorsGroup->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'confirmator-groups/edit',
				['page' => 'confirmator-groups', 'confirmatorsGroup' => $confirmatorsGroup, 'errors' => $errors]
			);
		}

		$this->confirmatorsGroupMapper->update($confirmatorsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmator-groups');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmator-groups/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$confirmatorsGroup = $this->confirmatorsGroupMapper->getConfirmatorGroupOfUser($id, $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'confirmator-groups/edit',
			['page' => 'confirmator-groups', 'confirmatorsGroup' => $confirmatorsGroup]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/confirmator-groups/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->confirmatorsGroupMapper->deleteConfirmatorGroup($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/confirmator-groups');
	}


}
