<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
namespace OCA\DeadManSwitch\Controller;

use OCA\DeadManSwitch\AppInfo\Application;
use OCA\DeadManSwitch\Db\AliveStatusMapper;
use OCA\DeadManSwitch\Db\ResetTokenMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\IUserManager;

class CheckInController extends Controller {

	private IUserManager $userManager;

	private AliveStatusMapper $aliveStatusMapper;

	private ResetTokenMapper $tokenMapper;

	public function __construct(
		string $appName,
		IRequest $request,
		IUserManager $userManager,
		AliveStatusMapper $aliveStatusMapper,
		ResetTokenMapper $tokenMapper,
	) {
		parent::__construct($appName, $request, $userManager, $aliveStatusMapper, $tokenMapper);
		$this->userManager = $userManager;
		$this->aliveStatusMapper = $aliveStatusMapper;
		$this->tokenMapper = $tokenMapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
    #[PublicPage]
	#[FrontpageRoute(verb: 'GET', url: '/reset/{token}')]
	public function reset(string $token): TemplateResponse {
		$resetToken = $this->tokenMapper->getResetTokenByToken($token);
        if ($resetToken != null) {
            $aliveStatus = $this->aliveStatusMapper->getAliveStatus($resetToken->getAliveStatusId());
            $user = $this->userManager->get($aliveStatus->getUserId());
            if ($aliveStatus->getStatus() == AliveStatusMapper::STATUS_PENDING) {
                $this->aliveStatusMapper->updateAliveStatus($aliveStatus->getId(), AliveStatusMapper::STATUS_ALIVE);
            }
            $this->tokenMapper->deleteResetTokensOfAliveStatus($aliveStatus->getId());

		    return new TemplateResponse(Application::APP_ID, 'checkin/reset', ['name' => $user->getDisplayName(),]);
        }

		return new TemplateResponse(Application::APP_ID, 'checkin/reset', ['name' => null,]);
	}

}
