<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method int getContactId()
 * @method void setContactId(int $contactId)
 * @method int getAliveStatusId()
 * @method void setAliveStatusId(int $aliveStatusId)
 * @method string getToken()
 * @method void setToken(string $token)
 */
class ResetToken extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var int */
	protected $contactId;
    /** @var int */
	protected $aliveStatusId;
	/** @var string */
	protected $token;

	public function __construct() {
		$this->addType('contact_id', 'integer');
        $this->addType('alive_status_id', 'integer');
		$this->addType('token', 'string');
	}

	public function rules() {
		return [];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'contact_id' => $this->contactId,
            'alive_status_id' => $this->aliveStatusId,
			'token' => $this->token,
		];
	}
}
