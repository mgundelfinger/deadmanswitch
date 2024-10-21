<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCA\DeadManSwitch\AppInfo\Application;
use OCP\AppFramework\Services\IAppConfig;

class PageController extends Controller {

	/**
	 * @var IAppConfig
	 */
	private $config;

	public function __construct(
		string $appName,
		IRequest $request,
		IAppConfig $config,
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function mainPage(): TemplateResponse {
		$appVersion = $this->config->getAppValueString('installed_version');
		return new TemplateResponse(
			Application::APP_ID,
			'myMainTemplate',
			[
				'app_version' => $appVersion,
			]
		);
	}
}
