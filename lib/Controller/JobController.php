<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\ContactsGroupMapper;
use OCA\DeadManSwitch\Db\Job;
use OCA\DeadManSwitch\Db\JobMapper;
use OCA\DeadManSwitch\Db\JobsGroupMapper;
use OCA\DeadManSwitch\Db\UserSettingsMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\AppInfo\Application;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IL10N;
use OCP\IUser;
use OCP\IUserSession;
use Symfony\Component\HttpFoundation\JsonResponse;

class JobController extends BasicController {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $jobMapper;

	/**
	 * @var JobsGroupMapper
	 */
	private $jobsGroupMapper;

	private IL10N $l;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		JobMapper $jobMapper,
		JobsGroupMapper $jobsGroupMapper,
		UserSettingsMapper $userSettingsMapper,
		IL10N $l,
	) {
		parent::__construct($appName, $request, $currentUser, $userSettingsMapper);
		$this->currentUser = $currentUser->getUser();
		$this->jobMapper = $jobMapper;
		$this->jobsGroupMapper = $jobsGroupMapper;
		$this->l = $l;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs')]
	public function jobs(): TemplateResponse {
		return $this->getTemplate('jobs/jobs', ['page' => 'jobs']);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-jobs')]
	public function getJobs(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$jobs = $this->jobMapper->getJobsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($jobs as $job) {
			$data[] = [
				'name' => $job->getName(),
				'emailSubject' => $job->getEmailSubject(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/jobs/delete?id='.$job->getId().'">' . $this->l->t("Delete") . '</a>
					<a href="/index.php/apps/deadmanswitch/jobs/edit?id='.$job->getId().'">' . $this->l->t("Edit") . '</a>'
			];
		}

		$jobsCount = $this->jobMapper->getJobsOfUserTotal($userId);


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
	#[FrontpageRoute(verb: 'GET', url: '/jobs/create')]
	public function create(): TemplateResponse {
		$job = new Job();
		$userId = $this->currentUser->getUID();
		$currentGroups = $this->jobMapper->getGroups($job);
		$groupsList = $this->jobsGroupMapper->getList($userId);

		return $this->getTemplate('jobs/create', ['page' => 'jobs', 'job' => $job, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups]);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/jobs/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$job = new Job();
		$job->loadData($this->request->getParams());
		$job->setUserId($userId);
		$errors = $job->validate();

		if($errors) {
			$groupsList = $this->jobsGroupMapper->getList($userId);
			$currentGroups = $this->jobMapper->getGroups($job);

			return $this->getTemplate('jobs/create', [
				'page' => 'jobs', 'job' => $job, 'errors' => $errors, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups
			]);
		}

		$this->jobMapper->insert($job);
		$groupsIds = (array) $this->request->getParam('jobGroups');
		$groups = $this->jobsGroupMapper->getGroups($userId, $groupsIds);
		$this->jobsGroupMapper->updateGroups($job, $groups);
		return new RedirectResponse('/index.php/apps/deadmanswitch/jobs');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/jobs/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$job = $this->jobMapper->getJobOfUser($id, $userId);

		$job->loadData($this->request->getParams());
		$job->setUserId($userId);
		$errors = $job->validate();

		if($errors) {
			$groupsList = $this->jobsGroupMapper->getList($userId);
			$currentGroups = $this->jobMapper->getGroups($job);

			return $this->getTemplate('jobs/edit', [
				'page' => 'jobs', 'job' => $job, 'errors' => $errors, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups
			]);

		}

		if($job->isModified()) {
			$this->jobMapper->update($job);
		}
		$groupsIds = (array) $this->request->getParam('contactGroups');
		$groups = $this->jobsGroupMapper->getGroups($userId, $groupsIds);
		$this->jobsGroupMapper->updateGroups($job, $groups);
		return new RedirectResponse('/index.php/apps/deadmanswitch/jobs');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$job = $this->jobMapper->getJobOfUser($id, $userId);
		$groupsList = $this->jobsGroupMapper->getList($userId);
		$currentGroups = $this->jobMapper->getGroups($job);

		return $this->getTemplate('jobs/edit', ['page' => 'jobs', 'job' => $job, 'groupsList' => $groupsList, 'currentGroups' => $currentGroups]);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->jobMapper->deleteJob($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/jobs');
	}


}
