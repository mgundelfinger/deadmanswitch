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
 * @method int getInterval()
 * @method void setInterval(int $interval)
 * @method string getLastCheckup()
 * @method void setLastCheckup(string $lastCheckup)
 */
class AliveStatus extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var string */
	protected $userId;
	/** @var string */
	protected $status;
	/** @var int */
	protected $interval;
	/** @var string */
	protected $lastCheckup;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('status', 'integer');
		$this->addType('interval', 'integer');
		$this->addType('last_checkup', 'datetime_immutable');
	}

	public function getLastCheckupAsDate(): DateTimeImmutable {
		return new DateTimeImmutable($this->lastCheckup);
	}

	public function setLastCheckupAsDate(DateTimeImmutable $lastCheckup) {
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
			'status' => $this->status,
			'interval' => $this->interval,
			'last_checkup' => $this->lastCheckup,
		];
	}
}
