<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\CheckupInterval;
use OCA\DeadManSwitch\Db\CheckupIntervalMapper;
use OCA\DeadManSwitch\Db\Job;
use OCA\DeadManSwitch\Db\JobMapper;
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

class CheckupIntervalController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $checkupIntervalMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		CheckupIntervalMapper $checkupIntervalMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->checkupIntervalMapper = $checkupIntervalMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/checkup-intervals')]
	public function jobs(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'checkup-intervals/checkup-intervals',
			['page' => 'checkup-intervals']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-checkup-intervals')]
	public function getCheckupIntervals(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$checkupIntervals = $this->checkupIntervalMapper->getCheckupIntervalsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($checkupIntervals as $checkupInterval) {
			$data[] = [
				'name' => $checkupInterval->getName(),
				'interval' => $checkupInterval->getInterval(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/checkup-intervals/delete?id='.$checkupInterval->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/checkup-intervals/edit?id='.$checkupInterval->getId().'">Edit</a>'
			];
		}

		$jobsCount = $this->checkupIntervalMapper->getCheckupIntervalsOfUserTotal($userId);


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
	#[FrontpageRoute(verb: 'GET', url: '/checkup-intervals/create')]
	public function create(): TemplateResponse {
		$checkupInterval = new CheckupInterval();
		return new TemplateResponse(
			Application::APP_ID,
			'checkup-intervals/create',
			['page' => 'checkup-intervals', 'checkupInterval' => $checkupInterval]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/checkup-intervals/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$checkupInterval = new CheckupInterval();
		$checkupInterval->loadData($this->request->getParams());
		$checkupInterval->setUserId($userId);
		$errors = $checkupInterval->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'checkup-intervals/create',
				['page' => 'checkup-intervals', 'checkupInterval' => $checkupInterval, 'errors' => $errors]
			);
		}

		$this->checkupIntervalMapper->insert($checkupInterval);
		return new RedirectResponse('/index.php/apps/deadmanswitch/checkup-intervals');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/checkup-intervals/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$job = $this->checkupIntervalMapper->getCheckupIntervalOfUser($id, $userId);

		$job->loadData($this->request->getParams());
		$job->setUserId($userId);
		$errors = $job->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'checkup-intervals/edit',
				['page' => 'checkup-intervals', 'job' => $job, 'errors' => $errors]
			);
		}

		$this->checkupIntervalMapper->update($job);
		return new RedirectResponse('/index.php/apps/deadmanswitch/checkup-intervals');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/checkup-intervals/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$checkupInterval = $this->checkupIntervalMapper->getCheckupIntervalOfUser($id, $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'checkup-intervals/edit',
			['page' => 'checkup-intervals', 'checkupInterval' => $checkupInterval]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/checkup-intervals/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->checkupIntervalMapper->deleteCheckupInterval($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/checkup-intervals');
	}


}
