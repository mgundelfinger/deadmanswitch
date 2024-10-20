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
 * @method string getName()
 * @method void setName(string $name)
 */
class ContactsGroup extends Entity implements \JsonSerializable {

	use ValidationTrait;

    /** @var string */
	protected $userId;
	/** @var string */
	protected $name;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
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
		];
	}
}
