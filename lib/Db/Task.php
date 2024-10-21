<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use DateTimeImmutable;
use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method string getName()
 * @method void setName(string $name)
 * @method bool getActive()
 * @method void setActive(bool $active)
 * @method int getContactsGroupId()
 * @method void setContactsGroupId(int $contactsGroupId)
 * @method int getJobsGroupId()
 * @method void setJobsGroupId(int $jobsGroupId)
 * @method int getConfirmatorsGroupId()
 * @method void setConfirmatorsGroupId(int $confirmatorsGroupId)
 * @method int getIntervalId()
 * @method void setIntervalId(int $intervalId)
 * @method DateTimeImmutable getLastCheckup()
 * @method void setLastCheckup(DateTimeImmutable $lastCheckup)
 * @method int getTriggerId()
 * @method void setTriggerId(int $triggerId)
 */
class Task extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var string */
	protected $userId;
	/** @var string */
	protected $name;
	/** @var bool */
	protected $active;
    /** @var int */
	protected $contactsGroupId;
    /** @var int */
	protected $jobsGroupId;
    /** @var int */
	protected $confirmatorsGroupId;
	/** @var int */
	protected $intervalId;
	/** @var string */
	protected $lastCheckup;
    /** @var int */
	protected $triggerId;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('active', 'boolean');
        $this->addType('contacts_group_id', 'integer');
        $this->addType('jobs_group_id', 'integer');
        $this->addType('confirmators_group_id', 'integer');
		$this->addType('interval_id', 'integer');
		$this->addType('last_checkup', 'datetime_immutable');
        $this->addType('trigger_id', 'integer');
	}

	public function getLastCheckup(): DateTimeImmutable {
		return new DateTimeImmutable($this->lastCheckup);
	}

	public function setLastCheckup(DateTimeImmutable $lastCheckup) {
		$this->lastCheckup = new DateTimeImmutable($lastCheckup->format('Y-m-d'));
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
			'name' => $this->name,
			'active' => $this->active,
            'contacts_group_id' => $this->contactsGroupId,
            'jobs_group_id' => $this->jobsGroupId,
            'confirmators_group_id' => $this->confirmatorsGroupId,
			'interval_id' => $this->intervalId,
			'last_checkup' => $this->lastCheckup,
            'trigger_id' => $this->triggerId,
		];
	}
}
