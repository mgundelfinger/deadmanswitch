<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use DateTime;
use OCA\DeadManSwitch\Db\Job;
use OCA\DeadManSwitch\Db\JobMapper;
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
			'jobs',
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


		return new JsonResponse(array('headers' => 'kjhjkh'));
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs/create')]
	public function create(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'jobs/create',
			['page' => 'jobs']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/jobs/create')]
	public function store(): TemplateResponse {

		$job = new Job();


		return new TemplateResponse(
			Application::APP_ID,
			'jobs/create',
			['page' => 'jobs']
		);
	}


}
