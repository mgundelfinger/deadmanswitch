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
 * @method string getContent()
 * @method void setContent(string $content)
 * @method int getLastModified()
 * @method void setLastModified(int $lastModified)
 */
class CheckupInterval extends Entity implements \JsonSerializable {

    /** @var string */
	protected $userId;
	/** @var string */
	protected $name;
	/** @var int */
	protected $interval;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('interval', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'name' => $this->name,
			'interval' => $this->interval,
		];
	}
}