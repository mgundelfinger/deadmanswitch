<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use DateTimeImmutable;
use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $name)
 * @method int getStatus()
 * @method void setStatus(int $status)
 * @method int getContactsGroupId()
 * @method void setContactsGroupId(int $contactsGroupId)
 * @method int getAliveDays()
 * @method void setAliveDays(int $aliveDays)
 * @method int getPendingDays()
 * @method void setPendingDays(int $pendingDays)
 * @method string getLastChange()
 * @method void setLastChange(string $lastChange)
 */
class AliveStatus extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var string */
	protected $userId;
	/** @var string */
	protected $status;
	/** @var int */
	protected $contactsGroupId;
	/** @var int */
	protected $aliveDays;
	/** @var int */
	protected $pendingDays;
	/** @var string */
	protected $lastChange;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('status', 'integer');
		$this->addType('contacts_group_id', 'integer');
		$this->addType('alive_days', 'integer');
		$this->addType('pending_days', 'integer');
		$this->addType('last_change', 'string');
	}

	public function getLastChangeAsDate(): DateTimeImmutable {
		return new DateTimeImmutable($this->lastChange);
	}

	public function setLastChangeAsDate(DateTimeImmutable $lastChange) {
		$this->setLastChange($lastChange->format('Y-m-d'));
		// $this->lastChange = $lastChange->format('Y-m-d');
	}

	public function rules() {
		return [
			'name' => [
				'min' => 3,
				'max' => 64
			],
		];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'status' => $this->status,
			'contacts_group_id' => $this->contactsGroupId,
			'alive_days' => $this->aliveDays,
			'pending_days' => $this->pendingDays,
			'last_change' => $this->lastChange,
		];
	}
}
