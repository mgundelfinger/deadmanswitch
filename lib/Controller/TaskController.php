<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\ContactsGroupMapper;
use OCA\DeadManSwitch\Db\JobsGroupMapper;
use OCA\DeadManSwitch\Db\Task;
use OCA\DeadManSwitch\Db\TaskMapper;
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

class TaskController extends Controller {

	private IUser $currentUser;

	private TaskMapper $taskMapper;

	private ContactsGroupMapper $contactsGroupMapper;

	private JobsGroupMapper $jobsGroupMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		TaskMapper $taskMapper,
		ContactsGroupMapper $contactsGroupMapper,
		JobsGroupMapper $jobsGroupMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->taskMapper = $taskMapper;
		$this->contactsGroupMapper = $contactsGroupMapper;
		$this->jobsGroupMapper = $jobsGroupMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/tasks')]
	public function tasks(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'tasks/tasks',
			['page' => 'tasks']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-tasks')]
	public function getTasks(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$tasks = $this->taskMapper->getTasksOfUser($userId, $limit, $offset);
		$data = [];
		foreach($tasks as $task) {
			$data[] = [
				'name' => $task->getName(),
				'active' => $task->getActive(),
				'contactGroup' => $task->getContactsGroupId(),
				'jobGroup' => $task->getJobsGroupId(),
				'deathDays' => $task->getDeathDays(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/tasks/delete?id='.$task->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/tasks/edit?id='.$task->getId().'">Edit</a>'
			];
		}

		$tasksCount = $this->taskMapper->getTasksOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $tasksCount,
			'recordsFiltered' => $tasksCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/tasks/create')]
	public function create(): TemplateResponse {
		$task = new Task();
		$userId = $this->currentUser->getUID();

		$contactGroups = $this->contactsGroupMapper->getList($userId);
		$jobGroups = $this->jobsGroupMapper->getList($userId);

		return new TemplateResponse(
			Application::APP_ID,
			'tasks/create',
			[
				'page' => 'tasks', 'task' => $task, 'contactGroups' => $contactGroups, 'jobGroups' => $jobGroups,
			]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/tasks/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$task = new Task();
		$task->loadData($this->request->getParams());
		$task->setUserId($userId);
		$errors = $task->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'tasks/create',
				['page' => 'tasks', 'task' => $task, 'errors' => $errors]
			);
		}

		$this->taskMapper->createTask($userId, $task->getName(), $task->getContactsGroupId(), $task->getJobsGroupId(), $task->getDeathDays(), $task->getActive());

		// $this->taskMapper->insert($task);

		return new RedirectResponse('/index.php/apps/deadmanswitch/tasks');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/tasks/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$task = $this->taskMapper->getTaskOfUser($id, $userId);

		$task->loadData($this->request->getParams());
		$task->setUserId($userId);
		$errors = $task->validate();

		if($errors) {
			$contactGroups = $this->contactsGroupMapper->getList($userId);
			$jobGroups = $this->jobsGroupMapper->getList($userId);
			return new TemplateResponse(
				Application::APP_ID,
				'tasks/edit',
				[
					'page' => 'tasks', 'task' => $task, 'errors' => $errors, 'contactGroups' => $contactGroups,
					'jobGroups' => $jobGroups,
				]
			);
		}

		if($task->isModified()) {
			$this->taskMapper->update($task);
		}

		return new RedirectResponse('/index.php/apps/deadmanswitch/tasks');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/tasks/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$task = $this->taskMapper->getTaskOfUser($id, $userId);

		$contactGroups = $this->contactsGroupMapper->getList($userId);
		$jobGroups = $this->jobsGroupMapper->getList($userId);

		return new TemplateResponse(
			Application::APP_ID,
			'tasks/edit',
			[
				'page' => 'tasks', 'task' => $task, 'contactGroups' => $contactGroups, 'jobGroups' => $jobGroups,
				]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/tasks/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->taskMapper->deleteTask($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/tasks');
	}


}
