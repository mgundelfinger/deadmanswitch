<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\JobsGroup;
use OCA\DeadManSwitch\Db\JobsGroupMapper;
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

class JobGroupsController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $jobsGroupMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		JobsGroupMapper $jobsGroupMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->jobsGroupMapper = $jobsGroupMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/job-groups')]
	public function jobGroups(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'job-groups/job-groups',
			['page' => 'job-groups']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-job-groups')]
	public function getJobGroups(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$jobsGroups = $this->jobsGroupMapper->getJobGroupsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($jobsGroups as $jobsGroup) {
			$data[] = [
				'name' => $jobsGroup->getName(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/job-groups/delete?id='.$jobsGroup->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/job-groups/edit?id='.$jobsGroup->getId().'">Edit</a>'
			];
		}

		$jobsGroupsCount = $this->jobsGroupMapper->getJobsGroupsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $jobsGroupsCount,
			'recordsFiltered' => $jobsGroupsCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/job-groups/create')]
	public function create(): TemplateResponse {
		$jobsGroup = new JobsGroup();
		return new TemplateResponse(
			Application::APP_ID,
			'job-groups/create',
			['page' => 'job-groups', 'jobsGroup' => $jobsGroup]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/job-groups/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$jobsGroup = new JobsGroup();
		$jobsGroup->loadData($this->request->getParams());
		$jobsGroup->setUserId($userId);
		$errors = $jobsGroup->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'job-groups/create',
				['page' => 'job-groups', 'jobsGroup' => $jobsGroup, 'errors' => $errors]
			);
		}

		$this->jobsGroupMapper->insert($jobsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/job-groups');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/job-groups/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$jobsGroup = $this->jobsGroupMapper->getJobGroupOfUser($id, $userId);

		$jobsGroup->loadData($this->request->getParams());
		$jobsGroup->setUserId($userId);
		$errors = $jobsGroup->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'job-groups/edit',
				['page' => 'job-groups', 'jobsGroup' => $jobsGroup, 'errors' => $errors]
			);
		}

		$this->jobsGroupMapper->update($jobsGroup);
		return new RedirectResponse('/index.php/apps/deadmanswitch/job-groups');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/job-groups/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$jobsGroup = $this->jobsGroupMapper->getJobGroupOfUser($id, $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'job-groups/edit',
			['page' => 'job-groups', 'jobsGroup' => $jobsGroup]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/job-groups/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->jobsGroupMapper->deleteJobGroup($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/job-groups');
	}


}
