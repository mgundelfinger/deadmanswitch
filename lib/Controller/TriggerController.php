<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\Job;
use OCA\DeadManSwitch\Db\Trigger;
use OCA\DeadManSwitch\Db\TriggerMapper;
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

class TriggerController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $triggerMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		TriggerMapper $triggerMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->triggerMapper = $triggerMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/triggers')]
	public function jobs(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'triggers/triggers',
			['page' => 'triggers']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-triggers')]
	public function getTriggers(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$triggers = $this->triggerMapper->getTriggersOfUser($userId, $limit, $offset);
		$data = [];
		foreach($triggers as $trigger) {
			$data[] = [
				'name' => $trigger->getName(),
				'delay' => $trigger->getDelay(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/triggers/delete?id='.$trigger->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/triggers/edit?id='.$trigger->getId().'">Edit</a>'
			];
		}

		$jobsCount = $this->triggerMapper->getTriggersOfUserTotal($userId);


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
	#[FrontpageRoute(verb: 'GET', url: '/triggers/create')]
	public function create(): TemplateResponse {
		$trigger = new Trigger();
		return new TemplateResponse(
			Application::APP_ID,
			'triggers/create',
			['page' => 'triggers', 'trigger' => $trigger]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/triggers/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$trigger = new Trigger();
		$trigger->loadData($this->request->getParams());
		$trigger->setUserId($userId);
		$errors = $trigger->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'triggers/create',
				['page' => 'triggers', 'trigger' => $trigger, 'errors' => $errors]
			);
		}

		$this->triggerMapper->insert($trigger);
		return new RedirectResponse('/index.php/apps/deadmanswitch/jobs');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/triggers/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$trigger = $this->triggerMapper->getTriggerOfUser($id, $userId);

		$trigger->loadData($this->request->getParams());
		$trigger->setUserId($userId);
		$errors = $trigger->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'triggers/edit',
				['page' => 'triggers', 'trigger' => $trigger, 'errors' => $errors]
			);
		}

		$this->triggerMapper->update($trigger);
		return new RedirectResponse('/index.php/apps/deadmanswitch/triggers');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/triggers/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$trigger = $this->triggerMapper->getTriggerOfUser($id, $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'triggers/edit',
			['page' => 'triggers', 'trigger' => $trigger]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/triggers/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->triggerMapper->deleteTrigger($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/triggers');
	}


}
