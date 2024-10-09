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
class AliveStatus extends Entity implements \JsonSerializable {

	/** @var string */
	protected $name;

	public function __construct() {
		$this->addType('name', 'string');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}