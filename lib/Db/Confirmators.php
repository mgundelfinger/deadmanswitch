<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method int getContactsGroupId()
 * @method void setContactsGroupId(int $contactsGroupId)
 */
class Confirmators extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var string */
	protected $userId;
	/** @var int */
	protected $contactsGroupId;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('contacts_group_id', 'integer');
	}

	public function rules() {
		return [];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'contacts_group_id' => $this->contactsGroupId,
		];
	}
}
