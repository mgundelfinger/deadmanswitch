<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
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
use Symfony\Component\HttpFoundation\JsonResponse;

class AliveStatusController extends Controller {

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
	#[FrontpageRoute(verb: 'GET', url: '/alive-statuses')]
	public function jobs(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'alive-statuses/alive-statuses',
			['page' => 'alive-statuses']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-alive-statuses')]
	public function getAliveStatuses(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$aliveStatuses = $this->aliveStatusMapper->getAliveStatusesOfUser($userId, $limit, $offset);
		$data = [];
		foreach($aliveStatuses as $aliveStatus) {
			$data[] = [
				'name' => $aliveStatus->getName(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/alive-statuses/delete?id='.$aliveStatus->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/alive-statuses/edit?id='.$aliveStatus->getId().'">Edit</a>'
			];
		}

		$jobsCount = $this->aliveStatusMapper->getAliveStatusesOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $jobsCount,
			'recordsFiltered' => $jobsCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/alive-statuses/create')]
	public function create(): TemplateResponse {
		$aliveStatus = new AliveStatus();
		return new TemplateResponse(
			Application::APP_ID,
			'alive-statuses/create',
			['page' => 'alive-statuses', 'aliveStatus' => $aliveStatus]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/alive-statuses/store')]
	public function store(): Response {
		$aliveStatus = new AliveStatus();
		$aliveStatus->loadData($this->request->getParams());
		$errors = $aliveStatus->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'alive-statuses/create',
				['page' => 'alive-statuses', 'aliveStatus' => $aliveStatus, 'errors' => $errors]
			);
		}

		$this->aliveStatusMapper->insert($aliveStatus);
		return new RedirectResponse('/index.php/apps/deadmanswitch/alive-statuses');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/alive-statuses/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($id);

		$aliveStatus->loadData($this->request->getParams());
		$errors = $aliveStatus->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'alive-statuses/edit',
				['page' => 'alive-statuses', 'aliveStatus' => $aliveStatus, 'errors' => $errors]
			);
		}

		$this->aliveStatusMapper->update($aliveStatus);
		return new RedirectResponse('/index.php/apps/deadmanswitch/alive-statuses');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/alive-statuses/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');

		$aliveStatus = $this->aliveStatusMapper->getAliveStatusOfUser($id);

		return new TemplateResponse(
			Application::APP_ID,
			'alive-statuses/edit',
			['page' => 'alive-statuses', 'aliveStatus' => $aliveStatus]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/alive-statuses/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');

		$this->aliveStatusMapper->deleteAliveStatus($id);

		return new RedirectResponse('/index.php/apps/deadmanswitch/alive-statuses');
	}


}
