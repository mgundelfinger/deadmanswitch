<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

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
 * @method int getTriggerId()
 * @method void setTriggerId(int $triggerId)
 */
class Task extends Entity implements \JsonSerializable {

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
	protected $triggerId;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('active', 'boolean');
        $this->addType('contacts_group_id', 'integer');
        $this->addType('jobs_group_id', 'integer');
        $this->addType('confirmators_group_id', 'integer');
        $this->addType('trigger_id', 'integer');
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
            'trigger_id' => $this->triggerId,
		];
	}
}