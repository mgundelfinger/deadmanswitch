<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use DateTime;
use OCA\DeadManSwitch\Db\Job;
use OCA\DeadManSwitch\Db\JobMapper;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\PreConditionNotMetException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\Service\MailService;
use OCA\DeadManSwitch\AppInfo\Application;
use OCA\DeadManSwitch\Cron\CheckInTask;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\IUser;
use OCP\IUserSession;
use Symfony\Component\HttpFoundation\JsonResponse;

class JobController extends Controller {

	public const ACTIVE_CONFIG_KEY = 'active';



	/**
	 * @var IInitialState
	 */
	private $initialStateService;
	/**
	 * @var IConfig
	 */
	private $config;
	/**
	 * @var string|null
	 */
	private $userId;

	/**
	 * @var MailService
	 */
	private $mailService;

	/**
	 * @var CheckInController
	 */
	private $checkInController;

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $jobMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IInitialState $initialStateService,
		IConfig $config,
		?string $userId,
		MailService $mailService,
		CheckInController $checkInController,
		IUserSession $currentUser,
		JobMapper $jobMapper,
	) {
		parent::__construct($appName, $request);
		$this->initialStateService = $initialStateService;
		$this->config = $config;
		$this->userId = $userId;
		$this->mailService = $mailService;
		$this->checkInController = $checkInController;
		$this->currentUser = $currentUser->getUser();
		$this->jobMapper = $jobMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs')]
	public function jobs(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'jobs/jobs',
			['page' => 'jobs']
		);
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
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/jobs/delete?id='.$job->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/jobs/edit?id='.$job->getId().'">Edit</a>'
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
		return new TemplateResponse(
			Application::APP_ID,
			'jobs/create',
			['page' => 'jobs', 'job' => $job]
		);
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
			return new TemplateResponse(
				Application::APP_ID,
				'jobs/create',
				['page' => 'jobs', 'job' => $job, 'errors' => $errors]
			);
		}

		$this->jobMapper->insert($job);
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
			return new TemplateResponse(
				Application::APP_ID,
				'jobs/edit',
				['page' => 'jobs', 'job' => $job, 'errors' => $errors]
			);
		}

		$this->jobMapper->update($job);
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

		return new TemplateResponse(
			Application::APP_ID,
			'jobs/edit',
			['page' => 'jobs', 'job' => $job]
		);
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
