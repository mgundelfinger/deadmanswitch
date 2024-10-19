<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\Db\ContactMapper;
use OCA\DeadManSwitch\Db\Contact;
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

class ContactController extends Controller {

	/**
	 * @var IUser
	 */
	private $currentUser;

	private $contactMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserSession $currentUser,
		ContactMapper $contactMapper,
	) {
		parent::__construct($appName, $request);
		$this->currentUser = $currentUser->getUser();
		$this->contactMapper = $contactMapper;
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contacts')]
	public function contacts(): TemplateResponse {
		return new TemplateResponse(
			Application::APP_ID,
			'contacts/contacts',
			['page' => 'contacts']
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return JsonResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/get-contacts')]
	public function getContacts(): JsonResponse {
		$userId = $this->currentUser->getUID();

		$limit = $this->request->getParam('length');
		$offset = $this->request->getParam('start');
		$draw = $this->request->getParam('draw');

		$contacts = $this->contactMapper->getContactsOfUser($userId, $limit, $offset);
		$data = [];
		foreach($contacts as $contact) {
			$data[] = [
				'firstName' => $contact->getFirstName(),
				'lastName' => $contact->getLastName(),
				'email' => $contact->getEmail(),
				'actions' => '<a class="confirm-action" href="/index.php/apps/deadmanswitch/contacts/delete?id='.$contact->getId().'">Delete</a>
					<a href="/index.php/apps/deadmanswitch/contacts/edit?id='.$contact->getId().'">Edit</a>'
			];
		}

		$contactsCount = $this->contactMapper->getContactsOfUserTotal($userId);


		$data = json_encode([
			'draw' => $draw,
			'recordsTotal' => $contactsCount,
			'recordsFiltered' => $contactsCount,
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
	#[FrontpageRoute(verb: 'GET', url: '/contacts/create')]
	public function create(): TemplateResponse {
		$contact = new Contact();
		return new TemplateResponse(
			Application::APP_ID,
			'contacts/create',
			['page' => 'contacts', 'contact' => $contact]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/contacts/store')]
	public function store(): Response {
		$userId = $this->currentUser->getUID();
		$contact = new Contact();
		$contact->loadData($this->request->getParams());
		$contact->setUserId($userId);
		$errors = $contact->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'contacts/create',
				['page' => 'contacts', 'contact' => $contact, 'errors' => $errors]
			);
		}

		$this->contactMapper->insert($contact);
		return new RedirectResponse('/index.php/apps/deadmanswitch/contacts');
	}



	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/contacts/update')]
	public function update(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();
		$contact = $this->contactMapper->getContactOfUser($id, $userId);

		$contact->loadData($this->request->getParams());
		$contact->setUserId($userId);
		$errors = $contact->validate();

		if($errors) {
			return new TemplateResponse(
				Application::APP_ID,
				'contacts/edit',
				['page' => 'contacts', 'contact' => $contact, 'errors' => $errors]
			);
		}

		$this->contactMapper->update($contact);
		return new RedirectResponse('/index.php/apps/deadmanswitch/contacts');
	}


	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contacts/edit')]
	public function edit(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$contact = $this->contactMapper->getContactOfUser($id, $userId);

		return new TemplateResponse(
			Application::APP_ID,
			'contacts/edit',
			['page' => 'contacts', 'contact' => $contact]
		);
	}

	/**
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/contacts/delete')]
	public function delete(): Response {
		$id = $this->request->getParam('id');
		$userId = $this->currentUser->getUID();

		$this->contactMapper->deleteContact($id, $userId);

		return new RedirectResponse('/index.php/apps/deadmanswitch/contacts');
	}


}
