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
class Job extends Entity implements \JsonSerializable {

    /** @var string */
	protected $userId;
	/** @var string */
	protected $name;
	/** @var string */
	protected $emailSubject;
	/** @var string */
	protected $emailBody;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('email_subject', 'string');
		$this->addType('email_body', 'string');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'name' => $this->name,
			'email_subject' => $this->emailSubject,
			'email_body' => $this->emailBody,
		];
	}
}