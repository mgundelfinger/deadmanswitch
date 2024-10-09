<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method string getFirstName()
 * @method void setFirstName(string $firstName)
 * @method string getLastName()
 * @method void setLastName(string $lastName)
 * @method string getEmail()
 * @method void setEmail(string $email)
 */
class Contact extends Entity implements \JsonSerializable {

    /** @var string */
	protected $userId;
	/** @var string */
	protected $firstName;
	/** @var string */
	protected $lastName;
	/** @var string */
	protected $email;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('first_name', 'string');
		$this->addType('last_name', 'string');
		$this->addType('email', 'string');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'email' => $this->email,
		];
	}
}